<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class CurrencyRateMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'finance_currency_rates', CurrencyRate::class);
    }

    /**
     * Find exchange rate between two currencies
     * @param string $baseCurrency
     * @param string $targetCurrency
     * @return CurrencyRate|null
     */
    public function findRate(string $baseCurrency, string $targetCurrency) {
        $qb = $this->db->getQueryBuilder();
        
        $qb->select('*')
           ->from($this->getTableName())
           ->where($qb->expr()->eq('base_currency', $qb->createNamedParameter($baseCurrency)))
           ->andWhere($qb->expr()->eq('target_currency', $qb->createNamedParameter($targetCurrency)));
        
        try {
            return $this->findEntity($qb);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
            return null;
        }
    }

    /**
     * Save or update currency rate
     * @param string $baseCurrency
     * @param string $targetCurrency
     * @param float $rate
     * @param string $source
     * @return CurrencyRate
     */
    public function saveOrUpdateRate(
        string $baseCurrency, 
        string $targetCurrency, 
        float $rate, 
        string $source = 'default'
    ): CurrencyRate {
        $existingRate = $this->findRate($baseCurrency, $targetCurrency);

        if ($existingRate) {
            // Update existing rate
            $existingRate->setExchangeRate($rate);
            $existingRate->setLastUpdated(new \DateTime());
            $existingRate->setSource($source);
            
            return $this->update($existingRate);
        }

        // Create new rate
        $newRate = new CurrencyRate();
        $newRate->setBaseCurrency($baseCurrency);
        $newRate->setTargetCurrency($targetCurrency);
        $newRate->setExchangeRate($rate);
        $newRate->setLastUpdated(new \DateTime());
        $newRate->setSource($source);

        return $this->insert($newRate);
    }

    /**
     * Clean up old currency rates
     * @param \DateTime $cutoffDate
     * @return int Number of deleted rates
     */
    public function cleanupOldRates(\DateTime $cutoffDate): int {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete($this->getTableName())
           ->where($qb->expr()->lt('last_updated', $qb->createNamedParameter($cutoffDate, IQueryBuilder::PARAM_DATE)));
        
        return $qb->executeStatement();
    }
}
