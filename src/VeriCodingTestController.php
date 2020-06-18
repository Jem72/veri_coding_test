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

	/**
	 * Set the name of the attendance file
	 * @param string $fileName
	 * @return bool
	 */
	public function setAttendanceFile(string $fileName): bool
	{
		$ok = false;
		$this->attendanceData = array();
		/** @var ObjectCSVReader $reader */
		$reader = $this->loadFile($fileName);

		if(null != $reader)
		{
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
			$ok = $reader->isValid();
		}

		return $ok;
	}


	/**
	 * Set teh name of the workpalce file
	 * @param string $fileName
	 * @return bool
	 */
	public function setWorkplaceFile(string $fileName): bool
	{
		$ok = false;
		$this->workplaceData = array();
		/** @var ObjectCSVReader $reader */
		$reader = $this->loadFile($fileName);

		if(null != $reader)
		{
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
			$ok = $reader->isValid();
		}

		return $ok;
	}

	/**
	 * Reads a CSV file where the first row contains column names. Returns the reader object or null if the file
	 * is invalid
	 * @param string $fileName
	 * @return ObjectCSVReader|null
	 */
	private function loadFile(string $fileName): ?ObjectCSVReader
	{
		$reader = new ObjectCSVReader($fileName);
		if(false == $reader->isFileLoaded())
		{
			Log::get_instance()->error("Failed to load file: " . $fileName);
			return null;
		}
		if(false == $reader->isValid())
		{
			Log::get_instance()->error("Invalid file data: " . $fileName);
			return null;
		}

		return $reader;
	}

	/**
	 * Executes the processing tasks - works out the total payment for each employee and outputs it to stdio
	 */
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
			$ok = true;
			foreach($this->attendanceData as $attendanceRecord)
			{
				$ok &= $this->calculateAttendancePayment($attendanceRecord);
			}

			if(false == $ok)
			{
				print("One or more attendance records couldn't be processed. See log for more details\n");
				exit;
			}
			else
			{
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
	}

	/**
	 * Finds the workplace with matching ID
	 * @param int $id
	 * @return Workplace|null
	 */
	private function getWorkplaceForID(int $id): ?Workplace
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

	/**
	 * Calculates the payment for an attendance record and stores it back into the record
	 * @param Attendance $attendance
	 * @return bool - false for failure
	 */
	private function calculateAttendancePayment(Attendance $attendance): bool
	{
		$workplaceID = $attendance->getWorkplaceID();
		$workplace = $this->getWorkplaceForID($workplaceID);

		if(null !== $workplace)
		{
			$ok = true;
			$distance = $attendance->distanceTo($workplace->getLocation());
			$age = $attendance->getAge();
			$status = $attendance->getStatus();

			$payment = new Payment($age, $status, $distance);
			$attendance->setPaymentValue($payment->getTotal());
		}
		else
		{
			$ok = false;
			Log::get_instance()->error("Failed to locate workplace for attendance record:" . $attendance->getID());
		}

		return $ok;
	}

}