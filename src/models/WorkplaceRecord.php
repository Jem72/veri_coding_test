<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:43
 */

include_once(dirname(__FILE__) . '/Location.php');


/**
 * Class AttendanceRecord - stores a single attendance record
 *
 * @property int      $id: The ID of the record
 * @property string   $name: The name of the record
 * @property Location $location: The location of the record
 * @property DateTime $dob: The DOB for the record
 * @property int      $workplace_id: Reference to the workplace for the record
 * @property string   $status: The status for the record
 *
 */
class WorkplaceRecord extends GenericRecord
{
	private static $source_keys = array('id', 'name', 'location');

	const MESSAGE_INVALID_DOB = "Invalid DOB value for record";
	const MESSAGE_INVALID_WORKPLACE_ID = "Invalid Workplace ID value for record";
	const MESSAGE_INVALID_STATUS = "Invalid Status value for record";

	public function __construct($arrayData)
	{
		$complete = $this->verifyComplete($arrayData, self::$source_keys);

		if(true == $complete)
		{
			$this->parseBasicRecordData($arrayData);
			$this->validate();
		}
	}

	public function isValid()
	{
		return $this->valid;
	}

	private function fail_record(string $message)
	{
		Log::get_instance()->warning($message);
		$this->valid = false;
		if(null == $this->failureMessage)
		{
			$this->failureMessage = $message;
		}
	}

	public function getFailureMessage()
	{
		return $this->failureMessage;
	}
}