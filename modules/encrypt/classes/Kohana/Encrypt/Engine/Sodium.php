<?php

/**
 * The Encrypt Sodium engine provides two-way encryption of text and binary strings
 * using the [Sodium](http://php.net/sodium) extension, which consists of two
 * parts: the key and the cipher.
 *
 * The Key
 * :  A secret passphrase that is used for encoding and decoding
 *
 * @package    Kohana/Encrypt
 * @category   Security
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_Encrypt_Engine_Sodium extends Kohana_Encrypt_Engine
{
	use Traits_Encrypt_Iv;

	/**
	 * @var string Engine type
	 */
	const TYPE = 'Sodium';

	/**
	 * @var int the size of the Initialization Vector (IV) in bytes
	 */
	protected $_iv_size;

	/**
	 * Sodium constructor.
	 * @param array $config Array with configuration
	 * @throws Kohana_Exception
	 */
	public function __construct($config)
	{
		parent::__construct($config);
		if( ! extension_loaded('sodium') || ! sodium_crypto_aead_aes256gcm_is_available())
		{
			throw new Kohana_Exception('Sodium extension is not available');
		}

		$length = mb_strlen($this->_key, '8bit');

		if ($length !== constant('SODIUM_CRYPTO_AEAD_AES256GCM_KEYBYTES'))
		{
			// No valid encryption key is provided!
			throw new Kohana_Exception('No valid encryption key is defined in the encryption configuration: length should be :required_length for AEAD-AES-256-GCM, is: :current_length',[
				':required_length' => constant('SODIUM_CRYPTO_AEAD_AES256GCM_KEYBYTES'),
				':current_length' => $length
			]);
		}

		$this->_iv_size = constant('SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES');
	}

	/**
	 * Encrypts
	 * @param string $message Your message to be encrypted
	 * @param string $iv
	 * @return null|string
	 */
	public function encrypt(string $message, string $iv)
	{
		$value = sodium_crypto_aead_aes256gcm_encrypt($message, '', $iv, $this->_key);

		if ($value === FALSE)
		{
			// Encryption failed
			return NULL;
		}

		//Base64 encode binary data, otherwise they cannot be
		//transformed into JSON.
		$value = base64_encode($value);
		$iv = base64_encode($iv);

		$json = json_encode(compact('iv', 'value'));

		if ( ! is_string($json))
		{
			// Encryption failed
			return NULL;
		}

		return base64_encode($json);
	}

	/**
	 * Decrypts the ciphertext
	 * @param string $ciphertext Ciphertext to be decrypted
	 * @return null|string
	 */
	public function decrypt(string $ciphertext)
	{
		// Convert the data back to binary
        $decode = base64_decode($ciphertext);
        //Check if base64 decoding succeeded
        if($decode === false)
        {
            return NULL;
        }

        $data = json_decode($decode, TRUE);

        //check if json_decode succeeded
		if(is_null($data))
		{
			return NULL;
		}

		// If the payload is not valid JSON or does not have the proper keys set we will
		// assume it is invalid and bail out of the routine since we will not be able
		// to decrypt the given value. We'll also check the MAC for this encryption.
		if ( ! $this->valid_payload($data))
		{
			// Decryption failed
			return NULL;
		}

		$iv = base64_decode($data['iv']);
		if ($iv === FALSE)
		{
			// Invalid base64 data
			return NULL;
		}

		// Here we will decrypt the value. If we are able to successfully decrypt it
		// we will then unserialize it and return it out to the caller. If we are
		// unable to decrypt this value we will return NULL.
		$value = base64_decode($data['value']);

		if($value === FALSE)
		{
			return NULL;
		}

		$decrypted = sodium_crypto_aead_aes256gcm_decrypt($value, '', $iv, $this->_key);

		if ($decrypted === FALSE)
		{
			return NULL;
		}

		return $decrypted === FALSE ? NULL : $decrypted;
	}

	/**
	 * Verify that the encryption payload is valid.
	 * @param $payload
	 * @return bool
	 */
	protected function valid_payload($payload): bool
	{
		return is_array($payload) &&
			isset($payload['iv'], $payload['value']) &&
			strlen(base64_decode($payload['iv'], TRUE)) === $this->_iv_size;
	}
}