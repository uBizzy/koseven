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

        $this->set_config([
            'type' => Encrypt_Engine_Sodium::TYPE,
            'key' => EncryptTestBase::KEY32,
        ]);
    }
}
