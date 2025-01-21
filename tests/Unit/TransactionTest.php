<?php
namespace OCA\FinanceTracker\Tests\Unit;

use OCA\FinanceTracker\Db\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase {
    private $transaction;

    protected function setUp(): void {
        $this->transaction = new Transaction();
    }

    public function testCreateTransaction() {
        $this->transaction->setUserId('testuser');
        $this->transaction->setAmount(100.50);
        $this->transaction->setCategory('groceries');
        $this->transaction->setDescription('Weekly grocery shopping');
        $this->transaction->setTransactionDate(new \DateTime());

        $this->assertEquals('testuser', $this->transaction->getUserId());
        $this->assertEquals(100.50, $this->transaction->getAmount());
        $this->assertEquals('groceries', $this->transaction->getCategory());
    }

    public function testInvalidTransactionAmount() {
        $this->expectException(\InvalidArgumentException::class);
        $this->transaction->setAmount(-50);
    }

    public function testTransactionJsonSerialization() {
        $this->transaction->setUserId('testuser');
        $this->transaction->setAmount(100.50);
        $this->transaction->setCategory('groceries');
        $this->transaction->setTransactionDate(new \DateTime('2023-06-15'));

        $jsonData = $this->transaction->jsonSerialize();

        $this->assertArrayHasKey('id', $jsonData);
        $this->assertArrayHasKey('userId', $jsonData);
        $this->assertArrayHasKey('amount', $jsonData);
        $this->assertArrayHasKey('category', $jsonData);
        $this->assertEquals(100.50, $jsonData['amount']);
    }

    public function testTransactionCategoryValidation() {
        $validCategories = [
            'groceries', 'dining', 'entertainment', 
            'utilities', 'transportation', 'healthcare'
        ];

        foreach ($validCategories as $category) {
            $this->transaction->setCategory($category);
            $this->assertEquals($category, $this->transaction->getCategory());
        }
    }
}
