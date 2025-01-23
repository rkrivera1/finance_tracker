<?php
namespace OCA\FinanceTracker\Db;

class Account extends Entity {
    protected $name;
    protected $type;
    protected $balance;
    protected $userId;
    protected $createdAt;

    public function __construct() {
        $this->addType('name', 'string');
        $this->addType('type', 'string');
        $this->addType('balance', 'float');
        $this->addType('userId', 'string');
        $this->addType('createdAt', 'datetime');
    }
}
