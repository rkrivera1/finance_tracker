<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Debt extends Entity {
    protected $userId;
    protected $creditorName;
    protected $type;
    protected $totalAmount;
    protected $remainingBalance;
    protected $interestRate;
    protected $startDate;
    protected $dueDate;
    protected $minimumPayment;
    protected $status;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('creditorName', 'string');
        $this->addType('type', 'string');
        $this->addType('totalAmount', 'float');
        $this->addType('remainingBalance', 'float');
        $this->addType('interestRate', 'float');
        $this->addType('startDate', 'datetime');
        $this->addType('dueDate', 'datetime');
        $this->addType('minimumPayment', 'float');
    }

    /**
     * Calculate total interest paid
     * @return float
     */
    public function calculateTotalInterest(): float {
        return $this->totalAmount - $this->remainingBalance;
    }

    /**
     * Calculate payoff progress
     * @return float
     */
    public function getPayoffProgress(): float {
        return (($this->totalAmount - $this->remainingBalance) / $this->totalAmount) * 100;
    }

    /**
     * Determine debt status
     * @return string
     */
    public function determineStatus(): string {
        $now = new \DateTime();

        if ($this->remainingBalance <= 0) {
            return 'paid_off';
        }

        if ($now > $this->dueDate) {
            return 'overdue';
        }

        $daysRemaining = $now->diff($this->dueDate)->days;
        if ($daysRemaining <= 60) { // Changed from 30 to 60
            return 'due_soon';
        }

        return 'active';
    }

    /**
     * Estimate time to payoff
     * @param float $monthlyPayment
     * @return int Months to payoff
     */
    public function estimateTimeToPayoff(float $monthlyPayment): int {
        if ($monthlyPayment <= 0) {
            return PHP_INT_MAX;
        }

        $remainingBalance = $this->remainingBalance;
        $monthlyInterest = $this->interestRate / 12;
        $months = 0;

        while ($remainingBalance > 0) {
            $interestPayment = $remainingBalance * $monthlyInterest;
            $principalPayment = $monthlyPayment - $interestPayment;

            $remainingBalance -= $principalPayment;
            $months++;

            // Prevent infinite loop
            if ($months > 360) { // 30 years
                break;
            }
        }

        return $months;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'creditorName' => $this->getCreditorName(),
            'type' => $this->getType(),
            'totalAmount' => $this->getTotalAmount(),
            'remainingBalance' => $this->getRemainingBalance(),
            'interestRate' => $this->getInterestRate(),
            'startDate' => $this->getStartDate(),
            'dueDate' => $this->getDueDate(),
            'minimumPayment' => $this->getMinimumPayment(),
            'status' => $this->determineStatus(),
            'payoffProgress' => $this->getPayoffProgress(),
            'totalInterestPaid' => $this->calculateTotalInterest()
        ];
    }

    // Getters and Setters
    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getCreditorName() {
        return $this->creditorName;
    }

    public function setCreditorName($creditorName) {
        $this->creditorName = $creditorName;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $validDebtTypes = [
            'credit_card', 'personal_loan', 'mortgage', 
            'student_loan', 'car_loan', 'other'
        ];

        if (!in_array($type, $validDebtTypes)) {
            throw new \InvalidArgumentException('Invalid debt type');
        }
        $this->type = $type;
    }

    public function getTotalAmount() {
        return $this->totalAmount;
    }

    public function setTotalAmount($totalAmount) {
        if ($totalAmount < 0) {
            throw new \InvalidArgumentException('Total amount cannot be negative');
        }
        $this->totalAmount = $totalAmount;
    }

    public function getRemainingBalance() {
        return $this->remainingBalance;
    }

    public function setRemainingBalance($remainingBalance) {
        $this->remainingBalance = $remainingBalance;
    }

    public function getInterestRate() {
        return $this->interestRate;
    }

    public function setInterestRate($interestRate) {
        $this->interestRate = $interestRate;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    public function getDueDate() {
        return $this->dueDate;
    }

    public function setDueDate($dueDate) {
        $this->dueDate = $dueDate;
    }

    public function getMinimumPayment() {
        return $this->minimumPayment;
    }

    public function setMinimumPayment($minimumPayment) {
        $this->minimumPayment = $minimumPayment;
    }
}
