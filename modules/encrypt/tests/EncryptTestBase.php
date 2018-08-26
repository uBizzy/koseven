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
	 * @var string encryption key
	 */
	const KEY = '01234567890123456789012345678901';

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
	 * @return void
	 */
	public function encode_and_decode($encryptable)
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
