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
     * @return string
     */
    public function format() : string
    {
        return json_encode($this->_body);
    }

}
