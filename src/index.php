<?php

include_once 'VeriCodingTestController.php';
Log::get_instance()->info('Starting Execution');

$controller = new VeriCodingTestController();
$controller->setAttendanceFile("data/attendance.csv");
$controller->setWorkplaceFile("data/workplaces.csv");

$controller->processData();