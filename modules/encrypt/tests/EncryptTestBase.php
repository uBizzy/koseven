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
     * @param array $config
     * @param string|null $name
     * @return void
     */
    public function set_config(array $config, string $name = NULL)
    {
        if (is_null($name))
        {
            $name = Encrypt::$default;
        }
        Kohana::$config->load('encrypt')->set($name, $config);
    }

    /**
     * @dataProvider provider_encode_and_decode
     * @param string $plaintext
     * @return void
     */
    public function test_encode_and_decode(string $plaintext)
    {
        $this->encode_and_decode($plaintext);
    }

    /**
     * @param string $plaintext
     * @return void
     */
    private function encode_and_decode(string $plaintext)
    {
        $instance = Encrypt::instance();
        $this->assertEquals($plaintext, $instance->decode($instance->encode($plaintext)));
    }

    /**
     * Test Encrypt class initialization
     * @return void
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
