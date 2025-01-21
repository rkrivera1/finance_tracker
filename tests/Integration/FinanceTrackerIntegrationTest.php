<?php
namespace OCA\FinanceTracker\Tests\Integration;

use PHPUnit\Framework\TestCase;
use OCA\FinanceTracker\Db\Transaction;
use OCA\FinanceTracker\Db\Budget;
use OCA\FinanceTracker\Db\Investment;
use OCA\FinanceTracker\Db\Debt;

class FinanceTrackerIntegrationTest extends TestCase {
    public function testTransactionEntityCreation() {
        $transaction = new Transaction();
        $transaction->setUserId('testuser');
        $transaction->setAmount(100.50);
        $transaction->setCategory('groceries');

        $this->assertEquals('testuser', $transaction->getUserId());
        $this->assertEquals(100.50, $transaction->getAmount());
        $this->assertEquals('groceries', $transaction->getCategory());
    }

    public function testBudgetEntityCreation() {
        $budget = new Budget();
        $budget->setUserId('testuser');
        $budget->setCategory('dining');
        $budget->setMonthlyLimit(300.00);

        $this->assertEquals('testuser', $budget->getUserId());
        $this->assertEquals('dining', $budget->getCategory());
        $this->assertEquals(300.00, $budget->getMonthlyLimit());
    }

    public function testInvestmentEntityCreation() {
        $investment = new Investment();
        $investment->setUserId('testuser');
        $investment->setName('Tech ETF');
        $investment->setPurchasePrice(50.00);
        $investment->setQuantity(10);
        $investment->setCurrentPrice(55.00);

        $this->assertEquals('testuser', $investment->getUserId());
        $this->assertEquals('Tech ETF', $investment->getName());
        $this->assertEquals(50.00, $investment->getPurchasePrice());
        $this->assertEquals(10, $investment->getQuantity());
        $this->assertEquals(55.00, $investment->getCurrentPrice());
    }

    public function testDebtEntityCreation() {
        $debt = new Debt();
        $debt->setUserId('testuser');
        $debt->setCreditorName('Student Loan');
        $debt->setTotalAmount(10000.00);
        $debt->setRemainingBalance(9500.00);

        $this->assertEquals('testuser', $debt->getUserId());
        $this->assertEquals('Student Loan', $debt->getCreditorName());
        $this->assertEquals(10000.00, $debt->getTotalAmount());
        $this->assertEquals(9500.00, $debt->getRemainingBalance());
    }
}
