<?php

/**
 * @package    Kohana
 * @category   Security
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_Encrypt {

    /**
     * @var string Name of default config instance
     */
    public static $default = 'default';

    /**
     * @var array Encrypt class instances
     */
    public static $instances = [];

    /**
     * @var  Kohana_Encrypt_Engine Encryption engine
     */
    public $_engine = NULL;

    /**
     * Returns a singleton instance of Encrypt. An encryption key must be
     * provided in your "encrypt" configuration file.
     *
     *     $encrypt = Encrypt::instance();
     *
     * @param string $name configuration group name
     * @param array|null $config
     * @return Encrypt
     * @throws Kohana_Exception
     */
    public static function instance(string $name = NULL, array $config = NULL)
    {
        if ($name === NULL)
        {
            // Use the default instance name
            $name = Encrypt::$default;
        }

        if ( ! isset(Encrypt::$instances[$name]))
        {
            if ($config === NULL)
            {
                // Load the configuration data
                $config = Kohana::$config->load('encrypt')->$name;
            }

            if ( ! isset($config['key']))
            {
                // No default encryption key is provided!
                throw new Kohana_Exception('No encryption key is defined in the encryption configuration group: :group',
                    [':group' => $name]);
            }

            // Create a new instance
            Encrypt::$instances[$name] = new self($name, $config);
        }

        return Encrypt::$instances[$name];
    }

    /**
     * Creates a new Encrypt Engine instance.
     *
     * @param array $config
     * @param string $name
     */
    private function __construct(string $name, array $config)
    {
        if ( ! isset($config['type']))
        {
            $config['type'] = Encrypt_Engine_Openssl::TYPE;
        }

        $this->_name = $name;

        // Set the engine class name
        $engine_name = 'Encrypt_Engine_'.ucfirst($config['type']);

        // Create the engine class
        $this->_engine = new $engine_name($config);
    }

    /**
     * Encrypts a string and returns an encrypted string that can be decoded.
     *
     *     $data = $encrypt->encode($message);
     *
     * The encrypted binary data is encoded using [base64](http://php.net/base64_encode)
     * to convert it to a string. This string can be stored in a database,
     * displayed, and passed using most other means without corruption.
     *
     * @param string $message Message to be encrypted
     * @return null|string
     */
    public function encode(string $message): ?string
    {
        return $this->_engine->encrypt($message, $this->_create_iv());
    }

    /**
     * Decrypts an encoded string back to its original value.
     *
     *     $data = $encrypt->decode($ciphertext);
     *
     * @param string $ciphertext
     * @return null|string if decryption fails
     * @internal param string $data encoded string to be decrypted
     */
    public function decode(string $ciphertext): ?string
    {
        return $this->_engine->decrypt($ciphertext);
    }

    /**
     * Proxy for the create_iv function - to allow mocking and testing against KAT vectors
     *
     * @return string the initialization vector or FALSE on error
     */
    protected function _create_iv()
    {
        return $this->_engine->create_iv();
    }

    /**
     * Returns text representation of Encrypt class
     * @return string
     */
    public function __toString(): string
    {
        return get_class($this->_engine) . ' (' . $this->_name. ')';
    }
}
