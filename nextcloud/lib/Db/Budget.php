<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Budget extends Entity {
    protected $userId;
    protected $category;
    protected $monthlyLimit;
    protected $startDate;
    protected $endDate;
    protected $currentSpending;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('category', 'string');
        $this->addType('monthlyLimit', 'float');
        $this->addType('startDate', 'datetime');
        $this->addType('endDate', 'datetime');
        $this->addType('currentSpending', 'float');
    }

    public function getSpendingPercentage(): float {
        if ($this->monthlyLimit == 0) {
            return 0;
        }
        return ($this->currentSpending / $this->monthlyLimit) * 100;
    }

    public function getStatus(): string {
        $percentage = $this->getSpendingPercentage();
        return $percentage > 100 ? 'over_budget' : 'under_budget';
    }

    public function validatePeriod() {
        if ($this->startDate && $this->endDate && $this->startDate > $this->endDate) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'category' => $this->getCategory(),
            'monthlyLimit' => $this->getMonthlyLimit(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate(),
            'currentSpending' => $this->getCurrentSpending(),
            'status' => $this->getStatus()
        ];
    }

    // Getters and setters
    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getMonthlyLimit() {
        return $this->monthlyLimit;
    }

    public function setMonthlyLimit($monthlyLimit) {
        if ($monthlyLimit < 0) {
            throw new \InvalidArgumentException('Monthly limit cannot be negative');
        }
        $this->monthlyLimit = $monthlyLimit;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        $this->validatePeriod();
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        $this->validatePeriod();
    }

    public function getCurrentSpending() {
        return $this->currentSpending;
    }

    public function setCurrentSpending($currentSpending) {
        $this->currentSpending = $currentSpending;
    }
}
