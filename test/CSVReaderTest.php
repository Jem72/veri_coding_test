<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-15
 * Time: 19:34
 */

use PHPUnit\Framework\TestCase;


/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/CSVReader.php');

/**
 * @property CSVReader $attendanceReader
 * @property CSVReader $workplaceReader
 */
class CSVReaderTest extends TestCase
{
	private $attendanceReader;
	private $workplaceReader;

	protected function setUp(): void
	{
		Log::get_instance()->disable();
		$this->attendanceReader = new CSVReader(__DIR__ . '/../data/attendance.csv');
		$this->workplaceReader = new CSVReader(__DIR__ . '/../data/workplaces.csv');
	}


	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf(
			CSVReader::class, $this->attendanceReader
		);
	}


	public function testFilesLoaded(): void
	{
		$attendanceLoaded = $this->attendanceReader->isFileLoaded();
		$workplaceLoaded = $this->workplaceReader->isFileLoaded();
		$this->assertEquals(true, $attendanceLoaded, "Attendance File not opened");
		$this->assertEquals(true, $workplaceLoaded, "Workplace File not opened");
	}

	public function testItemCount(): void
	{
		$attendanceCount = $this->attendanceReader->getItemCount();
		$workplaceCount = $this->workplaceReader->getItemCount();
		$this->assertEquals(451, $attendanceCount, 'Unexpected attendance item count');
		$this->assertEquals(11, $workplaceCount, 'Unexpected workplace item count');
	}
}
