<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class TransactionMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'ft_trans', Transaction::class);
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
        int $accountId, 
        string $description, 
        float $amount, 
        string $type
    ) {
        $transaction = new Transaction();
        $transaction->setUserId($userId);
        $transaction->setAccountId($accountId);
        $transaction->setDescription($description);
        $transaction->setAmount($amount);
        $transaction->setType($type);
        $transaction->setDate(new \DateTime());
        
        return $this->insert($transaction);
    }
}
