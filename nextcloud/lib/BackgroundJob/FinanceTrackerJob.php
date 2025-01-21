<?php
namespace OCA\FinanceTracker\BackgroundJob;

use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCA\FinanceTracker\Service\NotificationService;
use OCA\FinanceTracker\Db\TransactionMapper;
use OCA\FinanceTracker\Db\BudgetMapper;
use OCP\IConfig;

class FinanceTrackerJob extends TimedJob {
    private $notificationService;
    private $transactionMapper;
    private $budgetMapper;
    private $config;

    public function __construct(
        ITimeFactory $time,
        NotificationService $notificationService,
        TransactionMapper $transactionMapper,
        BudgetMapper $budgetMapper,
        IConfig $config
    ) {
        parent::__construct($time);
        
        // Run daily
        $this->setInterval(24 * 60 * 60);
        
        $this->notificationService = $notificationService;
        $this->transactionMapper = $transactionMapper;
        $this->budgetMapper = $budgetMapper;
        $this->config = $config;
    }

    /**
     * Main background job execution
     * @param mixed $argument
     */
    protected function run($argument) {
        // Send budget alerts
        $this->notificationService->sendBudgetAlerts();

        // Send monthly summary
        $this->notificationService->sendMonthlySummary();

        // Perform data cleanup
        $this->performDataCleanup();
    }

    /**
     * Cleanup old data based on retention policy
     */
    private function performDataCleanup() {
        // Get global data retention period
        $retentionPeriod = $this->config->getAppValue(
            'finance_tracker', 
            'data_retention_period', 
            '365'
        );
        $retentionPeriod = intval($retentionPeriod);

        // Calculate cutoff date
        $cutoffDate = new \DateTime();
        $cutoffDate->modify("-{$retentionPeriod} days");

        // Delete old transactions
        $this->transactionMapper->deleteOldTransactions($cutoffDate);

        // Delete expired budgets
        $this->budgetMapper->deleteExpiredBudgets($cutoffDate);
    }
}
