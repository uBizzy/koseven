<?php
include_once(Kohana::find_file('tests/cache', 'CacheBasicMethodsTest'));

/**
 * @package    Kohana/Cache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Koseven Team
 * @copyright  (c) Koseven Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_RedisTest extends Kohana_CacheBasicMethodsTest {

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
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        if ( ! extension_loaded('redis'))
        {
            $this->markTestSkipped('Redis PHP Extension is not available');
        }

        if ( ! Kohana::$config->load('cache.redis'))
        {
            $this->markTestSkipped('Please specify Redis Server configuration inside your cache config');
        }

        $this->cache(Cache::instance('redis'));
    }

} // End Kohana_RedisTest