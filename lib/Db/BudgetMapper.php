<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class BudgetMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'ft_budgets', Budget::class);
    }

    public function findByUser(string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    public function create(
        string $userId, 
        string $name, 
        float $amount, 
        string $category, 
        \DateTime $startDate, 
        \DateTime $endDate
    ) {
        $budget = new Budget();
        $budget->setUserId($userId);
        $budget->setName($name);
        $budget->setAmount($amount);
        $budget->setCategory($category);
        $budget->setStartDate($startDate);
        $budget->setEndDate($endDate);
        
        return $this->insert($budget);
    }
}
