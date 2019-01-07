<?php

/**
 * Test case for K7_ORM
 *
 * @package    K7/ORM
 * @group      k7
 * @group      k7.orm
 * @category   Test
 * @author     Craig Duncan <git@duncanc.co.uk>
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */

class K7_ORMTest extends Unittest_TestCase
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

class ORM_Example extends K7_ORM
{
	public function __construct()
	{
	}
}
