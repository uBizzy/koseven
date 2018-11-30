<?php
return [
/*
    'default' => 'file',                            // allows to specify default cache directl from config file
    'prefix'  => 'cache1_',                          //used to avoid duplicates when using _sanitize_id
    'apcu'   => [
        'driver'             => 'apcu',
        'default_expire'     => 3600,
        'prefix'             => 'cache_apcu_',       // if set uses this prefix instead of global 'prefix'
    ],
    'memcache' => [
		'driver'             => 'memcache',
		'default_expire'     => 3600,
		'compression'        => FALSE,              // Use Zlib compression (can cause issues with integers)
		'servers'            => array(
			'local' => array(
				'host'             => 'localhost',  // Memcache Server
				'port'             => 11211,        // Memcache port number
				'persistent'       => FALSE,        // Persistent connection
				'weight'           => 1,
				'timeout'          => 1,
				'retry_interval'   => 15,
				'status'           => TRUE,
			),
		),
		'instant_death'      => TRUE,               // Take server offline immediately on first fail (no retry)
	],
	'memcachetag' => [
		'driver'             => 'memcachetag',
		'default_expire'     => 3600,
		'compression'        => FALSE,              // Use Zlib compression (can cause issues with integers)
		'servers'            => array(
			'local' => array(
				'host'             => 'localhost',  // Memcache Server
				'port'             => 11211,        // Memcache port number
				'persistent'       => FALSE,        // Persistent connection
				'weight'           => 1,
				'timeout'          => 1,
				'retry_interval'   => 15,
				'status'           => TRUE,
			),
		),
		'instant_death'      => TRUE,
	],
	'wincache' => [
		'driver'             => 'wincache',
		'default_expire'     => 3600,
	],
	'sqlite'   => [
		'driver'             => 'sqlite',
		'default_expire'     => 3600,
		'database'           => APPPATH.'cache/kohana-cache.sql3',
		'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
	],
	'eaccelerator'           => [
		'driver'             => 'eaccelerator',
	],
	'xcache'   => [
		'driver'             => 'xcache',
		'default_expire'     => 3600,
	],
	'file'    => [
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	],
    'redis' => [
        'driver'             => 'redis',
        'default_expire'     => 3600,
        'cache_prefix'       => 'cache',
        'tag_prefix'         => '_tag',
        'servers' => [
            'local' => [
                //'host'     => 'unix:///var/run/redis/redis.sock',
                'host'       => 'localhost',
                'port'       => 6379,
                'persistent' => FALSE,
                'prefix'     => '',
                'password'   => '',
            ],
        ],
    ]
*/
];
