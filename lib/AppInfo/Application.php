<?php
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\IUserSession;

use OCA\FinanceTracker\Controller\PageController;
use OCA\FinanceTracker\Controller\AccountController;
use OCA\FinanceTracker\Controller\BudgetController;
use OCA\FinanceTracker\Controller\TransactionController;
use OCA\FinanceTracker\Controller\InvestmentController;

use OCA\FinanceTracker\Db\AccountMapper;
use OCA\FinanceTracker\Db\BudgetMapper;
use OCA\FinanceTracker\Db\TransactionMapper;
use OCA\FinanceTracker\Db\InvestmentMapper;

use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\IL10N;
use Exception;

class Application extends App implements IBootstrap {
    public const APP_ID = 'finance_tracker';

    public function __construct(array $urlParams = []) {
        // Attempt to load autoloader with detailed error handling
        $this->loadComposerAutoloader();
        
        parent::__construct(self::APP_ID, $urlParams);
    }

    private function loadComposerAutoloader(): void {
        // Potential autoload paths in a Nextcloud environment
        $possiblePaths = [
            __DIR__ . '/../../vendor/autoload.php',           // App-specific vendor directory
            __DIR__ . '/../../../vendor/autoload.php',        // Custom apps directory
            __DIR__ . '/vendor/autoload.php',                 // Fallback path
            '/var/www/html/custom_apps/finance_tracker/vendor/autoload.php', // Typical Nextcloud path
            '/var/www/nextcloud/custom_apps/finance_tracker/vendor/autoload.php' // Alternative Nextcloud path
        ];

        $loadedSuccessfully = false;
        $failedPaths = [];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                try {
                    require_once $path;
                    $loadedSuccessfully = true;
                    break;
                } catch (\Throwable $e) {
                    $failedPaths[$path] = $e->getMessage();
                }
            } else {
                $failedPaths[$path] = 'File does not exist';
            }
        }

        if (!$loadedSuccessfully) {
            // Detailed error logging
            $errorMessage = "Composer autoload failed. Checked paths:\n";
            foreach ($failedPaths as $path => $reason) {
                $errorMessage .= "- $path: $reason\n";
            }

            // Log the error using Nextcloud's logging mechanism
            \OC::$server->getLogger()->error(
                'Finance Tracker Autoload Error: ' . $errorMessage, 
                ['app' => self::APP_ID]
            );

            // Throw an exception with detailed information
            throw new \Exception(
                "Cannot include Composer autoload. Please run 'composer install' in the app directory.\n" . 
                $errorMessage
            );
        }
    }

    public function register(IRegistrationContext $context): void {
        // Simplified service registration with error handling
        try {
            $context->registerService('AccountMapper', function($c) {
                return new \OCA\FinanceTracker\Db\AccountMapper(
                    $c->query(IDBConnection::class)
                );
            });

            $context->registerService('BudgetMapper', function($c) {
                return new \OCA\FinanceTracker\Db\BudgetMapper(
                    $c->query(IDBConnection::class)
                );
            });

            $context->registerService('TransactionMapper', function($c) {
                return new \OCA\FinanceTracker\Db\TransactionMapper(
                    $c->query(IDBConnection::class)
                );
            });

            $context->registerService('InvestmentMapper', function($c) {
                return new \OCA\FinanceTracker\Db\InvestmentMapper(
                    $c->query(IDBConnection::class)
                );
            });

            // Register Controllers
            $context->registerService('PageController', function($c) {
                return new \OCA\FinanceTracker\Controller\PageController(
                    self::APP_ID,
                    $c->query(IRequest::class)
                );
            });

            $context->registerService('AccountController', function($c) {
                return new \OCA\FinanceTracker\Controller\AccountController(
                    self::APP_ID,
                    $c->query(IRequest::class),
                    $c->query('AccountMapper'),
                    $c->query(IUserSession::class)->getUser()->getUID()
                );
            });

            $context->registerService('BudgetController', function($c) {
                return new \OCA\FinanceTracker\Controller\BudgetController(
                    self::APP_ID,
                    $c->query(IRequest::class),
                    $c->query('BudgetMapper'),
                    $c->query(IUserSession::class)->getUser()->getUID()
                );
            });

            $context->registerService('TransactionController', function($c) {
                return new \OCA\FinanceTracker\Controller\TransactionController(
                    self::APP_ID,
                    $c->query(IRequest::class),
                    $c->query('TransactionMapper'),
                    $c->query(IUserSession::class)->getUser()->getUID()
                );
            });

            $context->registerService('InvestmentController', function($c) {
                return new \OCA\FinanceTracker\Controller\InvestmentController(
                    self::APP_ID,
                    $c->query(IRequest::class),
                    $c->query('InvestmentMapper'),
                    $c->query(IUserSession::class)->getUser()->getUID()
                );
            });
        } catch (\Throwable $e) {
            // Log any service registration errors
            \OC::$server->getLogger()->error(
                'Finance Tracker Service Registration Error: ' . $e->getMessage(), 
                [
                    'app' => self::APP_ID,
                    'exception' => $e
                ]
            );
            throw $e;
        }
    }

    public function boot(IBootContext $context): void {
        // Simplified navigation registration with error handling
        try {
            $context->getAppContainer()
                ->get(\OCP\INavigationManager::class)
                ->add(function () use ($context) {
                    $urlGenerator = $context->getAppContainer()->get(\OCP\IURLGenerator::class);
                    $l10n = $context->getAppContainer()->get(\OCP\IL10N::class);

                    return [
                        'id' => self::APP_ID,
                        'order' => 10,
                        'href' => $urlGenerator->linkToRoute('finance_tracker.page.index'),
                        'icon' => $urlGenerator->imagePath(self::APP_ID, 'app-dark.svg'),
                        'name' => $l10n->t('Finance Tracker')
                    ];
                });
        } catch (\Throwable $e) {
            // Log any navigation registration errors
            \OC::$server->getLogger()->error(
                'Finance Tracker Navigation Registration Error: ' . $e->getMessage(),
                [
                    'app' => self::APP_ID,
                    'exception' => $e
                ]
            );
        }
    }
}
