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
            'key' => EncryptTestBase::KEY,
        ]);
    }

    /**
	 * @dataProvider provider_encode_and_decode
	 * @return void
	 */
	public function test_encode_and_decode(string $encryptable)
	{
		$this->encode_and_decode($encryptable);
	}
}
