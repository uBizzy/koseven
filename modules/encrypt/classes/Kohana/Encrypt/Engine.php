<?php

/**
 * Class Kohana_Encrypt_Engine
 *
 * @package    Kohana
 * @category   Security
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
abstract class Kohana_Encrypt_Engine
{
    /**
     * @var string Encryption key
     */
    protected $_key;

    /**
     * @var string mcrypt mode
     */
    protected $_mode;

    /**
     * @var string cipher
     */
    protected $_cipher;

    /**
     * Creates a new Encrypt object.
     *
     * @param array $config
     * @throws Kohana_Exception
     */
    public function __construct(array $config)
    {
        if (isset($config['key']))
        {
            $this->_key = $config['key'];
        }
        else
        {
            // No default encryption key is provided!
            throw new Kohana_Exception('No encryption key is defined in the encryption configuration');
        }
    }

    /**
     * Encrypts the message
     * @param String $message Your message to be encrypted.
     * @param String $iv
     * @return null|string
     */
    abstract public function encrypt(String $message, String $iv): ?string;

    /**
     * Decrypts the ciphertext
     * @param String $ciphertext Your ciphertext to be decrypted.
     * @return null|string
     * @internal param String $iv
     * @internal param String $data
     */
    abstract public function decrypt(String $ciphertext): ?string;

    /**
     * Creates random IV (Initialization vector) for each encryption action.
     * @return string
     */
    abstract public function create_iv(): string;
}
