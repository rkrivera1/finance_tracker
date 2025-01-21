<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class DebtMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_debts', Debt::class);
    }

    /**
     * Find all debts for a user
     * @param string $userId
     * @return Debt[]
     */
    public function findAll(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    /**
     * Find debts by type
     * @param string $userId
     * @param string $type
     * @return Debt[]
     */
    public function findByType(string $userId, string $type): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->eq('type', $qb->createNamedParameter($type)));
        
        return $this->findEntities($qb);
    }

    /**
     * Find overdue debts
     * @param string $userId
     * @return Debt[]
     */
    public function findOverdueDebts(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->lt('due_date', $qb->createNamedParameter(new \DateTime(), IQueryBuilder::PARAM_DATE)))
           ->andWhere($qb->expr()->gt('remaining_balance', $qb->createNamedParameter(0)));
        
        return $this->findEntities($qb);
    }

    /**
     * Update remaining balance
     * @param int $debtId
     * @param float $payment
     * @return Debt
     */
    public function makePayment(int $debtId, float $payment): Debt {
        $debt = $this->find($debtId);
        
        $newBalance = max(0, $debt->getRemainingBalance() - $payment);
        $debt->setRemainingBalance($newBalance);
        
        // Update status if paid off
        if ($newBalance <= 0) {
            $debt->setStatus('paid_off');
        }
        
        return $this->update($debt);
    }

    /**
     * Calculate total debt for a user
     * @param string $userId
     * @return float
     */
    public function calculateTotalDebt(string $userId): float {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select($qb->func()->sum('remaining_balance'))
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return (float)$qb->executeQuery()->fetchOne();
    }

    /**
     * Calculate total monthly payments
     * @param string $userId
     * @return float
     */
    public function calculateTotalMonthlyPayments(string $userId): float {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select($qb->func()->sum('minimum_payment'))
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return (float)$qb->executeQuery()->fetchOne();
    }

    /**
     * Clean up paid off debts
     * @param \DateTime $cutoffDate
     * @return int Number of deleted debts
     */
    public function cleanupPaidDebts(\DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where(
               $qb->expr()->orX(
                   $qb->expr()->eq('remaining_balance', $qb->createNamedParameter(0)),
                   $qb->expr()->lt('due_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE))
               )
           );
        
        return $qb->executeStatement();
    }

    /**
     * Create a new debt
     * @param Debt $debt
     * @return Debt
     */
    public function insert(\OCP\AppFramework\Db\Entity $debt): \OCP\AppFramework\Db\Entity {
        return parent::insert($debt);
    }
}
