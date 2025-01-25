<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Transaction extends Entity {
    protected $id;
    protected $userId;
    protected $accountId;
    protected $description;
    protected $amount;
    protected $type;
    protected $date;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('userId', 'string');
        $this->addType('accountId', 'integer');
        $this->addType('description', 'string');
        $this->addType('amount', 'float');
        $this->addType('type', 'string');
        $this->addType('date', 'datetime');
    }

    public function setUserId(string $userId) {
        $this->userId = $userId;
    }

    public function getUserId(): string {
        return $this->userId;
    }

    public function setAccountId(int $accountId) {
        $this->accountId = $accountId;
    }

    public function getAccountId(): int {
        return $this->accountId;
    }

    public function setDescription(string $description) {
        $this->description = $description;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setAmount(float $amount) {
        $this->amount = $amount;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function setType(string $type) {
        $this->type = $type;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

    public function getDate(): \DateTime {
        return $this->date;
    }
}
