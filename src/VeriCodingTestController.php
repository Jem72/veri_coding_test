<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:39
 */
include_once "ObjectCSVReader.php";
include_once "models/Location.php";
include_once "models/AttendanceRecord.php";

class VeriCodingTestController
{
	private $attendanceData;

	public function __construct()
	{
	}


	public function setupAttendance($fileName)
	{
		$attendance = new ObjectCSVReader($fileName);
		$record = $attendance->getItem(0);
		$object = new AttendanceRecord($record);
	}
}