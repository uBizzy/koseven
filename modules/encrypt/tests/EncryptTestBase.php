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
abstract class EncryptTestBase extends Unittest_TestCase
{
	/**
	 * @var string 128 bit encryption key
	 */
	const KEY16 = '0123456789012345';

	/**
	 * @var string 256 bit encryption key
	 */
	const KEY32 = '01234567890123456789012345678901';

	/**
	 * @return void
	 */
    public function setUp()
    {
        parent::setUp();

		// clear instances
		Encrypt::$instances = [];
	}

	/**
	 * @return void
	 */
	public function set_config(array $config)
	{
		Kohana::$config->load('encrypt')->set('default', $config);
	}

	/**
	 * @dataProvider provider_encode_and_decode
	 * @return void
	 */
	public function test_encode_and_decode(string $encryptable)
	{
		$this->encode_and_decode($encryptable);
	}

	/**
	 * @return void
	 */
	public function encode_and_decode($encryptable)
	{
		$instance = Encrypt::instance();
		$this->assertEquals($encryptable,$instance->decode($instance->encode($encryptable)));
	}

	/**
	 * Data source for encode_and_decode test
	 * @return array
	 */
	public function provider_encode_and_decode(): array
	{
		return [
			[
				'abcdefghijklm',
			],
			[
				'777888000',
			],
			[
				'verylongTEXTwithSTUFFandnumbers0123456789',
			],
		];
	}
}
