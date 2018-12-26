<?php
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

        $this->set_config([
            'type' => Encrypt_Engine_Openssl::TYPE,
            'key' => EncryptTestBase::KEY32,
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
            'type' => Encrypt_Engine_Openssl::TYPE,
            'cipher' => 'AES-128-CBC',
            'key' => EncryptTestBase::KEY16,
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
            'type' => Encrypt_Engine_Openssl::TYPE,
            'cipher' => 'AES-256-CBC',
            'key' => EncryptTestBase::KEY32,
        ]);

        $this->test_encode_and_decode($plaintext);
    }
}
