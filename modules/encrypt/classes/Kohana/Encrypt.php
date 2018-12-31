<?php

/**
 * @package    Kohana/Encrypt
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2016-2018 Koseven Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_Encrypt
{
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
    private $_engine = NULL;

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

        if (!isset(Encrypt::$instances[$name]))
        {
            if ($config === NULL)
            {
                // Load the configuration data
                $config = Kohana::$config->load('encrypt')->$name;
            }

            if (!isset($config[Kohana_Encrypt_Engine::CONFIG_KEY]))
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
        if (!isset($config[Kohana_Encrypt_Engine::CONFIG_TYPE]))
        {
            $config[Kohana_Encrypt_Engine::CONFIG_TYPE] = Encrypt_Engine_Openssl::TYPE;
        }

        $this->_name = $name;

        // Set the engine class name
        $engine_name = 'Encrypt_Engine_' . ucfirst($config[Kohana_Encrypt_Engine::CONFIG_TYPE]);

        // Create the engine class
        $this->setEngine(new $engine_name($config));
    }

    /**
     * Set engine, ensure its parent is Kohana_Encrypt_Engine
     * @param Kohana_Encrypt_Engine $engine
     * @return void
     */
    private function setEngine(Kohana_Encrypt_Engine $engine)
    {
        $this->_engine = $engine;
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
     * WARNING! The $iv variable is for testing purposes only, do not use this variable
     * unless you REALLY know what you are doing.
     *
     * @param string $message Message to be encrypted
     * @param string|null Initialization Vector $iv
     * @return null|string
     */
    public function encode(string $message, string $iv = NULL)
    {
        if (is_null($iv))
        {
            $iv = $this->_create_iv();
        }
        return $this->_engine->encrypt($message, $iv);
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
    public function decode(string $ciphertext)
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
        return get_class($this->_engine) . ' (' . $this->_name . ')';
    }
}
