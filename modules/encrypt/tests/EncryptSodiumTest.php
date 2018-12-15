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
	
    /**
     * Tests against known answers
     *
     * @dataProvider provider_kat
     *
     * @param  array $vectors   Known Answers
     * @throws Kohana_Exception
     */
    public function test_kat(array $vectors)
    {
        // Init
	extract($vectors);
        $this->set_config([
            'type' => Encrypt_Engine_Sodium::TYPE,
            'key' => EncryptTestBase::KEY32,
        ]);

        // Test encryption with known answers
        $encrypt = Encrypt::instance();
        $encrypted = $encrypt->_engine->encrypt($plaintext, $iv);
        $this->assertEquals($encrypted, $ciphertext);
        $decrypted = $encrypt->_engine->decrypt($encrypted);
        $this->assertEquals($decrypted, $plaintext);
    }

    /**
     * Data source for kat
     * @return array
     */
    public function provider_kat(): array
    {
        return [
            [
                [
                    'iv'         => '000000000000',
                    'ciphertext' => 'eyJpdiI6Ik1EQXdNREF3TURBd01EQXciLCJ2YWx1ZSI6Inp2MncyTmdQWW5TcFdBclV2Y2xsblVTVzFQMD0ifQ==',
                    'plaintext'  => 'test'
                ],
                [
                    'iv'         => '111111111111',
                    'ciphertext' => 'eyJpdiI6Ik1URXhNVEV4TVRFeE1URXgiLCJ2YWx1ZSI6ImxSVXgwWDBNQklkSXRUOGw5cGIwVmtMSm96XC9GIn0=',
                    'plaintext'  => 'test2'
                ],
                [
                    'iv'         => '222222222222',
                    'ciphertext' => 'eyJpdiI6Ik1qSXlNakl5TWpJeU1qSXkiLCJ2YWx1ZSI6Ik5OWGxETGNmZmlhKzlZd2xOSDFvSzZCd1wvRmlBIn0=',
                    'plaintext'  => 'test3'
                ],
                [
                    'iv'         => '333333333333',
                    'ciphertext' => 'eyJpdiI6Ik16TXpNek16TXpNek16TXoiLCJ2YWx1ZSI6IlJrMnlXTXRPRGlrcG8yckNsTFVpTG93RDZWODEifQ==',
                    'plaintext'  => 'test4'
                ],
                [
                    'iv'         => '444444444444',
                    'ciphertext' => 'eyJpdiI6Ik5EUTBORFEwTkRRME5EUTAiLCJ2YWx1ZSI6IitkT2JsRkd0eTFLZkpaSU0zYjN4T2UremlKbEkifQ==',
                    'plaintext'  => 'test5'
                ],
            ]
        ];
    }
}
