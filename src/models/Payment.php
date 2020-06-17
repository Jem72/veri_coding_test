<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 15:53
 */


/**
 * Class Payment
 * @property int    age   - Age of the person claiming the payment
 * @property string status - Status of the day for the payment
 * @property float  distance - distance travelled
 */
class Payment
{
	public static $valid_status = array('AT', 'AL', 'CSL', 'USL');

	const RATE_BASIC_4 = 90.5;
	const RATE_BASIC_3 = 85.9;
	const RATE_BASIC_2 = 81.0;
	const RATE_BASIC_1 = 72.5;

	const RATE_MEAL = 5.5;
	const RATE_FUEL = 1.0;
	const RATE_TRAVEL = 1.09;

	const MIN_TRAVEL_DISTANCE = 5.0;
	const RATE_ZERO = 0.0;
	const MIN_AGE_BASIC_4 = 26;
	const MIN_AGE_BASIC_3 = 25;
	const MIN_AGE_BASIC_2 = 18;

	private $age;
	private $status;
	private $distance;

	/**
	 * Payment constructor.
	 * @param int    $age
	 * @param string $status
	 * @param float  $distance
	 */
	public function __construct(int $age, string $status, float $distance)
	{
		$this->setAge($age);
		$this->setStatus($status);
		$this->setDistance($distance);
	}

	/**
	 * Set the age of the person receiving the payment
	 * @param int $age
	 */
	private function setAge(int $age)
	{
		$this->age = $age;
	}

	/**
	 * Set the status of the payment record
	 * @param string $status
	 */
	private function setStatus(string $status)
	{
		$this->status = $status;
		if(false === array_search($status, self::$valid_status))
		{
			Log::get_instance()->warning("Payment called with invalid status: " . $status);
		}
	}

	/**
	 * Set the distance between home and work for the record
	 * @param float $distance
	 */
	private function setDistance(float $distance)
	{
		$this->distance = $distance;
	}


	public function getBasicRate(): float
	{
		$rate = self::RATE_ZERO;

		if(false !== array_search($this->status, array('AT', 'AL', 'CSL')))
		{
			if($this->age >= self::MIN_AGE_BASIC_4)
			{
				$rate = self::RATE_BASIC_4;
			}
			elseif($this->age >= self::MIN_AGE_BASIC_3)
			{
				$rate = self::RATE_BASIC_3;
			}
			elseif($this->age >= self::MIN_AGE_BASIC_2)
			{
				$rate = self::RATE_BASIC_2;
			}
			else
			{
				$rate = self::RATE_BASIC_1;
			}
		}

		return $rate;
	}

	public function getMealRate()
	{
		$rate = self::RATE_ZERO;

		if('AT' === $this->status)
		{
			$rate = self::RATE_MEAL;
		}

		return $rate;
	}

	public function getFuelRate()
	{
		return self::RATE_FUEL;
	}

	public function getTravelRate()
	{
		$rate = self::RATE_ZERO;
		if('AT' === $this->status)
		{
			if($this->distance >= self::MIN_TRAVEL_DISTANCE)
			{
				$rate = ($this->distance * 2) * self::RATE_TRAVEL;
			}
		}
		return round($rate, 2);
	}

	public function getTotal()
	{
		$total = $this->getBasicRate() + $this->getMealRate() + $this->getTravelRate() + $this->getFuelRate();

		return $total;
	}
}