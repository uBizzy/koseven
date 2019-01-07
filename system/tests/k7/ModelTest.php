<?php

/**
 * This test only really exists for code coverage.
 *
 * @group k7
 * @group k7.core
 * @group k7.core.model
 *
 * @package    K7
 * @category   Tests
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class K7_ModelTest extends Unittest_TestCase
{
	/**
	 * Test the model's factory.
	 *
	 * @test
	 * @covers Model::factory
	 */
	public function test_create()
	{
		$foobar = Model::factory('Foobar');

		$this->assertEquals(TRUE, $foobar instanceof Model);
	}
}

class Model_Foobar extends Model
{

}
