<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:39
 */


/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 1) . '/ObjectCSVReader.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 1) . '/models/Location.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 1) . '/models/Attendance.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 1) . '/models/Workplace.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 1) . '/models/Employee.php');

/**
 * Class VeriCodingTestController
 *
 * @property Workplace[]  $workplaceData
 * @property Attendance[] $attendanceData
 */
class VeriCodingTestController
{
	private $attendanceData;
	private $workplaceData;

	public function __construct()
	{
		$this->attendanceData = array();
		$this->workplaceData = array();
	}

	public function setAttendanceFile(string $fileName): bool
	{
		$ok = false;
		$this->attendanceData = array();
		/** @var ObjectCSVReader $reader */
		$reader = $this->loadFile($fileName);

		if(null != $reader)
		{
			$ok = true;
			$count = $reader->getItemCount();
			for($index = 0; $index < $count; $index++)
			{
				$item = $reader->getItem($index);
				if(null != $item)
				{
					$object = new Attendance($item);
					if(true == $object->isValid())
					{
						$this->attendanceData[] = $object;
					}
				}
			}
		}

		return $ok;
	}


	public function setWorkplaceFile(string $fileName): bool
	{
		$ok = false;
		$this->workplaceData = array();
		/** @var ObjectCSVReader $reader */
		$reader = $this->loadFile($fileName);

		if(null != $reader)
		{
			$ok = true;
			$count = $reader->getItemCount();
			for($index = 0; $index < $count; $index++)
			{
				$item = $reader->getItem($index);
				if(null != $item)
				{
					$object = new Workplace($item);
					if(true == $object->isValid())
					{
						$this->workplaceData[] = $object;
					}
				}
			}
		}

		return $ok;
	}

	private function loadFile(string $fileName): ?ObjectCSVReader
	{
		$attendance = new ObjectCSVReader($fileName);
		if(false == $attendance->isFileLoaded())
		{
			Log::get_instance()->error("Failed to load file: " . $fileName);
			return null;
		}
		if(false == $attendance->isValid())
		{
			Log::get_instance()->error("Invalid file data: " . $fileName);
			return null;
		}

		return $attendance;
	}

	public function processData()
	{
		Log::get_instance()->info('Process Data');
		Log::get_instance()->info('Workplace records: ' . count($this->workplaceData));
		Log::get_instance()->info('Attendance records: ' . count($this->attendanceData));

		if((0 === count($this->workplaceData)) || (0 === count($this->attendanceData)))
		{
			Log::get_instance()->error("Cannot process data as one of the sources is empty");
			return;
		}
		else
		{
			foreach($this->attendanceData as $attendanceRecord)
			{
				$this->processAttendanceRecord($attendanceRecord);
			}

			/** @var Employee[] $employeeData */
			$employeeData = array();
			foreach($this->attendanceData as $index => $attendanceRecord)
			{
				$id = $attendanceRecord->getID();
				$name = $attendanceRecord->getName();
				$dob = $attendanceRecord->getDOB();
				$location = $attendanceRecord->getLocation();
				$payment = $attendanceRecord->getPaymentValue();

				if(false === array_key_exists($id, $employeeData))
				{
					$employee = new Employee($id, $name, $location, $dob, $payment);
					$employeeData[$id] = $employee;
				}
				else
				{
					$employee = $employeeData[$id];
					if(true === $employee->verifyMatch($id, $name, $location, $dob))
					{
						$employee->addPayment($payment);
					}
					else
					{
						$message = "Employee data mismatch at index: " . $index;
						Log::get_instance()->error($message);
						print($message . "\n");
						exit;
					}
				}
			}
			ksort($employeeData);

			printf("id,payout\n");
			foreach($employeeData as $index => $datum)
			{
				printf("%d, %.02f\n", $datum->getID(), $datum->getPayment());
			}
		}
	}

	private function getWorkplaceForID(int $id)
	{
		$match = null;

		foreach($this->workplaceData as $workplace)
		{
			if($workplace->getID() === $id)
			{
				$match = $workplace;
				break;
			}
		}
		return $match;
	}

	private function processAttendanceRecord(Attendance $attendance)
	{
		$workplaceID = $attendance->getWorkplaceID();
		$workplace = $this->getWorkplaceForID($workplaceID);

		$distance = $attendance->distanceTo($workplace->getLocation());
		$age = $attendance->getAge();
		$status = $attendance->getStatus();

		$payment = new Payment($age, $status, $distance);
		$attendance->setPaymentValue($payment->getTotal());
	}

}