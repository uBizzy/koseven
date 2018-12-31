<?php

use Encrypt_Engine_Mcrypt as Mcrypt;
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
class EncryptMcryptTest extends EncryptTestBase
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        if (!extension_loaded('mcrypt'))
        {
            $this->markTestSkipped('The Mcrypt extension is not available.');
        }

        $this->set_config([
            Mcrypt::CONFIG_TYPE => Mcrypt::TYPE,
            Mcrypt::CONFIG_KEY => EncryptTestBase::KEY32,
            Mcrypt::CONFIG_CIPHER => MCRYPT_RIJNDAEL_128,
            Mcrypt::CONFIG_MODE => MCRYPT_MODE_CBC,
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
            Mcrypt::CONFIG_TYPE => Mcrypt::TYPE,
            Mcrypt::CONFIG_KEY => EncryptTestBase::KEY16,
            Mcrypt::CONFIG_CIPHER => MCRYPT_RIJNDAEL_128,
            Mcrypt::CONFIG_MODE => MCRYPT_MODE_CBC,
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
            Mcrypt::CONFIG_TYPE => Mcrypt::TYPE,
            Mcrypt::CONFIG_KEY => EncryptTestBase::KEY32,
            Mcrypt::CONFIG_CIPHER => MCRYPT_RIJNDAEL_128,
            Mcrypt::CONFIG_MODE => MCRYPT_MODE_CBC,
        ]);

        $this->test_encode_and_decode($plaintext);
    }
}
