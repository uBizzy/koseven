<?php
/**
 * Class for formatting REST-Response bodies as JSON
 *
 * @package KO7\REST
 *
 * @copyright  (c) since 2016 Koseven Team
 * @license    https://koseven.ga/LICENSE
 */
class KO7_REST_Format_JSON extends REST_Format {

    /**
     * Format function
     *
     * @param array $body Body to format
     *
     * @return string
     */
    public function format(array $body): string
    {
        return json_encode($body);
    }

}
