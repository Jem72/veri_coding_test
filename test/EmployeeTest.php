<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-18
 * Time: 06:54
 */

use PHPUnit\Framework\TestCase;

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Employee.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Location.php');
/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/Log.php');

class EmployeeTest extends TestCase
{
	private $testID;
	private $testDOB;
	private $testName;
	private $testLocation;
	private $testPayment;

	public function setUp(): void
	{
		$this->testID = 55;
		$this->testName = "Test Record";
		$this->testLocation = new Location("(6, 7)");
		$this->testPayment = 2.55;
		try
		{
			$this->testDOB = new DateTime('2002-05-22');
		}
		catch(Exception $e)
		{
			$this->testDOB = null;
			Log::get_instance()->error("Could not set up DOB");
		}
	}

	public function testVerifyMatch(): void
	{
		$employee = new Employee($this->testID, $this->testName, $this->testLocation, $this->testDOB, $this->testPayment);

		try
		{
			$secondDOB = new DateTime('2014-02-11');
		}
		catch(Exception $e)
		{
			$secondDOB = null;
			Log::get_instance()->error("Could not set up second DOB");
		}
		$secondLocation = clone $this->testLocation;
		$secondLocation->y += 5;
		$this->assertEquals(true, $employee->verifyMatch($this->testID, $this->testName, $this->testLocation, $this->testDOB), 'Match did not verify');
		$this->assertEquals(false, $employee->verifyMatch($this->testID + 1, $this->testName, $this->testLocation, $this->testDOB), 'ID mismatch should not verify');
		$this->assertEquals(false, $employee->verifyMatch($this->testID, 'Junk', $this->testLocation, $this->testDOB), 'Name mismatch should not verify');
		$this->assertEquals(false, $employee->verifyMatch($this->testID, $this->testName, $secondLocation, $this->testDOB), 'Location mismatch should not verify');
		$this->assertEquals(false, $employee->verifyMatch($this->testID, $this->testName, $this->testLocation, $secondDOB), 'DOB mismatch should not verify');
	}

	public function testPaymentBasics()
	{
		$employee = new Employee($this->testID, $this->testName, $this->testLocation, $this->testDOB, $this->testPayment);
		$this->assertEquals($this->testPayment, $employee->getPayment(), 'Payment Does not match');

		$employee = new Employee($this->testID, $this->testName, $this->testLocation, $this->testDOB, $this->testPayment + 0.004);
		$this->assertEquals($this->testPayment, $employee->getPayment(), 'Rounded Payment Does not match');

		$employee = new Employee($this->testID, $this->testName, $this->testLocation, $this->testDOB, $this->testPayment + 0.006);
		$this->assertNotEquals($this->testPayment, $employee->getPayment(), 'Payment rouunded up should not match');
	}

	public function testPaymentSum()
	{
		$employee = new Employee($this->testID, $this->testName, $this->testLocation, $this->testDOB, $this->testPayment);
		$this->assertEquals($this->testPayment, $employee->getPayment(), 'Payment Does not match');

		$sum = $this->testPayment;

		$additional = 14.66463;
		$sum += $additional;
		$employee->addPayment($additional);
		$this->assertEquals(round($sum,2), $employee->getPayment(), 'Additional Payment should match');

		$additional = -5.52626;
		$sum += $additional;
		$employee->addPayment($additional);
		$this->assertEquals(round($sum,2), $employee->getPayment(), 'Second Additional Payment should match');
	}

}
