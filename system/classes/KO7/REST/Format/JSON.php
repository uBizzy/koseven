<?php
/**
 * Class for formatting REST-Response bodies as JSON
 *
 * @package        KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license        https://koseven.ga/LICENSE
 */
class KO7_REST_Format_JSON extends REST_Format {

    /**
     * Format function
     *
     * @throws REST_Exception
     *
     * @return string
     */
    public function format() : string
    {
        try
        {
            $body = json_encode($this->_body,
                JSON_THROW_ON_ERROR
                |JSON_FORCE_OBJECT
                |JSON_NUMERIC_CHECK
                |JSON_PRESERVE_ZERO_FRACTION
                |JSON_UNESCAPED_UNICODE
            );
        }
        catch(Exception $e)
        {
            $message = 'Unknown JSON error.';

            switch(json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $message = 'Maximum JSON stack depth has been exceeded.';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $message = 'JSON is not properly formed or invalid.';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $message = 'Error in JSON control characters. This usually happens with incorrect encoding.';
                    break;
                case JSON_ERROR_SYNTAX:
                    $message = 'Syntax error - Invalid JSON.';
                    break;
                case JSON_ERROR_UTF8:
                    $message = 'Malformed JSON UTF-8 characters. This usually happens with incorrect encoding.';
                    break;
            }
            throw new REST_Exception($message, NULL, $e->getCode(), $e);
        }

        return $body;
    }

}
