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
include_once "models/WorkplaceRecord.php";

/**
 * Class VeriCodingTestController
 *
 * @property WorkplaceRecord[]  $workplaceData
 * @property AttendanceRecord[] $attendanceData
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
				$object = new AttendanceRecord($item);
				if(true == $object->isValid())
				{
					$this->attendanceData[] = $object;
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
				$object = new WorkplaceRecord($item);
				if(true == $object->isValid())
				{
					$this->workplaceData[] = $object;
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

			$consolidatedData = array();
			foreach($this->attendanceData as $attendanceRecord)
			{
				$recordID = $attendanceRecord->getID();
				if(false === array_key_exists($recordID, $consolidatedData))
				{
					$consolidatedData[$recordID] = array('id' => $recordID);
					$consolidatedData[$recordID]['name'] = $attendanceRecord->getName();
					$consolidatedData[$recordID]['payment'] = $attendanceRecord->getPaymentValue();
					$consolidatedData[$recordID]['count'] = 1;
				}
				else
				{
					$consolidatedData[$recordID]['payment'] += $attendanceRecord->getPaymentValue();
					$consolidatedData[$recordID]['count']++;
				}
			}

			$records = $this->getAttendancesForID(1);

			foreach($records as $record)
			{
				$location = $record->getLocation();
				printf("%d,%d,%s,%s,%d,%d,%.02f\n", $record->getID(), $record->getWorkplaceID(), $record->getName(), $record->getStatus(), $location->x, $location->y, $record->getPaymentValue());
			}
//			ksort($consolidatedData);
//			printf("id,payout\n");
//			foreach($consolidatedData as $datum)
//			{
//				printf("%d, %.02f\n", $datum['id'], $datum['payment']);
//			}
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

	/**
	 * @param int $id
	 * @return AttendanceRecord[]
	 */
	private function getAttendancesForID(int $id): array
	{
		$matches = array();

		foreach($this->attendanceData as $attendance)
		{
			if($attendance->getID() === $id)
			{
				$matches[] = $attendance;
			}
		}
		return $matches;
	}

	private function processAttendanceRecord(AttendanceRecord $attendance)
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