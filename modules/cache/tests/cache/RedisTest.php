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
     * @dataProvider provider_set_get
     *
     * @throws Cache_Exception
     * @return void
     */
    public function setUp()
    {
        // Check if Redis extension is loaded
        if ( ! extension_loaded('redis'))
        {
            $this->markTestSkipped('Redis PHP Extension is not available');
        }

        parent::setUp();

        // Mock Redis Class (Required to test without having to configure and run a redis server)
        $stub = $this->createMock(Redis::class);

        // Always "establish" connection and auth successful
        $stub->method('connect')->willReturn(true);
        $stub->method('pconnect')->willReturn(true);
        $stub->method('auth')->willReturn(true);

        // Setting a value to cache always returns true
        $stub->method('mset')->willReturn(true);

        // Flush cache also returns true
        $stub->method('flushDB')->willReturn(true);

        // Set Return for delete method
        $stub->method('del')->will(
            $this->returnValueMap(
                [
                    [sha1('del_false'), 0],
                    [sha1('del_true'), 1],
                ]
            )
        );

        // Always Return 1 (Length)
        $stub->method('lPush')->willReturn(1);

        // Return example array for testing purposes
        $stub->method('lRange')->willReturn(['A', 'B']);

        // Return for "exist" functions, return null if tag is "find_null"
        $stub->method('exists')->will($this->returnCallback(
            function($tag) {
                return !($tag === '_tag:find_null');
            }
        ));

        // Iterate each provider and sha1 id and put it into mocked class as return Map with expected return value
        $get = [];
        foreach ($this->provider_set_get() as $provider) {
            $id = sha1($provider[0]['id']);
            $expect = $provider[1];
            $get[] = [$id, $expect];
            $expected[] = $expect;
        }
        $stub->method('get')->will(
            $this->returnValueMap(
                $get
            )
        );

        // Multiple ids get mocking
        $stub->method('mget')->willReturn($expected);

        // Give (fake) Testing Configuration to Redis Cache class
        $config = [
            'servers' => [
                'local' => [
                    'host'       => '127.0.0.1',
                    'port'       => 6379,
                    'persistent' => FALSE,
                    'prefix'     => '_prefix',
                    'password'   => 'password',
                ],
                'local2' => [
                    'host'       => '127.0.0.1',
                    'port'       => 6380,
                    'persistent' => TRUE,
                    'prefix'     => '',
                    'password'   => '',
                ],
            ],
            'cache_prefix' => 'prefix',
            'redis_mock'=> $stub
        ];
        $redis = new Cache_Redis($config);
        $this->cache($redis);
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
        if ( ! $cache->set('id', 'data', 0))
        {
            $this->fail('Unable to set cache value to delete!');
        }

        // Test delete returns number of elements deleted and we also check the value is gone
        $this->assertSame(1, $cache->delete('del_true'));
        $this->assertNull($cache->get('get_null'));

        // Test non-existant cache value returns 0 if no error
        $this->assertSame(0, $cache->delete('del_false'));
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

        // Get Wrong Tag
        $this->assertNull($cache->find('find_null'));

        // Set cache element with tag
        $this->assertTrue($cache->set_with_tags($id, $value, $ttl, array('testing')));

        // Find Tag
        $rows = $cache->find('testing');

        // Check if not empty
        $this->assertNotNull($rows);
    }

    /**
     * Always Returns true
     */
    public function test_delete_all()
    {
        // Test delete_all is successful
        $this->assertTrue($this->cache()->delete_all());
    }

    /**
     * Test set and get with arrays for redis
     */
    public function test_set_get_array()
    {
        // Initialize
        $cache = $this->cache();
        $providers = $this->provider_set_get();
        $ids = $values = $expected = [];
        foreach ($providers as $provider) {
            $id = $provider[0]['id'];
            $ids[] = $id;
            $values[] = $provider[0]['value'];
            $expected[$id] = $provider[1];
        }

        // Check setting multiple ids
        $this->assertTrue($cache->set($ids, $values));

        // Check getting multiple ids
        $this->assertEquals($cache->get($ids), $expected);
    }

    /**
     * Test Redis::delete_tag
     */
    public function test_delete_tag()
    {
        $cache = $this->cache();
        $this->assertTrue($cache->delete_tag('del_true'));
        $this->assertFalse($cache->delete_tag('find_null'));
    }

    /**
     * Rest without proper configuration
     * @throws Cache_Exception
     */
    public function test_invalid_configuration()
    {
        $this->expectException(Cache_Exception::class);
        new Cache_Redis([]);
    }

} // End Kohana_RedisTest
