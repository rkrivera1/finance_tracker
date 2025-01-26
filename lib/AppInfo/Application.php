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

class Application extends App implements IBootstrap {
    public const APP_ID = 'finance_tracker';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Register Controllers
        $context->registerController(PageController::class);
        $context->registerController(AccountController::class);
        $context->registerController(BudgetController::class);
        $context->registerController(TransactionController::class);
        $context->registerController(InvestmentController::class);

        // Register Mappers as Services
        $context->registerService(AccountMapper::class, function($c) {
            return new AccountMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        $context->registerService(BudgetMapper::class, function($c) {
            return new BudgetMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        $context->registerService(TransactionMapper::class, function($c) {
            return new TransactionMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });

        $context->registerService(InvestmentMapper::class, function($c) {
            return new InvestmentMapper(
                $c->query(\OCP\IDBConnection::class)
            );
        });
    }

    public function boot(IBootContext $context): void {
        // Optional: Add any boot-time logic here
    }
}
