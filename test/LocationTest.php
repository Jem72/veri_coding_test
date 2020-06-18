<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 12:07
 */

use PHPUnit\Framework\TestCase;
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Location.php');

class LocationTest extends TestCase
{
	protected function setUp(): void
	{
		Log::get_instance()->disable();
	}

	function testValidString(): void
	{
		$location = new Location("(2, 42)");
		$this->assertEquals(true, $location->isValid(), "Valid Location Decoded");
		$this->assertSame(2, $location->x, 'Valid X decoded');
		$this->assertSame(42, $location->y, 'Valid Y decoded');
	}

	function testInvalidFormat(): void
	{
		$location = new Location("2, 42");
		$this->assertEquals(false, $location->isValid(), "Invalid Location should not have decoded");
	}

	function testInvalidDataX(): void
	{
		$location = new Location("(a2, 42)");
		$this->assertEquals(false, $location->isValid(), "Invalid x should not have decoded");
	}

	function testInvalidDataY(): void
	{
		$location = new Location("(2, 42g)");
		$this->assertEquals(false, $location->isValid(), "Invalid x should not have decoded");
	}

	public function testDistanceTo()
	{
		$location = new Location("(55, 42)");
		$secondLocation = clone $location;
		$distance = $location->distanceTo($secondLocation);

		$x_offset = clone $secondLocation;
		$x_offset->x += 5;
		$x_offset_distance = $location->distanceTo($x_offset);

		$y_offset = clone $secondLocation;
		$y_offset->y += 5;
		$y_offset_distance = $location->distanceTo($y_offset);

		$both_offset = clone $secondLocation;
		$both_offset->x += 5;
		$both_offset->y += 5;
		$both_offset_distance = $location->distanceTo($both_offset);
		$both_offset_expected = 5 * sqrt(2);

		$this->assertEquals(0, $distance, 'Same Location should be zero');
		$this->assertEquals(5.0, $x_offset_distance, 'X Offset is wrong');
		$this->assertEquals(5.0, $y_offset_distance, 'Y Offset is wrong');
		$this->assertEquals($both_offset_expected, $both_offset_distance, 'Both Offset distance is wrong');
	}
}
