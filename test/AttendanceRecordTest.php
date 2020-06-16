<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 13:39
 */

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__, 2) . '/src/models/AttendanceRecord.php');
include_once(dirname(__FILE__, 2) . '/src/models/Location.php');
include_once(dirname(__FILE__, 2) . '/src/Log.php');

Log::get_instance()->disable();

class AttendanceRecordTest extends TestCase
{
	private $standardData = array('id' => '3', 'name' => 'Lisa Paul', 'location' => '(7, 17)', 'dob' => '1972-04-20',
											'workplace_id' => '7', 'status' => 'AL');

	public function testValidRecord(): void
	{
		$testData = $this->standardData;
		$record = new AttendanceRecord($testData);

		$this->assertEquals(true, $record->isValid(), 'Invalid Attendance: ' . $record->getFailureMessage());
	}

	public function testRecordMissingData()
	{
		$testData = $this->standardData;
		unset($testData['id']);
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_MISSING_DATA, $failure_message, 'Wrong detection of missing data');
	}

	public function testBadID(): void
	{
		$testData = $this->standardData;
		$testData['id'] = '-1';
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_INVALID_ID, $failure_message, 'Wrong detection of bad ID');
	}

	public function testBadName(): void
	{
		$testData = $this->standardData;
		$testData['name'] = null;
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_INVALID_NAME, $failure_message, 'Wrong detection of bad name');
	}

	public function testBadLocation(): void
	{
		$testData = $this->standardData;
		$testData['location'] = 'Longford';
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_INVALID_LOCATION, $failure_message, 'Wrong detection of bad location');
	}

	public function testBadDOB(): void
	{
		$testData = $this->standardData;
		$testData['dob'] = 'dsfnsdjf';
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_INVALID_DOB, $failure_message, 'Wrong detection of bad DOB');
	}

	public function testBadWorkplaceID(): void
	{
		$testData = $this->standardData;
		$testData['workplace_id'] = -1;
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_INVALID_WORKPLACE_ID, $failure_message, 'Wrong detection of bad workplace ID');
	}

	public function testBadStatus(): void
	{
		$testData = $this->standardData;
		$testData['status'] = 'GT';
		$record = new AttendanceRecord($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Status Record should hae been invalid');
		$this->assertEquals(AttendanceRecord::MESSAGE_INVALID_STATUS, $failure_message, 'Wrong detection of bad status');
	}

	public function testAge(): void
	{
		$testData = $this->standardData;
		$record = new AttendanceRecord($testData);
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
			$alteredRecord = new AttendanceRecord($testData);
			$age = $alteredRecord->getAge();
			$this->assertEquals(6, $age,'Person should be 6');
		}
		catch(Exception $e)
		{
			$this->fail('Exception generating age: ' . print_r($e->getMessage()));
		}

	}
}
