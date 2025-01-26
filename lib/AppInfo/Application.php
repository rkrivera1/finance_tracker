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
        // Register Services
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

        // Register Controllers as Services
        $context->registerService(PageController::class, function($c) {
            return new PageController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class)
            );
        });

        $context->registerService(AccountController::class, function($c) {
            return new AccountController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query(AccountMapper::class),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });

        $context->registerService(BudgetController::class, function($c) {
            return new BudgetController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query(BudgetMapper::class),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });

        $context->registerService(TransactionController::class, function($c) {
            return new TransactionController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query(TransactionMapper::class),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });

        $context->registerService(InvestmentController::class, function($c) {
            return new InvestmentController(
                self::APP_ID,
                $c->query(\OCP\IRequest::class),
                $c->query(InvestmentMapper::class),
                $c->query(\OCP\IUserSession::class)->getUser()->getUID()
            );
        });
    }

    public function boot(IBootContext $context): void {
        // Optional: Add any boot-time logic here
    }
}
