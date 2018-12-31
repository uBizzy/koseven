<?php

use Encrypt_Engine_Openssl as OpenSSL;

/**
 * @group      kohana
 * @group      kohana.encrypt
 *
 * @package    Kohana/Encrypt
 * @category   Test
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class EncryptOpensslTest extends EncryptTestBase
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        if (!extension_loaded('openssl'))
        {
            $this->markTestSkipped('The OpenSSL extension is not available.');
        }

        $this->set_config([
            OpenSSL::CONFIG_TYPE => OpenSSL::TYPE,
            OpenSSL::CONFIG_CIPHER => OpenSSL::AES_256_CBC,
            OpenSSL::CONFIG_KEY => EncryptTestBase::KEY32,
        ]);
    }

    /**
     * @dataProvider provider_encode_and_decode
     * @param string $plaintext
     * @return void
     */
    public function test_128_bit(string $plaintext)
    {
        $this->set_config([
            OpenSSL::CONFIG_TYPE => OpenSSL::TYPE,
            OpenSSL::CONFIG_CIPHER => OpenSSL::AES_128_CBC,
            OpenSSL::CONFIG_KEY => EncryptTestBase::KEY16,
        ]);

        $this->test_encode_and_decode($plaintext);
    }

    /**
     * @dataProvider provider_encode_and_decode
     * @param string $plaintext
     * @return void
     */
    public function test_256_bit(string $plaintext)
    {
        $this->set_config([
            OpenSSL::CONFIG_TYPE => Encrypt_Engine_Openssl::TYPE,
            OpenSSL::CONFIG_CIPHER => OpenSSL::AES_256_CBC,
            OpenSSL::CONFIG_KEY => EncryptTestBase::KEY32,
        ]);

        $this->test_encode_and_decode($plaintext);
    }
}
