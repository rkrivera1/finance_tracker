<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class TransactionMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_transactions', Transaction::class);
    }

    /**
     * Find all transactions for a user
     * @param string $userId
     * @return Transaction[]
     */
    public function findAll(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    /**
     * Find transactions by category
     * @param string $userId
     * @param string $category
     * @return Transaction[]
     */
    public function findByCategory(string $userId, string $category): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->eq('category', $qb->createNamedParameter($category)));
        
        return $this->findEntities($qb);
    }

    /**
     * Create a new transaction
     * @param \OCP\AppFramework\Db\Entity $transaction
     * @return \OCP\AppFramework\Db\Entity
     */
    public function insert(\OCP\AppFramework\Db\Entity $transaction): \OCP\AppFramework\Db\Entity {
        return parent::insert($transaction);
    }

    /**
     * Delete transactions older than the specified date
     * @param \DateTime $cutoffDate
     * @return int Number of deleted transactions
     */
    public function deleteOldTransactions(\DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where($qb->expr()->lt('transaction_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE)));
        
        return $qb->executeStatement();
    }

    /**
     * Delete transactions for a specific user older than the specified date
     * @param string $userId
     * @param \DateTime $cutoffDate
     * @return int Number of deleted transactions
     */
    public function deleteUserOldTransactions(string $userId, \DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->lt('transaction_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE)));
        
        return $qb->executeStatement();
    }
}
