<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 13:39
 */

use PHPUnit\Framework\TestCase;

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Workplace.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Location.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/Log.php');

Log::get_instance()->disable();

class WorkplaceTest extends TestCase
{
	private $standardData = array('id' => '10', 'name' => 'Walker, Johnson and Knight', 'location' => '(22, 23)');

	public function testValidRecord(): void
	{
		$testData = $this->standardData;
		$record = new Workplace($testData);

		$this->assertEquals(true, $record->isValid(), 'Invalid Workplace: ' . $record->getFailureMessage());
	}

	public function testRecordMissingData()
	{
		$testData = $this->standardData;
		unset($testData['id']);
		$record = new Workplace($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertStringContainsString(Workplace::MESSAGE_MISSING_DATA, $failure_message, 'Wrong detection of missing data');
	}

	public function testBadID(): void
	{
		$testData = $this->standardData;
		$testData['id'] = '-1';
		$record = new Workplace($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Workplace::MESSAGE_INVALID_ID, $failure_message, 'Wrong detection of bad ID');
	}

	public function testBadName(): void
	{
		$testData = $this->standardData;
		$testData['name'] = null;
		$record = new Workplace($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Workplace::MESSAGE_INVALID_NAME, $failure_message, 'Wrong detection of bad name');
	}

	public function testBadLocation(): void
	{
		$testData = $this->standardData;
		$testData['location'] = 'Longford';
		$record = new Workplace($testData);
		$failure_message = $record->getFailureMessage();

		$this->assertEquals(false, $record->isValid(), 'Record should hae been invalid');
		$this->assertEquals(Workplace::MESSAGE_INVALID_LOCATION, $failure_message, 'Wrong detection of bad location');
	}

	public function testDistanceTo()
	{
		$testData = $this->standardData;
		$record = new Workplace($testData);
		$secondLocation = $record->getLocation();
		$distance = $record->distanceTo($secondLocation);
		$both_offset = clone $secondLocation;
		$both_offset->x += 5;
		$both_offset->y += 5;
		$both_offset_distance = $record->distanceTo($both_offset);
		$both_offset_expected = 5 * sqrt(2);
		$x_offset = clone $secondLocation;
		$x_offset->x += 5;
		$x_offset_distance = $record->distanceTo($x_offset);
		$y_offset = clone $secondLocation;
		$y_offset->y += 5;
		$y_offset_distance = $record->distanceTo($y_offset);

		$this->assertEquals(0, $distance, 'Same Location should be zero');
		$this->assertEquals(5.0, $x_offset_distance, 'X Offset is wrong');
		$this->assertEquals(5.0, $y_offset_distance, 'Y Offset is wrong');
		$this->assertEquals($both_offset_expected, $both_offset_distance, 'Both Offset distance is wrong');
	}
}
