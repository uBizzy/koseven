<?php
return [
/*	
    'default' => 'file',                            // allows to specify default cache directl from config file
    'prefix'  => 'cache1_',                          //used to avoid duplicates when using _sanitize_id
    'apcu'   => array(
        'driver'             => 'apcu',
        'default_expire'     => 3600,
        'prefix'             => 'cache_apcu_',       // if set uses this prefix instead of global 'prefix'
    ),
	'wincache' => array(
		'driver'             => 'wincache',
		'default_expire'     => 3600,
	),
	'sqlite'   => array(
		'driver'             => 'sqlite',
		'default_expire'     => 3600,
		'database'           => APPPATH.'cache/kohana-cache.sql3',
		'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
	),
	'eaccelerator'           => array(
		'driver'             => 'eaccelerator',
	),
	'xcache'   => array(
		'driver'             => 'xcache',
		'default_expire'     => 3600,
	),
	'file'    => array(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	)
*/
];
