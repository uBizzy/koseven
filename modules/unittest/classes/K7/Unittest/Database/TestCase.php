<?php

use PHPUnit\DbUnit\TestCase;

/**
 * TestCase for testing a database
 *
 * @package    K7/UnitTest
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
abstract class K7_Unittest_Database_TestCase extends TestCase{


	/**
	 * Make sure PHPUnit backs up globals
	 * @var boolean
	 */
	protected $backupGlobals = FALSE;

	/**
	 * A set of unittest helpers that are shared between normal / database
	 * testcases
	 * @var K7_Unittest_Helpers
	 */
	protected $_helpers = NULL;

	/**
	 * A default set of environment to be applied before each test
	 * @var array
	 */
	protected $environmentDefault = [];

	/**
	 * The k7 database connection that PHPUnit should use for this test
	 * @var string
	 */
	protected $_database_connection = 'default';

	/**
	 * Creates a predefined environment using the default environment
	 *
	 * Extending classes that have their own setUp() should call
	 * parent::setUp()
	 */
	public function setUp()
	{
		$this->_helpers = new K7_Unittest_Helpers;

		$this->setEnvironment($this->environmentDefault);

		return parent::setUp();
	}

	/**
	 * Restores the original environment overriden with setEnvironment()
	 *
	 * Extending classes that have their own tearDown()
	 * should call parent::tearDown()
	 */
	public function tearDown()
	{
		$this->_helpers->restore_environment();

		return parent::tearDown();
	}

	/**
	 * Creates a connection to the unittesting database
	 *
	 * @return PDO
	 */
	public function getConnection()
	{
		// Get the unittesting db connection
		$config = K7::$config->load('database.'.$this->_database_connection);

		if(strtolower($config['type']) !== 'pdo')
		{
			$config['connection']['dsn'] = strtolower($config['type']).':'.
			'host='.$config['connection']['hostname'].';'.
			'dbname='.$config['connection']['database'];
		}

		$pdo = new PDO(
			$config['connection']['dsn'],
			$config['connection']['username'],
			$config['connection']['password']
		);

		return $this->createDefaultDBConnection($pdo, $config['connection']['database']);
	}

    /**
     * Gets a connection to the unittest database
     *
     * @return K7_Database The database connection
     */
    public function getK7Connection()
    {
        return Database::instance(K7::$config->load('unittest')->db_connection);
    }

	/**
	 * Removes all k7 related cache files in the cache directory
	 */
	public function cleanCacheDir()
	{
		return K7_Unittest_Helpers::clean_cache_dir();
	}

	/**
	 * Helper function that replaces all occurences of '/' with
	 * the OS-specific directory separator
	 *
	 * @param string $path The path to act on
	 * @return string
	 */
	public function dirSeparator($path)
	{
		return K7_Unittest_Helpers::dir_separator($path);
	}

	/**
	 * Allows easy setting & backing up of enviroment config
	 *
	 * Option types are checked in the following order:
	 *
	 * * Server Var
	 * * Static Variable
	 * * Config option
	 *
	 * @param array $environment List of environment to set
	 */
	public function setEnvironment(array $environment)
	{
		return $this->_helpers->set_environment($environment);
	}

	/**
	 * Check for internet connectivity
	 *
	 * @return boolean Whether an internet connection is available
	 */
	public function hasInternet()
	{
		return K7_Unittest_Helpers::has_internet();
	}

}
