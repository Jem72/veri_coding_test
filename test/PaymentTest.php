<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 16:43
 */

include_once(dirname(__FILE__, 2) . '/src/models/Payment.php');

use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
	function testAttendingPayments(): void
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

	function testNonAttendingPayments(): void
	{
		$payment1 = new Payment(14, 'AL', 0);
		$payment2 = new Payment(18, 'CSL', 0);
		$payment3 = new Payment(24, 'USL', 0);
		$payment4 = new Payment(25, 'AL', 0);
		$payment5 = new Payment(26, 'CSL', 0);
		$payment6 = new Payment(99, 'USL', 0);
		$this->assertEquals(Payment::RATE_ZERO, $payment1->getBasicRate(), 'Check Basic Rate 1');
		$this->assertEquals(Payment::RATE_BASIC_2, $payment2->getBasicRate(), 'Check Basic Rate 2');
		$this->assertEquals(Payment::RATE_BASIC_2, $payment3->getBasicRate(), 'Check Basic Rate 2');
		$this->assertEquals(Payment::RATE_BASIC_3, $payment4->getBasicRate(), 'Check Basic Rate 3');
		$this->assertEquals(Payment::RATE_BASIC_4, $payment5->getBasicRate(), 'Check Basic Rate 4');
		$this->assertEquals(Payment::RATE_BASIC_4, $payment6->getBasicRate(), 'Check Basic Rate 4');
	}

}
