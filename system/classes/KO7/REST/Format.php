<?php
/**
 * Interface KO7_REST_Format
 * This interface needs to be extended from every REST-Formatter
 *
 * The following formatter come shipped with Koseven:
 *  - JSON
 *  - XML
 *  - HTML
 *
 * @package KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license    https://koseven.ga/LICENSE
 */
abstract class KO7_REST_Format {

    /**
     * Holds an instance of the request class
     * @var Request
     */
    protected $_request;

    /**
     * Factory Method for REST Formatter
     *
     * @param string  $format   Format to use (e.g XML, JSON, XML, etc..)
     * @param Request $request  Request Class
     *
     * @throws REST_Exception
     *
     * @return REST_Format
     */
    public static function factory(string $format, Request $request) : REST_Format
    {
        $formatter = 'REST_Format_' . $format;

        // Check if formatter Exists
        if ( ! class_exists($formatter))
        {
            throw new REST_Exception('Formatter :formatter does not exist.', [
                ':formatter' => get_class($formatter)
            ]);
        }

        $formatter = new $formatter($request);

        // Check if client extends Request_Client_External
        if ( ! $formatter instanceof REST_Format)
        {
            throw new REST_Exception(':formatter is not a valid REST formatter.', [
                ':formatter' => get_class($formatter)
            ]);
        }

        return $formatter;
    }

    /**
     * KO7_REST_Format constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Function for formatting the body
     *
     * @param  array $body Body to Format
     *
     * @return string
     */
    abstract public function format(array $body) : string;

}