<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 14:31
 */

include_once(dirname(__FILE__) . '/Location.php');

/**
 * Class GenericRecord Generic record covering functionality common to workplace and attendance
 *
 * @property string   $failureMessage: A text message describing why the record has failed
 * @property int      $id Unique Identifier for the recond
 * @property string   $name Text Name for the record
 * @property Location $location Location for the record
 */
abstract class GenericRecord
{
	const MESSAGE_MISSING_DATA = 'Data is missing from record';
	const MESSAGE_INVALID_ID = "Invalid ID value for record";
	const MESSAGE_INVALID_NAME = "Invalid Name value for record";
	const MESSAGE_INVALID_LOCATION = "Invalid Location value for record";

	protected $failureMessage;
	protected $id;
	protected $name;
	protected $location;
	protected $valid = true;


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

	public function getFailureMessage()
	{
		return $this->failureMessage;
	}

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
	 * @param $arrayData
	 */
	protected function parseBasicRecordData($arrayData): void
	{
		$this->id = (int)$arrayData['id'];
		$this->name = $arrayData['name'];
		$this->location = new Location($arrayData['location']);
	}

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


	public function distanceTo(Location $location): float
	{
		$distance = 0.0;

		$distance_x = $this->location->x - $location->x;
		$distance_y = $this->location->y - $location->y;

		$distance = sqrt(pow($distance_x, 2) + pow($distance_y, 2));

		return $distance;
	}

	public function getLocation(): Location
	{
		return $this->location;
	}
}