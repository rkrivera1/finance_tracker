<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Transaction extends Entity {
    protected $userId;
    protected $amount;
    protected $category;
    protected $description;
    protected $transactionDate;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('amount', 'float');
        $this->addType('category', 'string');
        $this->addType('description', 'string');
        $this->addType('transactionDate', 'datetime');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'amount' => $this->getAmount(),
            'category' => $this->getCategory(),
            'description' => $this->getDescription(),
            'transactionDate' => $this->getTransactionDate()
        ];
    }

    // Getters and Setters
    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Transaction amount cannot be negative');
        }
        $this->amount = $amount;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $validCategories = [
            'groceries', 'dining', 'entertainment', 
            'utilities', 'transportation', 'healthcare'
        ];

        if (!in_array($category, $validCategories)) {
            throw new \InvalidArgumentException('Invalid transaction category');
        }
        $this->category = $category;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getTransactionDate() {
        return $this->transactionDate;
    }

    public function setTransactionDate($transactionDate) {
        $this->transactionDate = $transactionDate;
    }
}
