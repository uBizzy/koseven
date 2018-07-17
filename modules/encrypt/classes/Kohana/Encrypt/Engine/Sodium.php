<?php

/**
 * Class Kohana_Encrypt_Engine_Sodium
 * The Encrypt Sodium engine provides two-way encryption of text and binary strings
 * using the [Sodium](http://php.net/sodium) extension, which consists of two
 * parts: the key and the cipher.
 *
 * The Key
 * :  A secret passphrase that is used for encoding and decoding
 *
 *
 *
 * @package    Kohana
 * @category   Security
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_Encrypt_Engine_Sodium extends Kohana_Encrypt_Engine
{
    const TYPE = 'Sodium';

    /**
     * @var int the size of the Initialization Vector (IV) in bytes
     */
    protected $_iv_size;

    /**
     * Sodium constructor.
     * @param mixed $key_config
     * @param null $mode
     * @param null $cipher
     * @throws Kohana_Exception
     */
    public function __construct($key_config, $mode = NULL, $cipher = NULL)
    {
        parent::__construct($key_config, $mode, $cipher);
        if(function_exists('sodium_crypto_aead_aes256gcm_is_available') &&  ! sodium_crypto_aead_aes256gcm_is_available())
        {
            throw new Kohana_Exception('Sodium extension is not available');
        }

        $this->_iv_size = constant('SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES');
    }

    /**
     * Encrypts
     * @param String $message Your message to be encrypted
     * @param String $iv
     * @return null|string
     */
    public function encrypt(String $message, String $iv): ?string
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
     * @param String $ciphertext
     * @return null|string
     */
    public function decrypt(String $ciphertext): ?string
    {
        // Convert the data back to binary
        $data = json_decode(base64_decode($ciphertext), TRUE);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if ( ! $this->valid_payload($data))
        {
            // Decryption failed
            return NULL;
        }

        $iv = base64_decode($data['iv']);
        if ( ! $iv)
        {
            // Invalid base64 data
            return NULL;
        }

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will return NULL.
        $decrypted = sodium_crypto_aead_aes256gcm_decrypt(base64_decode($data['value']), '', $iv, $this->_key);

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
    protected function valid_payload($payload)
    {
        return is_array($payload) AND
            isset($payload['iv'], $payload['value']) AND
            strlen(base64_decode($payload['iv'], TRUE)) === $this->_iv_size;
    }

    /**
     * Creates random IV (Initialization vector)
     * @see https://paragonie.com/book/pecl-libsodium/read/08-advanced.md#crypto-aead-aes256gcm
     * @return string
     */
    public function create_iv(): string
    {
        return random_bytes($this->_iv_size);
    }
}