<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:43
 */

include_once(dirname(__FILE__) . '/Generic.php');
include_once(dirname(__FILE__) . '/Payment.php');


/**
 * Class Attendance - stores a single attendance record
 *
 * @property DateTime $dob: The DOB for the record
 * @property int      $workplace_id: Reference to the workplace for the record
 * @property string   $status: The status for the record
 * @property float    $paymentValue: The value of the payment for the record
 *
 */
class Attendance extends Generic
{
	const ATTRIBUTE_WORKPLACE_ID = 'workplace_id';
	const ATTRIBUTE_DOB = 'dob';
	const ATTRIBUTE_STATUS = 'status';
	private static $source_keys = array(self::ATTRIBUTE_ID, self::ATTRIBUTE_NAME, self::ATTRIBUTE_LOCATION,
													self::ATTRIBUTE_DOB, self::ATTRIBUTE_WORKPLACE_ID, self::ATTRIBUTE_STATUS);

	const MESSAGE_INVALID_DOB = "Invalid DOB value for record";
	const MESSAGE_INVALID_WORKPLACE_ID = "Invalid Workplace ID value for record";
	const MESSAGE_INVALID_STATUS = "Invalid Status value for record";

	protected $dob;
	protected $workplace_id;
	protected $status;

	protected $paymentValue = 0;

	public function __construct($arrayData)
	{
		parent::__construct();
		$complete = $this->verifyComplete($arrayData, self::$source_keys);

		if(true == $complete)
		{
			$this->parseBasicRecordData($arrayData);

			$this->workplace_id = $arrayData[self::ATTRIBUTE_WORKPLACE_ID];
			try
			{
				$this->dob = new DateTime($arrayData[self::ATTRIBUTE_DOB]);
			}
			catch(Exception $e)
			{
				$this->dob = null;
			}
			$this->status = $arrayData[self::ATTRIBUTE_STATUS];
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

	/**
	 * Get the attendance status
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the employees data of birth
	 * @return DateTime|null
	 */
	public function getDOB(): ?DateTime
	{
		return $this->dob;
	}

	/**
	 * There should really be a proper currency type but float is sufficient for the scope of this project
	 * @param float $value
	 */
	public function setPaymentValue(float $value)
	{
		$this->paymentValue = $value;
	}

	/**
	 * We round the payment value to the nearest penny - There is an assumption that we round this to 2 decimal places,
	 * @return float
	 */
	public function getPaymentValue(): float
	{
		return round($this->paymentValue, 2);
	}

}