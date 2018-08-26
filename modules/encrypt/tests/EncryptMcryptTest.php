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
class EncryptMcryptTest extends EncryptTestBase
{
	/**
	 * @return void
	 */
    public function setUp()
    {
        parent::setUp();

		if ( ! extension_loaded('mcrypt'))
		{
			$this->markTestSkipped('The Mcrypt extension is not available.');
		}

        $this->set_config([
            'type' => Encrypt_Engine_Mcrypt::TYPE,
            'cipher' => MCRYPT_RIJNDAEL_128,
            'mode' => MCRYPT_MODE_CBC,
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
