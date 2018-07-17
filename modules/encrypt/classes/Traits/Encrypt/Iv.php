<?php

/**
 * Trait Traits_Encrypt_Iv
 *
 * @package    Kohana
 * @category   Security
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
trait Traits_Encrypt_Iv
{
    /**
     * Proxy for the random_bytes function - to allow mocking and testing against KAT vectors
     * @return string the initialization vector or FALSE on error
     * @throws Kohana_Exception
     */
    public function create_iv(): string
    {
        if (function_exists('random_bytes'))
        {
            return random_bytes($this->_iv_size);
        }

        throw new Kohana_Exception('Could not create initialization vector.');
    }
}