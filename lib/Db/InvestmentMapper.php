<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

class InvestmentMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_tracker_investments', Investment::class);
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
        string $ticker, 
        float $shares, 
        float $purchasePrice
    ) {
        $investment = new Investment();
        $investment->setUserId($userId);
        $investment->setName($name);
        $investment->setTicker($ticker);
        $investment->setShares($shares);
        $investment->setPurchasePrice($purchasePrice);
        $investment->setPurchaseDate(new \DateTime());
        
        return $this->insert($investment);
    }
}
