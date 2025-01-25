<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Budget extends Entity {
    protected $id;
    protected $userId;
    protected $name;
    protected $amount;
    protected $category;
    protected $startDate;
    protected $endDate;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('userId', 'string');
        $this->addType('name', 'string');
        $this->addType('amount', 'float');
        $this->addType('category', 'string');
        $this->addType('startDate', 'date');
        $this->addType('endDate', 'date');
    }

    public function setUserId(string $userId) {
        $this->userId = $userId;
    }

    public function getUserId(): string {
        return $this->userId;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setAmount(float $amount) {
        $this->amount = $amount;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function setCategory(string $category) {
        $this->category = $category;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function setStartDate(\DateTime $startDate) {
        $this->startDate = $startDate;
    }

    public function getStartDate(): \DateTime {
        return $this->startDate;
    }

    public function setEndDate(\DateTime $endDate) {
        $this->endDate = $endDate;
    }

    public function getEndDate(): \DateTime {
        return $this->endDate;
    }
}
