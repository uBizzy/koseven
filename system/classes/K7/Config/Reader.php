<?php

/**
 * Interface for config readers
 *
 * @package    K7
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
interface K7_Config_Reader extends K7_Config_Source
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
