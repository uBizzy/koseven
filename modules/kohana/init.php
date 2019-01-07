<?php
/**
 * Set K7::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant K7::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	K7::$environment = constant('K7::'.strtoupper($_SERVER['KOSEVEN_ENV']));
}

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
    define('KOHANA_START_TIME', K7_START_TIME);
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
    define('KOHANA_START_MEMORY', K7_START_MEMORY);
}
