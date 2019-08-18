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
        $xml = new SimpleXMLElement('<root/>');

        // Add Child foreach body element
        $xml = $xml->addChild('data');
        array_walk_recursive($data, static function($value, $key) use ($xml, &$result)
        {
            $result = $xml->addChild($key, $value);
        });

        return $xml->asXML();
    }

}

