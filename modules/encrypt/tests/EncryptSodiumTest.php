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
class EncryptSodiumTest extends EncryptTestBase
{
	/**
	 * @return void
	 */
    public function setUp()
    {
        parent::setUp();

        if ( ! extension_loaded('sodium'))
		{
			$this->markTestSkipped('The Sodium extension is not available.');
		}
    }

	/**
	 * @dataProvider provider_encode_and_decode
	 * @return void
	 */
	public function test_256_bit(string $encryptable)
	{
		$this->set_config([
			'type' => Encrypt_Engine_Sodium::TYPE,
			'key' => EncryptTestBase::KEY32,
		]);

		$this->encode_and_decode($encryptable);
	}
}
