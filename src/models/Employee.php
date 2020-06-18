<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-18
 * Time: 06:39
 */

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__) . '/Generic.php');
include_once(dirname(__FILE__) . '/Location.php');

/**
 * Class Employee - stores consolidated data for employee
 *
 * @property float    payment
 * @property DateTime dob
 */
class Employee extends Generic
{
	private $payment;
	private $dob;

	public function __construct(int $id, string $name, Location $location, DateTime $dob, float $payment)
	{
		parent::__construct();

		$this->id = $id;
		$this->name = $name;
		$this->location = $location;
		$this->payment = $payment;
		$this->dob = $dob;
	}

	public function verifyMatch(int $id, string $name, Location $location, DateTime $dob)
	{
		return (($id == $this->id) && ($name == $this->name) && ($this->distanceTo($location) == 0) && ($this->dob == $dob));
	}

	public function getPayment()
	{
		return round($this->payment, 2);
	}

	public function addPayment(float $additional): float
	{
		$this->payment += $additional;
		return $this->getPayment();
	}
}
