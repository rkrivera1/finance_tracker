<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class InvestmentMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_investments', Investment::class);
    }

    /**
     * Find all investments for a user
     * @param string $userId
     * @return Investment[]
     */
    public function findAll(string $userId): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return $this->findEntities($qb);
    }

    /**
     * Find investments by type
     * @param string $userId
     * @param string $type
     * @return Investment[]
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
     * Find investments by sector
     * @param string $userId
     * @param string $sector
     * @return Investment[]
     */
    public function findBySector(string $userId, string $sector): array {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->andWhere($qb->expr()->eq('sector', $qb->createNamedParameter($sector)));
        
        return $this->findEntities($qb);
    }

    /**
     * Update current price for an investment
     * @param int $investmentId
     * @param float $currentPrice
     * @return Investment
     */
    public function updateCurrentPrice(int $investmentId, float $currentPrice): Investment {
        $investment = $this->find($investmentId);
        
        $investment->setCurrentPrice($currentPrice);
        $investment->setLastUpdateDate(new \DateTime());
        
        return $this->update($investment);
    }

    /**
     * Calculate total portfolio value
     * @param string $userId
     * @return float
     */
    public function calculatePortfolioValue(string $userId): float {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select($qb->func()->sum(
            $qb->expr()->mul('current_price', 'quantity')
        ))
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return (float)$qb->executeQuery()->fetchOne();
    }

    /**
     * Calculate total gain/loss for portfolio
     * @param string $userId
     * @return float
     */
    public function calculatePortfolioGainLoss(string $userId): float {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select(
            $qb->func()->sum(
                $qb->expr()->sub(
                    $qb->expr()->mul('current_price', 'quantity'),
                    $qb->expr()->mul('purchase_price', 'quantity')
                )
            )
        )
           ->from($this->getTableName())
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        
        return (float)$qb->executeQuery()->fetchOne();
    }

    /**
     * Delete old or inactive investments
     * @param \DateTime $cutoffDate
     * @return int Number of deleted investments
     */
    public function cleanupInactiveInvestments(\DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where(
               $qb->expr()->lt('last_update_date', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE))
           );
        
        return $qb->executeStatement();
    }

    /**
     * Create a new investment
     * @param Investment $investment
     * @return Investment
     */
    public function insert(\OCP\AppFramework\Db\Entity $investment): \OCP\AppFramework\Db\Entity {
        return parent::insert($investment);
    }
}
