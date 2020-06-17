<?php

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 1) . '/VeriCodingTestController.php');

Log::get_instance()->info('Starting Execution');

$controller = new VeriCodingTestController();
$controller->setAttendanceFile(dirname(__FILE__, 2) . "/data/attendance.csv");
$controller->setWorkplaceFile(dirname(__FILE__, 2) . "/data/workplaces.csv");
$controller->processData();