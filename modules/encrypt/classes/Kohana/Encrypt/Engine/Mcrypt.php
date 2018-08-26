<?php
/**
 * The Encrypt Mcrypt engine provides two-way encryption of text and binary strings
 * using the [Mcrypt](http://php.net/mcrypt) extension, which consists of three
 * parts: the key, the cipher, and the mode.
 *
 * The Key
 * :  A secret passphrase that is used for encoding and decoding
 *
 * The Cipher
 * :  A [cipher](http://php.net/mcrypt.ciphers) determines how the encryption
 *    is mathematically calculated. By default, the "rijndael-128" cipher
 *    is used. This is commonly known as "AES-128" and is an industry standard.
 *
 * The Mode
 * :  The [mode](http://php.net/mcrypt.constants) determines how the encrypted
 *    data is written in binary form. By default, the "nofb" mode is used,
 *    which produces short output with high entropy.
 *
 * @package    Kohana/Encrypt
 * @category   Security
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 * @deprecated
 */
class Kohana_Encrypt_Engine_Mcrypt extends Kohana_Encrypt_Engine
{
	/**
	 * @var string Engine type
	 */
	const TYPE = 'Mcrypt';

	/**
	 * @var  string  RAND type to use
	 *
	 * Only MCRYPT_DEV_URANDOM and MCRYPT_DEV_RANDOM are considered safe.
	 * Using MCRYPT_RAND will silently revert to MCRYPT_DEV_URANDOM
	 */
	protected static $_rand = MCRYPT_DEV_URANDOM;

	/**
	 * @var int the size of the Initialization Vector (IV) in bytes
	 */
	protected $_iv_size;

	/**
	 * Creates a new mcrypt wrapper.
	 *
	 * @param array $config Array with configuration
	 */
	public function __construct($config)
	{
		parent::__construct($config);

		if (isset($config['mode']))
		{
			$this->_mode = $config['mode'];
		}

		if ( ! isset($config['mode']) || $config['mode'] === NULL)
		{
			// Add the default mode
			$this->_mode = constant('MCRYPT_MODE_NOFB');
		}

		if (isset($config['cipher']))
		{
			$this->_cipher = $config['cipher'];
		}

		if ( ! isset($config['cipher']) || $config['cipher'] === NULL)
		{
			// Add the default cipher
			$this->_cipher = constant('MCRYPT_RIJNDAEL_128');
		}

		// Find the max length of the key, based on cipher and mode
		$size = mcrypt_get_key_size($this->_cipher, $this->_mode);

		if (isset($this->_key[$size]))
		{
			// Shorten the key to the maximum size
			$this->_key = substr($this->_key, 0, $size);
		}
		else
		{
			$this->_key = $this->_normalize_key($this->_key, $this->_cipher, $this->_mode);
		}

		/*
		 * Silently use MCRYPT_DEV_URANDOM when the chosen random number generator
		 * is not one of those that are considered secure.
		 */
		if ((Encrypt_Engine_Mcrypt::$_rand !== MCRYPT_DEV_URANDOM) AND (Encrypt_Engine_Mcrypt::$_rand !== MCRYPT_DEV_RANDOM))
		{
			Encrypt_Engine_Mcrypt::$_rand = MCRYPT_DEV_URANDOM;
		}

		// Store the IV size
		$this->_iv_size = mcrypt_get_iv_size($this->_cipher, $this->_mode);
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
	 * @param string $iv IV (Initialization vector)
	 * @return null|string
	 */
	public function encrypt(string $message, string $iv)
	{
		// Encrypt the data using the configured options and generated iv
		$data = mcrypt_encrypt($this->_cipher, $this->_key, $message, $this->_mode, $iv);

		// Use base64 encoding to convert to a string
		return base64_encode($iv.$data);
	}

	/**
	 * Decrypts an encoded string back to its original value.
	 *
	 *     $data = $encrypt->decode($ciphertext);
	 *
	 * @param string $ciphertext Encoded string to be decrypted
	 * @return null|string if decryption fails
	 */
	public function decrypt(string $ciphertext)
	{
		// Convert the data back to binary
		$data = base64_decode($ciphertext, TRUE);

		if ( ! $data)
		{
			// Invalid base64 data
			return NULL;
		}

		// Extract the initialization vector from the data
		$iv = substr($data, 0, $this->_iv_size);

		if ($this->_iv_size !== strlen($iv))
		{
			// The iv is not the expected size
			return NULL;
		}

		// Remove the iv from the data
		$data = substr($data, $this->_iv_size);

		// Return the decrypted data, trimming the \0 padding bytes from the end of the data
		return rtrim(mcrypt_decrypt($this->_cipher, $this->_key, $data, $this->_mode, $iv), "\0");
	}

	/**
	 * Proxy for the mcrypt_create_iv function - to allow mocking and testing against KAT vectors
	 * @return string the initialization vector or FALSE on error
	 * @throws Kohana_Exception
	 */
	public function create_iv(): string
	{
		// Create a random initialization vector of the proper size for the current cipher
		$iv = mcrypt_create_iv($this->_iv_size, Encrypt_Engine_Mcrypt::$_rand);
		if($iv)
		{
			return $iv;
		}

		throw new Kohana_Exception('Could not generate random iv');
	}

	/**
	 * Normalize key for PHP 5.6 for backwards compatibility
	 *
	 * This method is a shim to make PHP 5.6 behave in a B/C way for
	 * legacy key padding when shorter-than-supported keys are used
	 *
	 * @param   string $key encryption key
	 * @param   string $cipher mcrypt cipher
	 * @param   string $mode mcrypt mode
	 * @return bool|string
	 */
	protected function _normalize_key($key, $cipher, $mode)
	{
		// open the cipher
		$td = mcrypt_module_open($cipher, '', $mode, '');

		// loop through the supported key sizes
		foreach (mcrypt_enc_get_supported_key_sizes($td) as $supported) {
			// if key is short, needs padding
			if (strlen($key) <= $supported)
			{
				return str_pad($key, $supported, "\0");
			}
		}

		// at this point key must be greater than max supported size, shorten it
		return substr($key, 0, mcrypt_get_key_size($cipher, $mode));
	}

}
