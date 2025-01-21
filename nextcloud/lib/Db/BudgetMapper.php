<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class BudgetMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_budgets', Budget::class);
    }

    /**
     * Find all budgets for a user
     * @param string $userId
     * @return Budget[]
     */
    public function findAll(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    /**
     * Find budget for a specific category
     * @param string $userId
     * @param string $category
     * @return Budget|null
     */
    public function findByCategory(string $userId, string $category) {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->eq('category', $qb->createNamedParameter($category)));
        
        try {
            return $this->findEntity($qb);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
            return null;
        }
    }

    /**
     * Find active budgets for a user
     * @param string $userId
     * @param \DateTime $currentDate
     * @return Budget[]
     */
    public function findActiveBudgets(string $userId, \DateTime $currentDate): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->lte('start_date', $qb->createNamedParameter($currentDate, IQueryBuilder::PARAM_DATE)))
           ->andWhere($qb->expr()->gte('end_date', $qb->createNamedParameter($currentDate, IQueryBuilder::PARAM_DATE)));
        
        return $this->findEntities($qb);
    }

    /**
     * Delete budgets that have expired
     * @param \DateTime $cutoffDate
     * @return int Number of deleted budgets
     */
    public function deleteExpiredBudgets(\DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where($qb->expr()->lt('end_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE)));
        
        return $qb->executeStatement();
    }

    /**
     * Delete budgets for a specific user that have expired
     * @param string $userId
     * @param \DateTime $cutoffDate
     * @return int Number of deleted budgets
     */
    public function deleteUserExpiredBudgets(string $userId, \DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->lt('end_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE)));
        
        return $qb->executeStatement();
    }

    /**
     * Create a new budget
     * @param Budget $budget
     * @return Budget
     */
    public function insert(\OCP\AppFramework\Db\Entity $budget): \OCP\AppFramework\Db\Entity {
        return parent::insert($budget);
    }
}
