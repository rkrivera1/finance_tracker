<?php
namespace OCA\FinanceTracker\Tests\Unit;

use OCA\FinanceTracker\Db\Debt;
use PHPUnit\Framework\TestCase;

class DebtTest extends TestCase {
    private $debt;

    protected function setUp(): void {
        $this->debt = new Debt();
    }

    public function testCreateDebt() {
        $this->debt->setUserId('testuser');
        $this->debt->setCreditorName('Bank XYZ');
        $this->debt->setType('personal_loan');
        $this->debt->setTotalAmount(5000.00);
        $this->debt->setRemainingBalance(5000.00);
        $this->debt->setInterestRate(5.5);
        $this->debt->setStartDate(new \DateTime('2023-01-01'));
        $this->debt->setDueDate(new \DateTime('2024-01-01'));
        $this->debt->setMinimumPayment(200.00);

        $this->assertEquals('Bank XYZ', $this->debt->getCreditorName());
        $this->assertEquals('personal_loan', $this->debt->getType());
    }

    public function testDebtPayoffCalculations() {
        $this->debt->setTotalAmount(5000.00);
        $this->debt->setRemainingBalance(3000.00);

        $this->assertEquals(2000.00, $this->debt->calculateTotalInterest());
        $this->assertEquals(40, $this->debt->getPayoffProgress());
    }

    public function testDebtStatusDetermination() {
        $now = new \DateTime();
        $pastDueDate = (clone $now)->modify('-1 month');
        $futureDueDate = (clone $now)->modify('+2 months');

        // Paid off debt
        $this->debt->setRemainingBalance(0);
        $this->assertEquals('paid_off', $this->debt->determineStatus());

        // Overdue debt
        $this->debt->setRemainingBalance(1000);
        $this->debt->setDueDate($pastDueDate);
        $this->assertEquals('overdue', $this->debt->determineStatus());

        // Due soon
        $this->debt->setDueDate($futureDueDate);
        $this->assertEquals('due_soon', $this->debt->determineStatus());
    }

    public function testInvalidDebtAmount() {
        $this->expectException(\InvalidArgumentException::class);
        $this->debt->setTotalAmount(-1000);
    }

    public function testDebtPayoffTimeEstimation() {
        $this->debt->setTotalAmount(5000.00);
        $this->debt->setRemainingBalance(5000.00);
        $this->debt->setInterestRate(0.06); // 6% annual interest

        // Estimate months to payoff with $200 monthly payment
        $monthsToPayoff = $this->debt->estimateTimeToPayoff(200);
        
        $this->assertGreaterThan(20, $monthsToPayoff);
        $this->assertLessThan(40, $monthsToPayoff);
    }

    public function testDebtJsonSerialization() {
        $this->debt->setUserId('testuser');
        $this->debt->setCreditorName('Bank XYZ');
        $this->debt->setType('personal_loan');
        $this->debt->setTotalAmount(5000.00);
        $this->debt->setRemainingBalance(3000.00);
        $this->debt->setInterestRate(5.5);
        $this->debt->setStartDate(new \DateTime('2023-01-01'));
        $this->debt->setDueDate(new \DateTime('2024-01-01'));

        $jsonData = $this->debt->jsonSerialize();

        $this->assertArrayHasKey('id', $jsonData);
        $this->assertArrayHasKey('creditorName', $jsonData);
        $this->assertArrayHasKey('type', $jsonData);
        $this->assertArrayHasKey('totalAmount', $jsonData);
        $this->assertArrayHasKey('remainingBalance', $jsonData);
        $this->assertArrayHasKey('status', $jsonData);
        $this->assertArrayHasKey('payoffProgress', $jsonData);
    }

    public function testDebtTypeValidation() {
        $validDebtTypes = [
            'credit_card', 'personal_loan', 'mortgage', 
            'student_loan', 'car_loan', 'other'
        ];

        foreach ($validDebtTypes as $type) {
            $this->debt->setType($type);
            $this->assertEquals($type, $this->debt->getType());
        }
    }
}
