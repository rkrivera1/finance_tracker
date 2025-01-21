<?php
namespace OCA\FinanceTracker\Lib;

use OCP\AppFramework\Db\Entity;

class Transaction extends Entity {
    protected $userId;
    protected $amount;
    protected $category;
    protected $date;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('amount', 'float');
        $this->addType('category', 'string');
        $this->addType('date', 'datetime');
    }
}
