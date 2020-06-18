<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-15
 * Time: 19:17
 */

/**
 * Class Log
 * A minimal logger for debugging the output. Just writes simple messages to a text log file
 */
class Log
{
	protected static $instance = null;
	private $log_file = null;
	private $enabled = true;

	public function __construct()
	{
		$this->log_file = dirname(__FILE__,2) . '/log/coding-test.log';
	}

	public function getLogFileName()
	{
		return $this->log_file;
	}

	/**
	 * @return Log
	 */
	public static function get_instance()
	{
		if(null === static::$instance)
		{
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Log an info level message
	 * @param string $message
	 */
	public function info(string $message)
	{
		$this->saveMessage('INFO', $message);
	}

	/**
	 * Log a warning level message
	 * @param string $message
	 */
	public function warning(string $message): void
	{
		$this->saveMessage('WARNING', $message);
	}

	/**
	 * Log an error level message
	 * @param string $message
	 */
	public function error(string $message): void
	{
		$this->saveMessage('ERROR', $message);
	}

	/**
	 * Save the message to the log file
	 * @param string $prefix
	 * @param string $message
	 */
	private function saveMessage(string $prefix, string $message)
	{
		if(true == $this->enabled)
		{
			$file = fopen($this->log_file, "a");

			if(FALSE !== $file)
			{
				$timestamp = date('Y-m-d H:i:s,v');
				$message = "\n" . $prefix . ' ' . $timestamp . " " . $message;
				fwrite($file, $message);
				fclose($file);
			}
		}
	}

	public function disable()
	{
		$this->enabled = false;
	}

	public function enable()
	{
		$this->enabled = true;
	}
}