<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Account extends Entity {
    protected $id;
    protected $userId;
    protected $name;
    protected $type;
    protected $balance;
    protected $createdAt;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('userId', 'string');
        $this->addType('name', 'string');
        $this->addType('type', 'string');
        $this->addType('balance', 'float');
        $this->addType('createdAt', 'datetime');
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

    public function setType(string $type) {
        $this->type = $type;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setBalance(float $balance) {
        $this->balance = $balance;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function setCreatedAt(\DateTime $createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }
}
