<?php
/**
 * @group      kohana
 * @group      kohana.encrypt
 *
 * @package    Kohana/Encrypt
 * @category   Test
 * @author     Koseven Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2016-2018 Koseven Team
 * @license    https://koseven.ga/LICENSE.md
 */
abstract class EncryptTestBase extends Unittest_TestCase
{
    /**
	 * 128 bit encryption key
     * @var string
     */
    const KEY16 = '0123456789012345';

    /**
	 * 256 bit encryption key
     * @var string
     */
    const KEY32 = '01234567890123456789012345678901';

    /**
	 * Should be called from every test
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // clear instances
        Encrypt::$instances = [];
    }

    /**
	 * Overwrites Configuration Values
	 *
     * @param array $config
     * @param string|null $name
	 *
	 * @return void
	 * @throws Kohana_Exception
     */
    public function set_config(array $config, string $name = NULL)
    {
        if ($name === NULL)
        {
            $name = Encrypt::$default;
        }
        Kohana::$config->load('encrypt')->set($name, $config);
    }

    /**
     * Test Encrypt class initialization
     * @return void
	 * @throws Kohana_Exception
     */
    public function test_initialization()
    {
        $this->assertCount(0, Encrypt::$instances);
        Encrypt::instance();
        Encrypt::instance(Encrypt::$default);
        $this->assertCount(1, Encrypt::$instances);
        $this->assertArrayHasKey('default', Encrypt::$instances);

        $this->set_config(Kohana::$config->load('encrypt')->default, 'secondary');
        Encrypt::instance('secondary');
        $this->assertCount(2, Encrypt::$instances);
        $this->assertArrayHasKey('secondary', Encrypt::$instances);
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
