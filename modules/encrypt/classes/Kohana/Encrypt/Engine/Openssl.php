<?php
/**
 * The Encrypt Openssl engine provides two-way encryption of text and binary strings
 * using the [OpenSSL](http://php.net/openssl) extension, which consists of two
 * parts: the key and the cipher.
 *
 * The Key
 * :  A secret passphrase that is used for encoding and decoding
 *
 * The Cipher
 * :  A [cipher](http://php.net/manual/en/openssl.ciphers.php) determines how the encryption
 *    is mathematically calculated. By default, the "AES-256-CBC" cipher
 *    is used.
 *
 * @package    Kohana
 * @category   Security
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_Encrypt_Engine_Openssl extends Kohana_Encrypt_Engine
{
    use Traits_Encrypt_Iv;

    /**
     * @var string Engine type
     */
    const TYPE = 'Openssl';

    /**
     * @var int the size of the Initialization Vector (IV) in bytes
     */
    protected $_iv_size;

    /**
     * Creates a new openssl wrapper.
     *
     * @param array $config Array with configuration
     * @throws Kohana_Exception
     */
    public function __construct(array $config)
    {
        if( ! function_exists('openssl_cipher_iv_length'))
        {
            throw new Kohana_Exception('OpenSSL extension is not installed.');
        }

        if ( ! isset($config['cipher']) || $config['cipher'] === NULL)
        {
            // Add the default cipher
            $this->_cipher = 'AES-256-CBC';
        }

        parent::__construct($config);

        $this->_iv_size = openssl_cipher_iv_length($this->_cipher);

        $length = mb_strlen($this->_key, '8bit');

        // Validate configuration
        if ($this->_cipher === 'AES-128-CBC')
        {
            if ($length !== 16)
            {
                // No valid encryption key is provided!
                throw new Kohana_Exception('No valid encryption key is defined in the encryption configuration: length should be 16 for AES-128-CBC');
            }
        }
        elseif ($this->_cipher === 'AES-256-CBC')
        {
            if ($length !== 32)
            {
                // No valid encryption key is provided!
                throw new Kohana_Exception('No valid encryption key is defined in the encryption configuration: length should be 32 for AES-256-CBC');
            }
        }
        else
        {
            // No valid encryption cipher is provided!
            throw new Kohana_Exception('No valid encryption cipher is defined in the encryption configuration.');
        }
    }

    /**
     * Encrypts a string and returns an encrypted string that can be decoded.
     * @param string $data
     * @param string $iv
     * @return null|string
     */
    public function encrypt(string $data, string $iv): ?string
    {
        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = \openssl_encrypt($data, $this->_cipher, $this->_key, 0, $iv);

        if ($value === FALSE)
        {
            // Encryption failed
            return NULL;
        }

        // Once we have the encrypted value we will go ahead base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.
        $mac = $this->hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if ( ! is_string($json))
        {
            // Encryption failed
            return NULL;
        }

        return base64_encode($json);
    }

    /**
     * Decrypts an encoded string back to its original value.
     *
     * @param   string $data encoded string to be decrypted
     * @return NULL|string if decryption fails
     */
    public function decrypt(string $data): ?string
    {
        // Convert the data back to binary
        $data = json_decode(base64_decode($data), TRUE);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if ( ! $this->valid_payload($data))
        {
            // Decryption failed
            return NULL;
        }

        if ( ! $this->valid_mac($data))
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
        $decrypted = \openssl_decrypt($data['value'], $this->_cipher, $this->_key, 0, $iv);

        if ($decrypted === FALSE)
        {
            return NULL;
        }

        return $decrypted;
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @return string
     */
    protected function hash($iv, $value): string
    {
        return hash_hmac('sha256', $iv.$value, $this->_key);
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function valid_payload($payload): bool
    {
        return is_array($payload) &&
                isset($payload['iv'], $payload['value'], $payload['mac']) &&
                strlen(base64_decode($payload['iv'], TRUE)) === $this->_iv_size;
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function valid_mac(array $payload): bool
    {
        $bytes = $this->create_iv();
        $calculated = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, TRUE);

        return hash_equals(hash_hmac('sha256', $payload['mac'], $bytes, TRUE), $calculated);
    }
}
