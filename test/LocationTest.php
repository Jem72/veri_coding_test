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
}
