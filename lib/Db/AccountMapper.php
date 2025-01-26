<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class AccountMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'ft_accounts', Account::class);
    }

    public function findByUser(string $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    public function create(string $name, string $type, float $balance, string $userId) {
        $account = new Account();
        $account->setName($name);
        $account->setType($type);
        $account->setBalance($balance);
        $account->setUserId($userId);
        $account->setCreatedAt(new \DateTime());
        
        return $this->insert($account);
    }
}
