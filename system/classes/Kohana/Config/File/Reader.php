<?php
/**
 * File-based configuration reader. Multiple configuration directories can be
 * used by attaching multiple instances of this class to [Kohana_Config].
 *
 * @package    Kohana
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class Kohana_Config_File_Reader implements Kohana_Config_Reader {

	/**
	 * The directory where config files are located
	 * @var string
	 */
	protected $_directory = '';

	/**
	 * Cached Configurations
	 * @var array
	 */
	protected static $_cache;

	/**
	 * Creates a new file reader using the given directory as a config source
	 *
	 * @param string    $directory  Configuration directory to search
	 */
	public function __construct($directory = 'config')
	{
		// Set the configuration directory name
		$this->_directory = trim($directory, '/');
	}

	/**
	 * Load and merge all of the configuration files in this group.
	 *
	 *     $config->load($name);
	 *
	 * @param   string  $group  configuration group name
	 *
	 * @return  array   Configuration
	 * @throws Kohana_Exception
	 */
	public function load($group) : array
	{
		// Check caches and start Profiling
		if (Kohana::$caching === TRUE && isset(self::$_cache[$group]))
		{
			// This group has been cached
			return self::$_cache[$group];
		}

		if (Kohana::$profiling === TRUE && class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Config', __FUNCTION__);
		}

		// Init
		$config = [];

		// Loop through paths. Notice: array_reverse, so system files get overwritten by app files
		foreach (array_reverse(Kohana::include_paths()) as $path) {

			// Build path
			$file = $path.'config'. DIRECTORY_SEPARATOR . $group;
			$value = FALSE;

			// Try .php .json and .yaml extensions and parse contents with PHP support
			if (file_exists($path = $file.'.php')) {
				$value = Kohana::load($path);
			} elseif (file_exists($path = $file.'.json')) {
				$value = json_decode($this->read_from_ob($path), true);
			} elseif (file_exists($path = $file.'.yaml')) {
				if ( ! extension_loaded('yaml')) {
					throw new Kohana_Exception('PECL Yaml Extension is required in order to parse YAML Config');
				}
				$value = yaml_parse($this->read_from_ob($path));
			}

			// Merge config
			$config = $value !== FALSE ? $config = Arr::merge($config, $value) : [];
		}

		if (Kohana::$caching === TRUE)
		{
			self::$_cache[$group] = $config;
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $config;
	}

	/**
	 * Read Contents from file with output buffering.
	 * Used to support <?php ?> tags and code inside Configurations
	 *
	 * @param  string $path Path to File
	 *
	 * @return false|string
	 */
	protected function read_from_ob($path)
	{
		// Start output buffer
		ob_start();

		include_once $path;

		// Get contents of buffer
		$content = ob_get_contents();

		// Clear Buffer
		ob_end_clean();

		return $content;
	}
}
