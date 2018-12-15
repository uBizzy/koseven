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

        if (! Kohana::$caching) {
            $this->markTestSkipped('Please activate caching inside your bootstrap');
        }

        $this->cache(Cache::instance('redis'));
    }

    /**
     * Overwrites the test_delete method for redis because redis
     * returns number of items deleted not true or false!
     *
     * @throws Cache_Exception
     * @return  void
     */
    public function test_delete()
    {
        // Init
        $cache = $this->cache();
        $cache->delete_all();

        // Test deletion if real cached value
        if ( ! $cache->set('test_delete_1', 'This should not be here!', 0))
        {
            $this->fail('Unable to set cache value to delete!');
        }

        // Test delete returns number of elements deleted and we also check the value is gone
        $this->assertSame(1, $cache->delete('test_delete_1'));
        $this->assertNull($cache->get('test_delete_1'));

        // Test non-existant cache value returns 0 if no error
        $this->assertSame(0, $cache->delete('test_delete_1'));
    }

    /**
     * Test Redis::find() method, which searches for elements in cache by specific tag
     *
     * @dataProvider provider_set_get
     *
     * @param array $data Test Data
     * @param mixed $expected  Expected Result
     */
    public function test_find(array $data, $expected)
    {
        // Init
        $cache = $this->cache();
        $cache->delete_all();
        extract($data);

        // Set cache element with tag
        $this->assertTrue($cache->set_with_tags($id, $value, $ttl, array('testing')));

        // Find Tag
        $rows = $cache->find('testing');

        // Check if not empty and correct values returned
        $this->assertNotNull($rows);
    }

    /**
     * Test Redis::delete_tag
     *
     * @dataProvider provider_set_get
     *
     * @param array $data Test Data
     */
    public function test_delete_tag(array $data)
    {
        // Init
        $cache = $this->cache();
        $cache->delete_all();
        extract($data);

        // Set cache element with tag
        $this->assertTrue($cache->set_with_tags($id, $value, $ttl, array('testing')));

        // Delete
        $deleted = $cache->delete_tag('testing');
        $this->assertTrue($deleted);
    }

} // End Kohana_RedisTest
