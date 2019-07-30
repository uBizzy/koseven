<?php
include_once(KO7::find_file('tests/cache', 'CacheBasicMethodsTest'));

/**
 * @package    KO7/Cache
 * @group      ko7
 * @group      ko7.cache
 * @category   Test
 * 
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE
 */
class KO7_SqliteTest extends KO7_CacheBasicMethodsTest {

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
	public function setUp(): void
	{
		parent::setUp();

		if ( ! extension_loaded('pdo_sqlite'))
		{
			$this->markTestSkipped('SQLite PDO PHP Extension is not available');
		}

		if ( ! KO7::$config->load('cache.sqlite'))
		{
			KO7::$config->load('cache')
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

} // End KO7_SqliteTest
