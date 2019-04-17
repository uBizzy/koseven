<?php
/**
 * KO7 error exception class.
 * 
 * @package    KO7
 * @category   Exceptions
 * @author     Kohana Team
 * @copyright  (c) Kohana Team
 * @license    https://koseven.ga/LICENSE
 */
class KO7_Error_Exception extends ErrorException {

	/**
	 * Creates a new translated exception.
	 *
	 * @param   string    $message    Error message
	 * @param   integer   $code       The error code
	 * @param   integer   $severity   The severity level of the error.
	 * @param   string    $file 	  The filename where the error is thrown
	 * @param   integer   $line	  The line number where the error is thrown
	 * @return  void
	 */
	public function __construct($message, $code, $severity, $file, $line)
	{
		if ($message)
		{
			// Translate the message
			$message = I18n::get($message);
		}
		// Init parent construct
		parent::__construct($message, $code, $severity, $file, $line);
	}
}
