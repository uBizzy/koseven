<?php
/**
 * File-based configuration reader. Multiple configuration directories can be
 * used by attaching multiple instances of this class to [KO7_Config].
 *
 * @package    KO7
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class KO7_Config_File_Reader implements KO7_Config_Reader {

	/**
	 * The directory where config files are located
	 * @var string
	 */
	protected $_directory = '';

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
	 * @return  $this   current object
	 * @uses    KO7::load
	 */
	public function load($group)
	{
		$config = [];

		if ($files = KO7::find_file($this->_directory, $group, NULL, TRUE))
		{
			foreach ($files as $file)
			{
				// Merge each file to the configuration array
				$config = Arr::merge($config, KO7::load($file));
			}
		}

		return $config;
	}

}
