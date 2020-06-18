<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 14:31
 */

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__) . '/Location.php');

/**
 * Class GenericRecord Generic record covering functionality common to workplace and attendance
 *
 * @property string   $failureMessage: A text message describing why the record has failed
 * @property int      $id Unique Identifier for the recond
 * @property string   $name Text Name for the record
 * @property Location $location Location for the record
 */
abstract class Generic
{
	const MESSAGE_MISSING_DATA = 'Data is missing from record';
	const MESSAGE_INVALID_ID = "Invalid ID value for record";
	const MESSAGE_INVALID_NAME = "Invalid Name value for record";
	const MESSAGE_INVALID_LOCATION = "Invalid Location value for record";
	protected const ATTRIBUTE_ID = 'id';
	protected const ATTRIBUTE_NAME = 'name';
	protected const ATTRIBUTE_LOCATION = 'location';

	protected $failureMessage;
	protected $id = 0;
	protected $name = "";
	protected $location;
	protected $valid = true;

	public function __construct()
	{
		$this->location = new Location("");
	}

	public function getID(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function isValid()
	{
		return $this->valid;
	}

	protected function failRecord(string $message)
	{
		Log::get_instance()->warning($message);
		$this->valid = false;
		if(null == $this->failureMessage)
		{
			$this->failureMessage = $message;
		}
	}

	/**
	 * Is there is an error in processing the record, this is a text message for loggin
	 * @return string|null
	 */
	public function getFailureMessage(): ?string
	{
		return $this->failureMessage;
	}

	/**
	 * Verifies that the data for the record is complete. For now, all records must contain all data
	 * @param array $arrayData
	 * @param array $source_keys
	 * @return bool
	 */
	protected function verifyComplete(array $arrayData, array $source_keys)
	{
		$complete = true;
		$missingKey = null;
		foreach($source_keys as $key)
		{
			if(FALSE === array_key_exists($key, $arrayData))
			{
				$complete = false;
				$missingKey = $key;
				break;
			}
		}

		$this->valid = $complete;
		if(false == $complete)
		{
			$this->failRecord(self::MESSAGE_MISSING_DATA . ': ' . $missingKey);
		}
		return $complete;
	}

	/**
	 * Processes the three data values common to both data types
	 * @param $arrayData
	 */
	protected function parseBasicRecordData($arrayData): void
	{
		$this->id = (int)$arrayData[self::ATTRIBUTE_ID];
		$this->name = $arrayData[self::ATTRIBUTE_NAME];
		$this->location = new Location($arrayData[self::ATTRIBUTE_LOCATION]);
	}

	/**
	 * Verifies that the decoded record is valid
	 * @return bool
	 */
	protected function validate(): bool
	{
		$this->valid = true;
		if($this->id <= 0)
		{
			$this->failRecord(self::MESSAGE_INVALID_ID);
		}

		if(null == $this->name)
		{
			$this->failRecord(self::MESSAGE_INVALID_NAME);
		}

		if((null == $this->location) || (false == $this->location->isValid()))
		{
			$this->failRecord(self::MESSAGE_INVALID_LOCATION);
		}
		return $this->valid;
	}


	/**
	 * Returns the distance between the record and a supplied location
	 * @param Location $location
	 * @return float
	 */
	public function distanceTo(Location $location): float
	{
		return $this->location->distanceTo($location);
	}

	/**
	 * Gets the location of the record
	 * @return Location
	 */
	public function getLocation(): ?Location
	{
		return $this->location;
	}
}