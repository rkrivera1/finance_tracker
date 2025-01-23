<?php
namespace OCA\FinanceTracker\Db;

class Budget extends Entity {
    protected $name;
    protected $amount;
    protected $category;
    protected $userId;
    protected $startDate;
    protected $endDate;

    public function __construct() {
        $this->addType('name', 'string');
        $this->addType('amount', 'float');
        $this->addType('category', 'string');
        $this->addType('userId', 'string');
        $this->addType('startDate', 'datetime');
        $this->addType('endDate', 'datetime');
    }
}
