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
        if( ! function_exists('sodium_crypto_aead_aes256gcm_is_available') || ! sodium_crypto_aead_aes256gcm_is_available())
        {
            throw new Kohana_Exception('Sodium extension is not available');
        }

        $this->_iv_size = constant('SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES');
    }

    /**
     * Encrypts
     * @param string $message Your message to be encrypted
     * @param string $iv
     * @return null|string
     */
    public function encrypt(string $message, string $iv): ?string
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
     * @param string $ciphertext
     * @return null|string
     */
    public function decrypt(string $ciphertext): ?string
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
    protected function valid_payload($payload): bool
    {
        return is_array($payload) &&
            isset($payload['iv'], $payload['value']) &&
            strlen(base64_decode($payload['iv'], TRUE)) === $this->_iv_size;
    }
}