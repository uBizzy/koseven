<?php
/**
 * @package    Kohana/Encrypt
 * @group      kohana
 * @group      kohana.encrypt
 * @category   Test
 * @author     Koseven Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class EncryptOpensslTest extends EncryptBase
{
	/**
	 * @return void
	 */
    public function setUp()
    {
        parent::setUp();

        $this->set_config([
            'type' => Encrypt_Engine_Openssl::TYPE,
            'key' => EncryptBase::KEY,
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
