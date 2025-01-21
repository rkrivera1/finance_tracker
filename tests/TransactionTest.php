<?php
namespace OCA\FinanceTracker\Tests;

use OCA\FinanceTracker\Lib\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase {
    public function testCreateTransaction() {
        $transaction = new Transaction();
        $transaction->setUserId('testuser');
        $transaction->setAmount(100.50);
        $transaction->setCategory('groceries');
        $transaction->setDate(new \DateTime());

        $this->assertEquals('testuser', $transaction->getUserId());
        $this->assertEquals(100.50, $transaction->getAmount());
    }
}
