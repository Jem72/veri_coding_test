<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 13:39
 */

use PHPUnit\Framework\TestCase;

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Attendance.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Location.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/Log.php');

class AttendanceTest extends TestCase
{
	private $standardData = array('id' => '3', 'name' => 'Lisa Paul', 'location' => '(7, 17)', 'dob' => '1972-04-20',
											'workplace_id' => '7', 'status' => 'AL');

	protected function setUp(): void
	{
		Log::get_instance()->disable();
	}

	public function testValidRecord(): void
	{
		$testData = $this->standardData;
		$record = new Attendance($testData);

		$this->assertEquals(true, $record->isValid(), 'Invalid Attendance: ' . $record->getFailureMessage());
	}

	public function testRecordMissingData()
	{
		$testData = $this->standardData;
		unset($testData['id']);
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertStringContainsString(Attendance::MESSAGE_MISSING_DATA, $failure_message, 'Wrong detection of missing data');
	}

	public function testBadID(): void
	{
		$testData = $this->standardData;
		$testData['id'] = '-1';
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Attendance::MESSAGE_INVALID_ID, $failure_message, 'Wrong detection of bad ID');
	}

	public function testBadName(): void
	{
		$testData = $this->standardData;
		$testData['name'] = null;
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Attendance::MESSAGE_INVALID_NAME, $failure_message, 'Wrong detection of bad name');
	}

	public function testBadLocation(): void
	{
		$testData = $this->standardData;
		$testData['location'] = 'Longford';
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Attendance::MESSAGE_INVALID_LOCATION, $failure_message, 'Wrong detection of bad location');
	}

	public function testBadDOB(): void
	{
		$testData = $this->standardData;
		$testData['dob'] = 'dsfnsdjf';
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Attendance::MESSAGE_INVALID_DOB, $failure_message, 'Wrong detection of bad DOB');
	}

	public function testBadWorkplaceID(): void
	{
		$testData = $this->standardData;
		$testData['workplace_id'] = -1;
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Attendance::MESSAGE_INVALID_WORKPLACE_ID, $failure_message, 'Wrong detection of bad workplace ID');
	}

	public function testBadStatus(): void
	{
		$testData = $this->standardData;
		$testData['status'] = 'GT';
		$record = new Attendance($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Status Record should hae been invalid');
		$this->assertEquals(Attendance::MESSAGE_INVALID_STATUS, $failure_message, 'Wrong detection of bad status');
	}

	public function testAge(): void
	{
		$testData = $this->standardData;
		$record = new Attendance($testData);
		$age = $record->getAge('2020-06-16');

		$this->assertEquals(48, $age,'Person should be 48 on June 16');
		$age = $record->getAge('2020-04-20');
		$this->assertEquals(48, $age,'Person should be 48 on April 20');
		$age = $record->getAge('2020-04-19');
		$this->assertEquals(47, $age,'Person should be 48 on April 19');


		try
		{
			$time = new DateTime(date('Y-m-d',strtotime('-6 years')));
			$testData['dob'] = $time->format('Y-m-d');
			$alteredRecord = new Attendance($testData);
			$age = $alteredRecord->getAge();
			$this->assertEquals(6, $age,'Person should be 6');
		}
		catch(Exception $e)
		{
			$this->fail('Exception generating age: ' . print_r($e->getMessage()));
		}
	}

	public function testDistanceTo()
	{
		$testData = $this->standardData;

		$record = new Attendance($testData);
		$secondLocation = $record->getLocation();
		$distance = $record->distanceTo($secondLocation);

		$x_offset = clone $secondLocation;
		$x_offset->x += 5;
		$x_offset_distance = $record->distanceTo($x_offset);

		$y_offset = clone $secondLocation;
		$y_offset->y += 5;
		$y_offset_distance = $record->distanceTo($y_offset);

		$both_offset = clone $secondLocation;
		$both_offset->x += 5;
		$both_offset->y += 5;
		$both_offset_distance = $record->distanceTo($both_offset);
		$both_offset_expected = 5 * sqrt(2);

		$this->assertEquals(0, $distance, 'Same Location should be zero');
		$this->assertEquals(5.0, $x_offset_distance, 'X Offset is wrong');
		$this->assertEquals(5.0, $y_offset_distance, 'Y Offset is wrong');
		$this->assertEquals($both_offset_expected, $both_offset_distance, 'Both Offset distance is wrong');
	}
}
