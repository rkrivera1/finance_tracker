<?php
namespace OCA\FinanceTracker\Tests\Unit;

use OCA\FinanceTracker\Db\Budget;
use PHPUnit\Framework\TestCase;

class BudgetTest extends TestCase {
    private $budget;

    protected function setUp(): void {
        $this->budget = new Budget();
    }

    public function testCreateBudget() {
        $this->budget->setUserId('testuser');
        $this->budget->setCategory('groceries');
        $this->budget->setMonthlyLimit(500.00);
        $this->budget->setStartDate(new \DateTime('2023-01-01'));
        $this->budget->setEndDate(new \DateTime('2023-12-31'));

        $this->assertEquals('testuser', $this->budget->getUserId());
        $this->assertEquals('groceries', $this->budget->getCategory());
        $this->assertEquals(500.00, $this->budget->getMonthlyLimit());
    }

    public function testBudgetSpendingCalculation() {
        $this->budget->setMonthlyLimit(500.00);
        $this->budget->setCurrentSpending(250.00);

        $this->assertEquals(50, $this->budget->getSpendingPercentage());
    }

    public function testInvalidBudgetLimit() {
        $this->expectException(\InvalidArgumentException::class);
        $this->budget->setMonthlyLimit(-100);
    }

    public function testBudgetStatus() {
        $this->budget->setMonthlyLimit(500.00);
        $this->budget->setCurrentSpending(600.00);

        $this->assertEquals('over_budget', $this->budget->getStatus());

        $this->budget->setCurrentSpending(250.00);
        $this->assertEquals('under_budget', $this->budget->getStatus());
    }

    public function testBudgetPeriodValidation() {
        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime('2023-12-31');

        $this->budget->setStartDate($startDate);
        $this->budget->setEndDate($endDate);

        $this->expectException(\InvalidArgumentException::class);
        $this->budget->setEndDate(new \DateTime('2022-12-31'));
    }

    public function testBudgetJsonSerialization() {
        $this->budget->setUserId('testuser');
        $this->budget->setCategory('groceries');
        $this->budget->setMonthlyLimit(500.00);
        $this->budget->setCurrentSpending(250.00);

        $jsonData = $this->budget->jsonSerialize();

        $this->assertArrayHasKey('id', $jsonData);
        $this->assertArrayHasKey('userId', $jsonData);
        $this->assertArrayHasKey('category', $jsonData);
        $this->assertArrayHasKey('monthlyLimit', $jsonData);
        $this->assertArrayHasKey('currentSpending', $jsonData);
        $this->assertArrayHasKey('status', $jsonData);
    }
}
