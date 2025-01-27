<?php
namespace OCA\FinanceTracker\BackgroundJob;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use OCP\IConfig;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

class DataRetentionJob extends TimedJob {
    /** @var IConfig */
    private $config;

    /** @var IDBConnection */
    private $db;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ITimeFactory $time,
        IConfig $config,
        IDBConnection $db,
        LoggerInterface $logger
    ) {
        parent::__construct($time);
        
        // Run daily
        $this->setInterval(24 * 60 * 60);
        
        $this->config = $config;
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Execute data retention and cleanup job
     *
     * @param mixed $argument
     * @throws \Exception
     */
    protected function run($argument) {
        try {
            // Get data retention period from app settings
            $retentionPeriod = (int) $this->config->getAppValue(
                'finance_tracker', 
                'data_retention_period', 
                '365'
            );

            // Calculate cutoff date
            $cutoffDate = new \DateTime();
            $cutoffDate->sub(new \DateInterval("P{$retentionPeriod}D"));

            // Cleanup transactions
            $this->cleanupTransactions($cutoffDate);

            // Cleanup budgets
            $this->cleanupBudgets($cutoffDate);

            // Cleanup investments
            $this->cleanupInvestments($cutoffDate);

            $this->logger->info('Finance Tracker data retention job completed', [
                'retention_period' => $retentionPeriod,
                'cutoff_date' => $cutoffDate->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Finance Tracker data retention job failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Clean up old transactions
     *
     * @param \DateTime $cutoffDate
     */
    private function cleanupTransactions(\DateTime $cutoffDate): void {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete('finance_tracker_transactions')
           ->where($qb->expr()->lt('transaction_date', 
               $qb->createNamedParameter($cutoffDate->format('Y-m-d'))
           ));
        
        $deleted = $qb->executeStatement();
        
        $this->logger->info('Cleaned up old transactions', [
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Clean up old budgets
     *
     * @param \DateTime $cutoffDate
     */
    private function cleanupBudgets(\DateTime $cutoffDate): void {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete('finance_tracker_budgets')
           ->where($qb->expr()->lt('end_date', 
               $qb->createNamedParameter($cutoffDate->format('Y-m-d'))
           ));
        
        $deleted = $qb->executeStatement();
        
        $this->logger->info('Cleaned up old budgets', [
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Clean up old investments
     *
     * @param \DateTime $cutoffDate
     */
    private function cleanupInvestments(\DateTime $cutoffDate): void {
        $qb = $this->db->getQueryBuilder();
        
        $qb->delete('finance_tracker_investments')
           ->where($qb->expr()->lt('purchase_date', 
               $qb->createNamedParameter($cutoffDate->format('Y-m-d'))
           ));
        
        $deleted = $qb->executeStatement();
        
        $this->logger->info('Cleaned up old investments', [
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Optional: Archive deleted data before permanent removal
     * 
     * @param string $table
     * @param \DateTime $cutoffDate
     */
    private function archiveData(string $table, \DateTime $cutoffDate): void {
        // Implement data archiving logic if needed
        // This could involve moving old data to an archive table
        // or exporting to a CSV file
    }
}
