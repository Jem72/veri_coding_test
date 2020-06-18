<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 16:43
 */

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__, 2) . '/src/models/Payment.php');

use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
	protected function setUp(): void
	{
		Log::get_instance()->disable();
	}

	function testAgeBasicPayments(): void
	{
		$payment1 = new Payment(14, 'AT', 0);
		$payment2 = new Payment(18, 'AT', 0);
		$payment3 = new Payment(24, 'AT', 0);
		$payment4 = new Payment(25, 'AT', 0);
		$payment5 = new Payment(26, 'AT', 0);
		$payment6 = new Payment(99, 'AT', 0);
		$this->assertEquals(Payment::RATE_BASIC_1, $payment1->getBasicRate(), 'Check Basic Rate 1');
		$this->assertEquals(Payment::RATE_BASIC_2, $payment2->getBasicRate(), 'Check Basic Rate 2');
		$this->assertEquals(Payment::RATE_BASIC_2, $payment3->getBasicRate(), 'Check Basic Rate 2');
		$this->assertEquals(Payment::RATE_BASIC_3, $payment4->getBasicRate(), 'Check Basic Rate 3');
		$this->assertEquals(Payment::RATE_BASIC_4, $payment5->getBasicRate(), 'Check Basic Rate 4');
		$this->assertEquals(Payment::RATE_BASIC_4, $payment6->getBasicRate(), 'Check Basic Rate 4');
	}

	function testAttendingBasicPayments(): void
	{
		$payment1 = new Payment(24, 'AL', 0);
		$payment2 = new Payment(24, 'CSL', 0);
		$payment3 = new Payment(24, 'USL', 0);
		$payment4 = new Payment(24, 'AT', 0);
		$this->assertEquals(Payment::RATE_BASIC_2, $payment1->getBasicRate(), 'Check Basic Rate 1');
		$this->assertEquals(Payment::RATE_BASIC_2, $payment2->getBasicRate(), 'Check Basic Rate 2');
		$this->assertEquals(Payment::RATE_ZERO, $payment3->getBasicRate(), 'Check Basic Rate 2');
		$this->assertEquals(Payment::RATE_BASIC_2, $payment4->getBasicRate(), 'Check Basic Rate 3');
	}

	function testAttendingMealPayments(): void
	{
		$payment1 = new Payment(43, 'AT', 0);
		$payment2 = new Payment(43, 'CSL', 0);
		$payment3 = new Payment(43, 'USL', 0);
		$payment4 = new Payment(43, 'AL', 0);
		$this->assertEquals(Payment::RATE_MEAL, $payment1->getMealRate(), 'Check attending meal rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment2->getMealRate(), 'Check CSL meal rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment3->getMealRate(), 'Check USL meal rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment4->getMealRate(), 'Check Annual Leave meal rate');
	}


	function testAttendingFuelPayments(): void
	{
		$payment1 = new Payment(43, 'AT', 0);
		$payment2 = new Payment(43, 'CSL', 0);
		$payment3 = new Payment(43, 'USL', 0);
		$payment4 = new Payment(43, 'AL', 0);
		$this->assertEquals(Payment::RATE_FUEL, $payment1->getFuelRate(), 'Check attending fuel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment2->getFuelRate(), 'Check CSL fuel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment3->getFuelRate(), 'Check USL fuel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment4->getFuelRate(), 'Check Annual Leave fuel rate');
	}


	function testAttendingTravelPayments(): void
	{
		$distance = 10.0;
		$travelPayment = $distance * Payment::RATE_TRAVEL * 2;
		$payment1 = new Payment(43, 'AT', $distance);
		$payment2 = new Payment(43, 'CSL', $distance);
		$payment3 = new Payment(43, 'USL', $distance);
		$payment4 = new Payment(43, 'AL', $distance);
		$this->assertEquals($travelPayment, $payment1->getTravelRate(), 'Check attending travel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment2->getTravelRate(), 'Check CSL travel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment3->getTravelRate(), 'Check USL travel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment4->getTravelRate(), 'Check Annual Leave travel rate');
	}

	function testDistanceTravelPayments(): void
	{
		$distance1 = 2.0;
		$distance2 = 4.9999;
		$distance3 = 5.0;
		$distance4 = 5.1;
		$travelPayment3 = round($distance3 * Payment::RATE_TRAVEL * 2,2);
		$travelPayment4 = round($distance4 * Payment::RATE_TRAVEL * 2,2);
		$payment1 = new Payment(43, 'AT', $distance1);
		$payment2 = new Payment(43, 'AT', $distance2);
		$payment3 = new Payment(43, 'AT', $distance3);
		$payment4 = new Payment(43, 'AT', $distance4);
		$this->assertEquals(Payment::RATE_ZERO, $payment1->getTravelRate(), 'Check 2.0 distance travel rate');
		$this->assertEquals(Payment::RATE_ZERO, $payment2->getTravelRate(), 'Check 4.9999 distance travel rate');
		$this->assertEquals($travelPayment3, $payment3->getTravelRate(), 'Check 5.0 distance travel rate');
		$this->assertEquals($travelPayment4, $payment4->getTravelRate(), 'Check 5.1 distance travel rate');
	}

}
