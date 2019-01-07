<?php

/**
 * Invalid Task Exception
 *
 * @package    K7/Minion
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE.md
 */
class K7_Minion_Exception_InvalidTask extends Minion_Exception {

	public function format_for_cli()
	{
		return 'ERROR: '. $this->getMessage().PHP_EOL;
	}

}
