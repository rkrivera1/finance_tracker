<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class BudgetMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_tracker_budgets', Budget::class);
    }

    public function findByUser(string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    public function create(string $name, float $amount, string $category, string $userId, \DateTime $startDate, \DateTime $endDate) {
        $budget = new Budget();
        $budget->setName($name);
        $budget->setAmount($amount);
        $budget->setCategory($category);
        $budget->setUserId($userId);
        $budget->setStartDate($startDate);
        $budget->setEndDate($endDate);
        
        return $this->insert($budget);
    }
}
