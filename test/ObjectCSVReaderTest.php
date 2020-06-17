<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 10:49
 */

use PHPUnit\Framework\TestCase;

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/ObjectCSVReader.php');

/**
 * @property ObjectCSVReader $attendanceReader
 * @property ObjectCSVReader $workplaceReader
 */
class ObjectCSVReaderTest extends TestCase
{
	private $attendanceReader;
	private $workplaceReader;

	protected function setUp(): void
	{
		Log::get_instance()->disable();
		$this->attendanceReader = new ObjectCSVReader(__DIR__ . '/../data/attendance.csv');
		$this->workplaceReader = new ObjectCSVReader(__DIR__ . '/../data/workplaces.csv');
	}


	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf(
			ObjectCSVReader::class, $this->attendanceReader
		);
	}


	public function testFilesLoaded(): void
	{
		$attendanceLoaded = $this->attendanceReader->isFileLoaded();
		$workplaceLoaded = $this->workplaceReader->isFileLoaded();
		echo("Loaded\n");
		$this->assertEquals(true, $attendanceLoaded, "Attendance File not opened");
		$this->assertEquals(true, $workplaceLoaded, "Workplace File not opened");
	}

	public function testItemCount(): void
	{
		$attendanceCount = $this->attendanceReader->getItemCount();
		$workplaceCount = $this->workplaceReader->getItemCount();
		$attendanceLines = $this->attendanceReader->getLineCount();
		$workplaceLines = $this->workplaceReader->getLineCount();
		$this->assertEquals( $attendanceLines-1, $attendanceCount, 'Unexpected attendance item count');
		$this->assertEquals($workplaceLines-1, $workplaceCount,'Unexpected workplace item count');
	}

	public function testAttendanceFields(): void
	{
		$expectedFields = array('id','name','location','dob','workplace_id','status');

		$objectFields = $this->attendanceReader->getFieldNames();

		foreach($expectedFields as $expectedField)
		{
			$this->assertContains($expectedField, $objectFields, 'Could not find workplace field');
		}
	}

	public function testWorkplaceFields(): void
	{
		$expectedFields = array('id','name','location');

		$objectFields = $this->workplaceReader->getFieldNames();

		foreach($expectedFields as $expectedField)
		{
			$this->assertContains($expectedField, $objectFields, 'Could not find workplace field');
		}
	}

}
