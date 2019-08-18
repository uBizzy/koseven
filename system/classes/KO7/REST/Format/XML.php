<?php
/**
 * Class for formatting REST-Response bodies as XML
 *
 * @package        KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE
 */
class KO7_REST_Format_XML extends REST_Format {

    /**
     * Format function
     *
     * @param array $body Body to format
     *
     * @throws REST_Exception
     *
     * @return string
     */
    public function format(array $body) : string
    {
        // Check if php-xml is loaded
        if ( ! extension_loaded('xml'))
        {
            throw new REST_Exception('PHP XML Module not loaded.');
        }

        // Create new XML Element
        $xml = $this->array_to_xml($body);

        // Check if xml is valid
        if ( ! $xml)
        {
            throw new REST_Exception($this->evaluate_error());
        }

        return $xml;
    }

    /**
     * Convert Array to an xml element
     *
     * @param array $array        Array to convert
     * @param mixed $rootElement  Root element of entry
     * @param null $xml           Current XML stack
     *
     * @return mixed
     */
    private function array_to_xml(array $array, $rootElement = NULL, $xml = NULL)
    {
        // If there is no Root Element then insert root
        if ($xml === NULL)
        {
            $xml = new SimpleXMLElement($rootElement ?? '<?xml version="1.0" encoding="'.KO7::$charset.'"?><root/>', LIBXML_COMPACT);
        }

        // Visit all key value pair
        foreach ($array as $k => $v)
        {
            // If there is nested array then
            if (is_array($v))
            {
                // Call function for nested array
                $this->array_to_xml($v, $k, $xml->addChild($k));
            }
            else
            {
                // Simply add child element.
                $xml->addChild($k, $v);
            }
        }

        return $xml->asXML();
    }

    /**
     * Evaluate the XML Error and create a error message
     *
     * @return string
     */
    private function evaluate_error() : string
    {
        $error_message = 'Unknown XML Error';
        foreach (libxml_get_errors() as $error)
        {
            if ($error instanceof LibXMLError)
            {
                switch ($error->level)
                {
                    case LIBXML_ERR_WARNING :
                        $error_message .= 'Warning '.$error->code.': ';
                        break;
                    case LIBXML_ERR_ERROR :
                        $error_message .= 'Error '.$error->code.': ';
                        break;
                    case LIBXML_ERR_FATAL :
                        $error_message .= 'Fatal '.$error->code.': ';
                        break;
                }

                $error_message .= trim($error->message)."\n  Line: $error->line"."\n  Column: $error->column";

                if ($error->file)
                {
                    $error_message .= "\n  File: $error->file";
                }
            }
        }

        return $error_message;
    }

}

