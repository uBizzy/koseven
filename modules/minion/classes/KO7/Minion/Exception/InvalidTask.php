<?php

/**
 * Invalid Task Exception
 *
 * @package    KO7/Minion
 *
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE
 */
class KO7_Minion_Exception_InvalidTask extends Minion_Exception {

	public function format_for_cli()
	{
		return 'ERROR: '. $this->getMessage().PHP_EOL;
	}

}
