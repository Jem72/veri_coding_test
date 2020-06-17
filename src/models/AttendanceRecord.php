<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:43
 */

include_once(dirname(__FILE__) . '/GenericRecord.php');
include_once(dirname(__FILE__) . '/Payment.php');


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
class AttendanceRecord extends GenericRecord
{
	private static $source_keys = array('id', 'name', 'location', 'dob', 'workplace_id', 'status');

	const MESSAGE_INVALID_DOB = "Invalid DOB value for record";
	const MESSAGE_INVALID_WORKPLACE_ID = "Invalid Workplace ID value for record";
	const MESSAGE_INVALID_STATUS = "Invalid Status value for record";

	protected $dob;
	protected $workplace_id;
	protected $status;

	protected $payment_value = 0;

	public function __construct($arrayData)
	{
		$complete = $this->verifyComplete($arrayData, self::$source_keys);

		if(true == $complete)
		{
			$this->parseBasicRecordData($arrayData);

			$this->workplace_id = $arrayData['workplace_id'];
			try
			{
				$this->dob = new DateTime($arrayData['dob']);
			}
			catch(Exception $e)
			{
				$this->dob = null;
			}
			$this->status = $arrayData['status'];
			$this->validate();
		}
	}

	protected function validate(): bool
	{
		$this->valid = parent::validate();

		if($this->workplace_id <= 0)
		{
			$this->failRecord(self::MESSAGE_INVALID_WORKPLACE_ID);
		}

		if(null == $this->dob)
		{
			$this->failRecord(self::MESSAGE_INVALID_DOB);
		}

		if((null == $this->status) || (false === array_search($this->status, Payment::$valid_status)))
		{
			$this->failRecord(self::MESSAGE_INVALID_STATUS);
		}

		return $this->valid;
	}

	/**
	 * @param string $onDate
	 * @return int
	 */
	public function getAge($onDate = 'now'): int
	{
		$age = null;
		try
		{
			$now = new DateTime($onDate);
			$time_diff = $now->diff($this->dob);
			$age = $time_diff->y;
		}
		catch(Exception $e)
		{
			Log::get_instance()->error('Exception thrown getting age');
		}

		return $age;
	}

	/**
	 * Gets the workplace ID for the record
	 * @return int
	 */
	public function getWorkplaceID(): int
	{
		return $this->workplace_id;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setPaymentValue(float $value)
	{
		$this->payment_value = $value;
	}

	public function getPaymentValue(): float
	{
		return round($this->payment_value, 2);
	}
}