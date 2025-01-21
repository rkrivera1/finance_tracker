<?php
/**
 * Nextcloud Finance Tracker App Registration
 */
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\BackgroundJob\IJobList;
use OCA\FinanceTracker\BackgroundJob\FinanceTrackerJob;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('finance_tracker', $urlParams);
        
        $container = $this->getContainer();
        
        // Register services and dependencies
        $container->registerService('FinanceTrackerJob', function($c) {
            return new FinanceTrackerJob(
                $c->query('TimeFactory'),
                $c->query('NotificationService'),
                $c->query('TransactionMapper'),
                $c->query('BudgetMapper'),
                $c->query('Config')
            );
        });

        // Register background job
        $jobList = $container->query(IJobList::class);
        $jobList->add(FinanceTrackerJob::class);
    }
}
