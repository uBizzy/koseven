<?php

/**
 * @category   Security
 * @package    Kohana/Encrypt
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2016-2018 Koseven Team
 * @license    https://koseven.ga/LICENSE.md
 */
abstract class Kohana_Encrypt_Engine {
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

		if (isset($config['mode']))
		{
			$this->_mode = $config['mode'];
		}

		if (isset($config['cipher']))
		{
			$this->_cipher = $config['cipher'];
		}
	}

	/**
	 * Encrypts the message
	 * @param string $message Your message to be encrypted.
	 * @param string $iv
	 * @return null|string
	 */
	abstract public function encrypt(string $message, string $iv);

	/**
	 * Decrypts the ciphertext
	 * @param string $ciphertext Your ciphertext to be decrypted.
	 * @return null|string
	 */
	abstract public function decrypt(string $ciphertext);

	/**
	 * Creates random IV (Initialization vector) for each encryption action.
	 * @return string
	 */
	abstract public function create_iv();
}
