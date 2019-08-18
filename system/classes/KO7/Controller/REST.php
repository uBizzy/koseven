<?php
/**
 * Abstract Controller class for REST controller mapping.
 * Supports GET, PUT, POST, and DELETE.
 *
 * @package        KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE
 */
abstract class KO7_Controller_REST extends Controller {

    /**
     * Default Output (only if allowed by configuration)
     *
     * @var string
     */
    public static $default_output = 'json';

    /**
     * REST types
     *
     * @var array
     */
    protected static $_action_map = [
        HTTP_Request::GET => 'index',
        HTTP_Request::PUT => 'update',
        HTTP_Request::POST => 'create',
        HTTP_Request::DELETE => 'delete'
    ];

    /**
     * Automatically executed before the controller action.
     * Evaluate Request (method, action, parameter, format)
     */
    public function before() : void
    {
        // Parent call
        parent::before();

        // Inject output format if not set by route
        $this->request->inject_param('format', $this->request->param('format') ?: static::$default_output);

        // Determine the request action from the request method, if the action/method is not allowed throw error
        // We need to do this because this module does not support all HTTP_Requests
        if ( ! isset(static::$_action_map[$this->request->method()]))
        {
            $this->response
                ->status(405)
                ->headers('Allow', implode(', ', array_keys(static::$_action_map)));
        }
        else
        {
            $this->request->action(static::$_action_map[$this->request->method()]);
        }
    }

    /**
     * Automatically executed after the controller action.
     *
     * - Adds cache and content header(s).
     * - Formats body with given formatting method
     * - Adds attachment header if necessary
     *
     * @throws REST_Exception
     * @throws HTTP_Exception
     */
    public function after() : void
    {
        // Parent call
        parent::after();

        // Try initializing the formatter
        try
        {
            $formatter = REST_Format::factory($this->request, $this->response);
        }
        catch (REST_Exception $e)
        {
            throw HTTP_Exception::factory(500, $e->getMessage(), NULL, $e);
        }

        // No cache / must-revalidate cache if method is not GET
        if ($this->request->method() !== HTTP_Request::GET)
        {
            $this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');
        }

        // Format body
        $this->response->body($formatter->format());

        // Support attachment header
        $params = $this->_parse_params();
        if (isset($params['attachment']))
        {
            try
            {
                $this->response->send_file(TRUE, $params['attachment'].'.'.($this->request->param('format') ?: static::$default_output));
            }
            catch (KO7_Exception $e)
            {
                throw new REST_Exception($e->getMessage(), NULL, $e->getCode(), $e);
            }
        }
    }

    /**
     * Initializes the request params array based on the current request.
     *
     * @return array
     */
    protected function _parse_params() : array
    {
        // If method is GET, fetch params from query
        if ($this->request->method() === HTTP_Request::GET)
        {
            return $this->request->query();
        }

        // Otherwise we have a PUT, POST, DELETE Method
        // If content_type is JSON we need to decode the body first
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== FALSE)
        {
            $parsed_body = json_decode($this->request->body(), TRUE);
        }
        else
        {
            parse_str($this->request->body(), $parsed_body);
        }

        return array_merge((array)$parsed_body, (array)$this->request->post());
    }

}