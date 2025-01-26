<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;
use OCA\FinanceTracker\Lib\Budget;
use DateTime;

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

    public function findByUserAndCategory(string $userId, string $category) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->andX(
                   $qb->expr()->eq('user_id', $qb->createNamedParameter($userId)),
                   $qb->expr()->eq('category', $qb->createNamedParameter($category))
               )
           );
        
        return $this->findEntities($qb);
    }

    public function findActiveBudgets(string $userId, DateTime $currentDate = null) {
        $currentDate = $currentDate ?? new DateTime();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->andX(
                   $qb->expr()->eq('user_id', $qb->createNamedParameter($userId)),
                   $qb->expr()->lte('start_date', $qb->createNamedParameter($currentDate->format('Y-m-d'))),
                   $qb->expr()->gte('end_date', $qb->createNamedParameter($currentDate->format('Y-m-d')))
               )
           );
        
        return $this->findEntities($qb);
    }

    public function create(
        string $userId, 
        string $name, 
        float $amount, 
        string $category, 
        DateTime $startDate, 
        DateTime $endDate,
        float $currentSpending = 0.0
    ) {
        $budget = new Budget();
        $budget->setUserId($userId);
        $budget->setName($name);
        $budget->setAmount($amount);
        $budget->setCategory($category);
        $budget->setStartDate($startDate);
        $budget->setEndDate($endDate);
        $budget->setCurrentSpending($currentSpending);
        
        return $this->insert($budget);
    }

    public function updateCurrentSpending(int $budgetId, float $spending) {
        $qb = $this->db->getQueryBuilder();
        $qb->update($this->getTableName())
           ->set('current_spending', $qb->createNamedParameter($spending))
           ->where($qb->expr()->eq('id', $qb->createNamedParameter($budgetId)));
        
        $qb->executeStatement();
    }

    public function getBudgetSummary(string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select(
            $qb->func()->count('id', 'total_budgets'),
            $qb->func()->sum('amount', 'total_budget_amount'),
            $qb->func()->sum('current_spending', 'total_spending')
        )
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $qb->executeQuery()->fetch();
    }

    public function getOverBudgetCategories(string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('category')
           ->from($this->getTableName())
           ->where(
               $qb->expr()->andX(
                   $qb->expr()->eq('user_id', $qb->createNamedParameter($userId)),
                   $qb->expr()->gt('current_spending', $qb->expr()->column('amount'))
               )
           );
        
        return $qb->executeQuery()->fetchAll();
    }
}
