<?php
include_once(K7::find_file('tests/cache', 'CacheBasicMethodsTest'));

/**
 * @package    K7/Cache
 * @group      k7
 * @group      k7.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class K7_SqliteTest extends K7_CacheBasicMethodsTest {

	/**
	 * This method MUST be implemented by each driver to setup the `Cache`
	 * instance for each test.
	 *
	 * This method should do the following tasks for each driver test:
	 *
	 *  - Test the Cache instance driver is available, skip test otherwise
	 *  - Setup the Cache instance
	 *  - Call the parent setup method, `parent::setUp()`
	 *
	 * @return  void
	 */
	public function setUp()
	{
		parent::setUp();

		if ( ! extension_loaded('pdo_sqlite'))
		{
			$this->markTestSkipped('SQLite PDO PHP Extension is not available');
		}

		if ( ! K7::$config->load('cache.sqlite'))
		{
			K7::$config->load('cache')
				->set(
					'sqlite',
					[
						'driver'             => 'sqlite',
						'default_expire'     => 3600,
						'database'           => 'memory',
						'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
					]
				);
		}

		$this->cache(Cache::instance('sqlite'));
	}

} // End K7_SqliteTest
