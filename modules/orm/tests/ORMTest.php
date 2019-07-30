<?php

/**
 * Test case for KO7_ORM
 *
 * @package    KO7/ORM
 * @group      ko7
 * @group      ko7.orm
 * @category   Test
 * @author     Craig Duncan <git@duncanc.co.uk>
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE
 */

class KO7_ORMTest extends Unittest_TestCase
{
	/**
	 * Ensure has() doesn't attempt to count non-countables.
	 *
	 * @test
	 * @covers ORM::has
	 */
	public function test_has()
	{
		$orm = new ORM_Example;

		$result = $orm->has('children', FALSE);

		$this->assertSame(FALSE, $result);
	}
}

class ORM_Example extends KO7_ORM
{
	public function __construct()
	{
	}
}
