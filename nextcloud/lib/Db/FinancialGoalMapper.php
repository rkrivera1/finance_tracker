<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class FinancialGoalMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_financial_goals', FinancialGoal::class);
    }

    /**
     * Find all financial goals for a user
     * @param string $userId
     * @return FinancialGoal[]
     */
    public function findAll(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    /**
     * Find active financial goals for a user
     * @param string $userId
     * @return FinancialGoal[]
     */
    public function findActiveGoals(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->orX(
               $qb->expr()->eq('status', $qb->createNamedParameter('in_progress')),
               $qb->expr()->eq('status', $qb->createNamedParameter('behind'))
           ));
        
        return $this->findEntities($qb);
    }

    /**
     * Find goals by status
     * @param string $userId
     * @param string $status
     * @return FinancialGoal[]
     */
    public function findByStatus(string $userId, string $status): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->eq('status', $qb->createNamedParameter($status)));
        
        return $this->findEntities($qb);
    }

    /**
     * Update goal progress
     * @param FinancialGoal $goal
     * @param float $amount
     * @return FinancialGoal
     */
    public function updateProgress(FinancialGoal $goal, float $amount): FinancialGoal {
        $goal->updateProgress($amount);
        return $this->update($goal);
    }

    /**
     * Delete expired or completed goals
     * @param \DateTime $cutoffDate
     * @return int Number of deleted goals
     */
    public function deleteCompletedGoals(\DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where(
               $qb->expr()->orX(
                   $qb->expr()->eq('status', $qb->createNamedParameter('completed')),
                   $qb->expr()->lt('target_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE))
               )
           );
        
        return $qb->executeStatement();
    }
}
