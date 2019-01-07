<?php

/**
 * Tests K7 Core
 *
 * @TODO Use a virtual filesystem (see phpunit doc on mocking fs) for find_file etc.
 *
 * @group k7
 * @group k7.core
 * @group k7.core.core
 *
 * @package    K7
 * @category   Tests
 * @author     Kohana Team
 * @author     Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class K7_CoreTest extends Unittest_TestCase
{
	protected $old_modules = [];

	/**
	 * Captures the module list as it was before this test
	 *
	 * @return null
	 */
	// @codingStandardsIgnoreStart
	public function setUp()
	// @codingStandardsIgnoreEnd
	{
		parent::setUp();
		$this->old_modules = K7::modules();
	}

	/**
	 * Restores the module list
	 *
	 * @return null
	 */
	// @codingStandardsIgnoreStart
	public function tearDown()
	// @codingStandardsIgnoreEnd
	{
		K7::modules($this->old_modules);
	}

	/**
	 * Provides test data for test_sanitize()
	 *
	 * @return array
	 */
	public function provider_sanitize()
	{
		return [
			// $value, $result
			['foo', 'foo'],
			["foo\r\nbar", "foo\nbar"],
			["foo\rbar", "foo\nbar"],
		];
	}

	/**
	 * Tests K7::santize()
	 *
	 * @test
	 * @dataProvider provider_sanitize
	 * @covers K7::sanitize
	 * @param boolean $value  Input for K7::sanitize
	 * @param boolean $result Output for K7::sanitize
	 */
	public function test_sanitize($value, $result)
	{
		$this->assertSame($result, K7::sanitize($value));
	}

	/**
	 * Passing FALSE for the file extension should prevent appending any extension.
	 * See issue #3214
	 *
	 * @test
	 * @covers  K7::find_file
	 */
	public function test_find_file_no_extension()
	{
		// EXT is manually appened to the _file name_, not passed as the extension
		$path = K7::find_file('classes', $file = 'K7/Core'.EXT, FALSE);

		$this->assertInternalType('string', $path);

		$this->assertStringEndsWith($file, $path);
	}

	/**
	 * If a file can't be found then find_file() should return FALSE if
	 * only a single file was requested, or an empty array if multiple files
	 * (i.e. configuration files) were requested
	 *
	 * @test
	 * @covers K7::find_file
	 */
	public function test_find_file_returns_false_or_array_on_failure()
	{
		$this->assertFalse(K7::find_file('configy', 'zebra'));

		$this->assertSame([], K7::find_file('configy', 'zebra', NULL, TRUE));
	}

	/**
	 * K7::list_files() should return an array on success and an empty array on failure
	 *
	 * @test
	 * @covers K7::list_files
	 */
	public function test_list_files_returns_array_on_success_and_failure()
	{
		$files = K7::list_files('config');

		$this->assertInternalType('array', $files);
		$this->assertGreaterThan(3, count($files));

		$this->assertSame([], K7::list_files('geshmuck'));
	}

	/**
	 * Provides test data for testCache()
	 *
	 * @return array
	 */
	public function provider_cache()
	{
		return [
			// $value, $result
			['foo', 'hello, world', 10],
			['bar', NULL, 10],
			['bar', NULL, -10],
		];
	}

	/**
	 * Tests K7::cache()
	 *
	 * @test
	 * @dataProvider provider_cache
	 * @covers K7::cache
	 * @param boolean $key      Key to cache/get for K7::cache
	 * @param boolean $value    Output from K7::cache
	 * @param boolean $lifetime Lifetime for K7::cache
	 */
	public function test_cache($key, $value, $lifetime)
	{
		K7::cache($key, $value, $lifetime);
		$this->assertEquals($value, K7::cache($key));
	}

	/**
	 * Tests K7::find_file() cache is saved on shutdown.
	 *
	 * @test
	 */
	/*public function test_find_file_cache_saved()
	{
		$old_caching     = K7::$caching;
		$old_errors      = K7::$errors;
		K7::$caching = TRUE;
		K7::$errors  = FALSE;

		// trigger find_file() so K7::$_files_changed is set to TRUE
		K7::find_file('abc', 'def');

		// trigger shutdown so k7 write to file cache
		K7::shutdown_handler();

		$this->assertInternalType('array', K7::file_cache('K7::find_file()'));

		K7::$caching = $old_caching;
		K7::$errors  = $old_errors;
	}*/

	/**
	 * Provides test data for test_message()
	 *
	 * @return array
	 */
	public function provider_message()
	{
		return [
			['no_message_file', 'anything', 'default', 'default'],
			['no_message_file', NULL, 'anything', []],
			['k7_core_message_tests', 'bottom_only', 'anything', 'inherited bottom message'],
			['k7_core_message_tests', 'cfs_replaced', 'anything', 'overriding cfs_replaced message'],
			['k7_core_message_tests', 'top_only', 'anything', 'top only message'],
			['k7_core_message_tests', 'missing', 'default', 'default'],
			['k7_core_message_tests', NULL, 'anything',
				[
					'bottom_only'  => 'inherited bottom message',
					'cfs_replaced' => 'overriding cfs_replaced message',
					'top_only'     => 'top only message'
				]
			],
		];
	}

	/**
	 * Tests K7::message()
	 *
	 * @test
	 * @dataProvider provider_message
	 * @covers       K7::message
	 * @param string $file     to pass to K7::message
	 * @param string $key      to pass to K7::message
	 * @param string $default  to pass to K7::message
	 * @param string $expected Output for K7::message
	 */
	public function test_message($file, $key, $default, $expected)
	{
		$test_path = realpath(dirname(__FILE__).'/../test_data/message_tests');
		K7::modules(['top' => "$test_path/top_module", 'bottom' => "$test_path/bottom_module"]);

		$this->assertEquals($expected, K7::message($file, $key, $default, $expected));
	}

	/**
	 * Provides test data for test_error_handler()
	 *
	 * @return array
	 */
	public function provider_error_handler()
	{
		return [
			[1, 'Foobar', 'foobar.php', __LINE__],
		];
	}

	/**
	 * Tests K7::error_handler()
	 *
	 * @test
	 * @dataProvider provider_error_handler
	 * @covers K7::error_handler
	 * @param boolean $code  Input for K7::sanitize
	 * @param boolean $error  Input for K7::sanitize
	 * @param boolean $file  Input for K7::sanitize
	 * @param boolean $line Output for K7::sanitize
	 */
	public function test_error_handler($code, $error, $file, $line)
	{
		$error_level = error_reporting();
		error_reporting(E_ALL);
		try
		{
			K7::error_handler($code, $error, $file, $line);
		}
		catch (Exception $e)
		{
			$this->assertEquals($code, $e->getCode());
			$this->assertEquals($error, $e->getMessage());
		}
		error_reporting($error_level);
	}

	/**
	 * Provides test data for test_modules_sets_and_returns_valid_modules()
	 *
	 * @return array
	 */
	public function provider_modules_detects_invalid_modules()
	{
		return [
			[['unittest' => MODPATH.'fo0bar']],
			[['unittest' => MODPATH.'unittest', 'fo0bar' => MODPATH.'fo0bar']],
		];
	}

	/**
	 * Tests K7::modules()
	 *
	 * @test
	 * @dataProvider provider_modules_detects_invalid_modules
	 * @expectedException K7_Exception
	 * @param boolean $source   Input for K7::modules
	 *
	 */
	public function test_modules_detects_invalid_modules($source)
	{
		$modules = K7::modules();

		try
		{
			K7::modules($source);
		}
		catch(Exception $e)
		{
			// Restore modules
			K7::modules($modules);

			throw $e;
		}

		// Restore modules
		K7::modules($modules);
	}

	/**
	 * Provides test data for test_modules_sets_and_returns_valid_modules()
	 *
	 * @return array
	 */
	public function provider_modules_sets_and_returns_valid_modules()
	{
		return [
			[[], []],
			[['module' => __DIR__], ['module' => $this->dirSeparator(__DIR__.'/')]],
		];
	}

	/**
	 * Tests K7::modules()
	 *
	 * @test
	 * @dataProvider provider_modules_sets_and_returns_valid_modules
	 * @param boolean $source   Input for K7::modules
	 * @param boolean $expected Output for K7::modules
	 */
	public function test_modules_sets_and_returns_valid_modules($source, $expected)
	{
		$modules = K7::modules();

		try
		{
			$this->assertEquals($expected, K7::modules($source));
		}
		catch(Exception $e)
		{
			K7::modules($modules);

			throw $e;
		}

		K7::modules($modules);
	}

	/**
	 * To make the tests as portable as possible this just tests that
	 * you get an array of modules when you can K7::modules() and that
	 * said array contains unittest
	 *
	 * @test
	 * @covers K7::modules
	 */
	public function test_modules_returns_array_of_modules()
	{
		$modules = K7::modules();

		$this->assertInternalType('array', $modules);

		$this->assertArrayHasKey('unittest', $modules);
	}

	/**
	 * Tests K7::include_paths()
	 *
	 * The include paths must contain the apppath and syspath
	 * @test
	 * @covers K7::include_paths
	 */
	public function test_include_paths()
	{
		$include_paths = K7::include_paths();
		$modules       = K7::modules();

		$this->assertInternalType('array', $include_paths);

		// We must have at least 2 items in include paths (APP / SYS)
		$this->assertGreaterThan(2, count($include_paths));
		// Make sure said paths are in the include paths
		// And make sure they're in the correct positions
		$this->assertSame(APPPATH, reset($include_paths));
		$this->assertSame(SYSPATH, end($include_paths));

		foreach ($modules as $module)
		{
			$this->assertContains($module, $include_paths);
		}
	}
}

