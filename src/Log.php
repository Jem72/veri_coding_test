<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-15
 * Time: 19:17
 */


class Log
{
	protected static $instance = null;
	private $log_file = null;
	private $enabled = true;

	public function __construct()
	{
		$this->log_file = __DIR__ . '/../log/coding-test.log';
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

	public function info(string $message)
	{
		$this->saveMessage('INFO', $message);
	}

	public function error(string $message): void
	{
		$this->saveMessage('ERROR', $message);
	}

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
			else
			{
				print("FAILED TO OPEN LOG FILE: " . $this->log_file);
			}
		}
	}

	public function disable()
	{
		$this->enabled = false;
	}
}