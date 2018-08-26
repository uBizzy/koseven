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
class EncryptTest extends Unittest_TestCase
{
	public function setUp()
	{
		parent::setUp();

		Kohana::$config->load('encrypt')->set(
			'default',
			[
				'type' => Kohana_Encrypt_Engine_Openssl::TYPE,
				'key' => '01234567890123456789012345678901',
			]
		);
	}

	/**
	 * @dataProvider provider_encode_and_decode
	 * @return void
	 */
	public function test_encode_and_decode($encryptable)
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
		];
	}
}
