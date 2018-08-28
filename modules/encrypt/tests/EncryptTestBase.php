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
	 * @var string 256 bit encryption key
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

	public function set_config(array $config): void
	{
		Kohana::$config->load('encrypt')->set('default', $config);
	}

	/**
	 * @dataProvider provider_encode_and_decode
	 */
	public function test_encode_and_decode(string $encryptable): void
	{
		$this->encode_and_decode($encryptable);
	}

	public function encode_and_decode($encryptable): void
	{
		$this->assertEquals($encryptable, Encrypt::instance()->decode(Encrypt::instance()->encode($encryptable)));
	}

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
