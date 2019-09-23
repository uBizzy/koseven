<?php
/**
 * Class for formatting REST-Response bodies as HTML
 *
 * @package        KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE
 */
class KO7_REST_Format_HTML extends REST_Format {

    /**
     * Format function
     *
     * @throws REST_Exception
     *
     * @return string
     */
    public function format() : string
    {
        // Parse Request Directory
        $directory = strtolower($this->_request->directory());

        // Prepend a directory separator if a directory is set
        if ($directory)
        {
            $directory .= DIRECTORY_SEPARATOR;
        }

        // Determine the view name
        $view_name = $directory.strtolower($this->_request->controller());

        // Try to find view, change view name if not found
        // e.g Controller: Welcome, check views/Welcome.php else views/welcome/{index,update,create,delete}.php
        if (KO7::find_file('views', $view_name) === FALSE)
        {
            $view_name .= DIRECTORY_SEPARATOR.$this->_request->action();
        }

        // Try to initialize View
        try
        {
            return View::factory($view_name,  $this->_body)->render();
        }
        catch (View_Exception $e)
        {
            throw new REST_Exception($e->getMessage());
        }
    }

}
