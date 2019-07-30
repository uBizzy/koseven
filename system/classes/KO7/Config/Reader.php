<?php

/**
 * Interface for config readers
 *
 * @package    KO7
 * @category   Configuration
 * 
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE
 */
interface KO7_Config_Reader extends KO7_Config_Source
{

	/**
	 * Tries to load the specified configuration group
	 *
	 * Returns FALSE if group does not exist or an array if it does
	 *
	 * @param  string $group Configuration group
	 * @return boolean|array
	 */
	public function load($group);

}
