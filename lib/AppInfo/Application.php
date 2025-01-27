<?php
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

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
        // Multiple potential autoload paths
        $autoloadPaths = [
            __DIR__ . '/../../vendor/autoload.php',
            __DIR__ . '/../../../vendor/autoload.php',
            __DIR__ . '/vendor/autoload.php'
        ];

        $autoloadFound = false;
        foreach ($autoloadPaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $autoloadFound = true;
                break;
            }
        }

        if (!$autoloadFound) {
            // Log the failure and provide a helpful error message
            \OC::$server->getLogger()->error(
                'Composer autoload not found. Please run "composer install" in the app directory.',
                ['app' => 'finance_tracker']
            );
            
            // Optionally, you can throw a more informative exception
            throw new \Exception(
                'Cannot include autoload. Please run "composer install" in the finance_tracker app directory. ' . 
                'Checked paths: ' . implode(', ', $autoloadPaths)
            );
        }

        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Register Services
        $context->registerService('AccountMapper', function($c) {
            return new AccountMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        $context->registerService('BudgetMapper', function($c) {
            return new BudgetMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        $context->registerService('TransactionMapper', function($c) {
            return new TransactionMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        $context->registerService('InvestmentMapper', function($c) {
            return new InvestmentMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        // Register Controllers
        $context->registerService('PageController', function($c) {
            return new PageController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class)
            );
        });

        $context->registerService('AccountController', function($c) {
            return new AccountController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query('AccountMapper'),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });

        $context->registerService('BudgetController', function($c) {
            return new BudgetController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query('BudgetMapper'),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });

        $context->registerService('TransactionController', function($c) {
            return new TransactionController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query('TransactionMapper'),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });

        $context->registerService('InvestmentController', function($c) {
            return new InvestmentController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query('InvestmentMapper'),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });
    }

    public function boot(IBootContext $context): void {
        // Register navigation entry
        $context->getAppContainer()->get(INavigationManager::class)->add(function () use ($context) {
            $urlGenerator = $context->getAppContainer()->get(IURLGenerator::class);
            $l10n = $context->getAppContainer()->get(IL10N::class);

            return [
                'id' => self::APP_ID,
                'order' => 10,
                'href' => $urlGenerator->linkToRoute('finance_tracker.page.index'),
                'icon' => $urlGenerator->imagePath(self::APP_ID, 'app-dark.svg'),
                'name' => $l10n->t('Finance Tracker')
            ];
        });

        // You can add additional boot-time configurations here
    }
}
