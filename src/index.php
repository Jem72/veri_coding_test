<?php

include_once 'ObjectCSVReader.php';

Log::get_instance()->info('Starting Execution');
$attendance = new ObjectCSVReader("data/attendance.csv");
print_r($attendance->getItemCount());
