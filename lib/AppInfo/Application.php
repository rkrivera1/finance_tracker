<?php
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IConfig;
use OCP\BackgroundJob\IJobList;

use OCA\FinanceTracker\Service\AccountService;
use OCA\FinanceTracker\Service\TransactionService;
use OCA\FinanceTracker\Service\BudgetService;
use OCA\FinanceTracker\Service\InvestmentService;
use OCA\FinanceTracker\BackgroundJob\DataRetentionJob;

class Application extends App implements IBootstrap {
    public const APP_ID = 'finance_tracker';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Service registrations
        $context->registerService(AccountService::class, function($c) {
            return new AccountService(
                $c->query(\OCA\FinanceTracker\Db\AccountMapper::class),
                $c->query(\OCP\IUserSession::class),
                $c->query(\Psr\Log\LoggerInterface::class)
            );
        });

        $context->registerService(TransactionService::class, function($c) {
            return new TransactionService(
                $c->query(\OCA\FinanceTracker\Db\TransactionMapper::class),
                $c->query(\OCP\IUserSession::class),
                $c->query(\Psr\Log\LoggerInterface::class)
            );
        });

        $context->registerService(BudgetService::class, function($c) {
            return new BudgetService(
                $c->query(\OCA\FinanceTracker\Db\BudgetMapper::class),
                $c->query(\OCP\IUserSession::class),
                $c->query(\Psr\Log\LoggerInterface::class)
            );
        });

        $context->registerService(InvestmentService::class, function($c) {
            return new InvestmentService(
                $c->query(\OCA\FinanceTracker\Db\InvestmentMapper::class),
                $c->query(\OCP\IUserSession::class),
                $c->query(\Psr\Log\LoggerInterface::class)
            );
        });

        // Register background job
        $context->registerService(DataRetentionJob::class, function($c) {
            return new DataRetentionJob(
                $c->query(\OCP\AppFramework\Utility\ITimeFactory::class),
                $c->query(\OCP\IConfig::class),
                $c->query(\OCP\IDBConnection::class),
                $c->query(\Psr\Log\LoggerInterface::class)
            );
        });

        // Controller registrations
        $context->registerController(\OCA\FinanceTracker\Controller\PageController::class, function($c) {
            return new \OCA\FinanceTracker\Controller\PageController(
                $c->query(\OCP\IRequest::class),
                $c->query(\OCP\IURLGenerator::class)
            );
        });

        $context->registerController(\OCA\FinanceTracker\Controller\AccountController::class, function($c) {
            return new \OCA\FinanceTracker\Controller\AccountController(
                $c->query(\OCP\IRequest::class),
                $c->query(AccountService::class)
            );
        });

        $context->registerController(\OCA\FinanceTracker\Controller\TransactionController::class, function($c) {
            return new \OCA\FinanceTracker\Controller\TransactionController(
                $c->query(\OCP\IRequest::class),
                $c->query(TransactionService::class)
            );
        });

        $context->registerController(\OCA\FinanceTracker\Controller\BudgetController::class, function($c) {
            return new \OCA\FinanceTracker\Controller\BudgetController(
                $c->query(\OCP\IRequest::class),
                $c->query(BudgetService::class)
            );
        });

        $context->registerController(\OCA\FinanceTracker\Controller\InvestmentController::class, function($c) {
            return new \OCA\FinanceTracker\Controller\InvestmentController(
                $c->query(\OCP\IRequest::class),
                $c->query(InvestmentService::class)
            );
        });

        $context->registerController(\OCA\FinanceTracker\Controller\SettingsController::class, function($c) {
            return new \OCA\FinanceTracker\Controller\SettingsController(
                $c->query(\OCP\IRequest::class),
                $c->query(\OCP\IConfig::class),
                $c->query(\OCP\IUserSession::class)
            );
        });
    }

    public function boot(IBootContext $context): void {
        // Set default settings if not already set
        $appContainer = $context->getAppContainer();
        /** @var IConfig $config */
        $config = $appContainer->get(IConfig::class);

        // Default admin settings
        $this->setDefaultAdminSettings($config);

        // Register background job
        $jobList = $context->getAppContainer()->get(IJobList::class);
        $jobList->add(DataRetentionJob::class);

        // Navigation menu
        $context->getAppContainer()
            ->get(\OCP\INavigationManager::class)
            ->add(function () use ($context) {
                $urlGenerator = $context->getAppContainer()->get(\OCP\IURLGenerator::class);
                $l10n = $context->getAppContainer()->get(\OCP\IL10N::class);

                return [
                    'id' => self::APP_ID,
                    'order' => 10,
                    'href' => $urlGenerator->linkToRoute(self::APP_ID . '.page.index'),
                    'icon' => $urlGenerator->imagePath(self::APP_ID, 'app-dark.svg'),
                    'name' => $l10n->t('Finance Tracker')
                ];
            });
    }

    /**
     * Set default admin settings if not already configured
     * 
     * @param IConfig $config
     */
    private function setDefaultAdminSettings(IConfig $config): void {
        $defaults = [
            'default_currency' => 'USD',
            'data_retention_period' => 365,
            'anonymize_data' => false
        ];

        foreach ($defaults as $key => $value) {
            // Only set if the value doesn't already exist
            if ($config->getAppValue(self::APP_ID, $key) === '') {
                $config->setAppValue(self::APP_ID, $key, 
                    is_bool($value) ? ($value ? 'true' : 'false') : (string)$value
                );
            }
        }
    }
}
