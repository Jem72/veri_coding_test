<?php

include_once 'VeriCodingTestController.php';
Log::get_instance()->info('Starting Execution');

$controller = new VeriCodingTestController();
$controller->setupAttendance("data/attendance.csv");