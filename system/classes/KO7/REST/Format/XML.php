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
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="'.KO7::$charset.'"?><root/>', LIBXML_COMPACT);

        // Add Child foreach body element
        $xml = $xml->addChild('data');
        array_walk_recursive($data, static function($value, $key) use ($xml, &$result)
        {
            $result = $xml->addChild($key, $value);
        });

        $xml = $xml->asXML();

        if ( ! $xml)
        {
            throw new REST_Exception($this->evaluate_error());
        }

        return $xml;
    }

    /**
     * Evaluate the XML Error and create a error message
     *
     * @return string
     */
    private function evaluate_error() : string
    {
        $error_message = 'Unknown Error XmlException';
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

