<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-18
 * Time: 10:50
 */

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/Log.php');

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
	protected function setUp(): void
	{
		Log::get_instance()->enable();
	}


	public function testInstanceExists(): void
	{
		$instance = Log::get_instance();

		$this->assertNotNull($instance, 'Log instance is null');
		$this->assertInstanceOf("Log", $instance, 'Log instance is the wrong type');
	}

	public function testCanWriteWrite()
	{
		$oldSize = 0;
		$fileName = Log::get_instance()->getLogFileName();
		$this->assertNotNull($fileName,'File name is null');

		$exists = @file_exists($fileName);
		if(true == $exists)
		{
			$oldSize = @filesize($fileName);
			$this->assertTrue(@is_writable($fileName), "Log File is not writable");
		}

		Log::get_instance()->info("Testing Log Write");

		$exists = @file_exists($fileName);
		$this->assertTrue($exists, 'File does not exist after writing');

		// Needed for force an update
		@clearstatcache(false, $fileName);
		$newSize = @filesize($fileName);
		$this->assertGreaterThan($oldSize, $newSize,'The log file did not change size');
	}
}
