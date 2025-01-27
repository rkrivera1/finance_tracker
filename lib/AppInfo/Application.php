<?php
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCP\IRequest;
use OCP\IDBConnection;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class Application extends App implements IBootstrap {
    public const APP_ID = 'finance_tracker';
    private $logger;

    public function __construct(
        array $urlParams = [], 
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        parent::__construct(self::APP_ID, $urlParams);
    }

    private function logError($message, $context = []) {
        if ($this->logger) {
            $this->logger->error($message, $context);
        } else {
            error_log($message . ' ' . json_encode($context));
        }
    }

    public function register(IRegistrationContext $context): void {
        try {
            // Comprehensive service registration with detailed logging
            $this->registerServices($context);
        } catch (\Throwable $e) {
            $this->logError('Finance Tracker Registration Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function registerServices(IRegistrationContext $context): void {
        // Mappers
        $mappers = [
            'AccountMapper' => \OCA\FinanceTracker\Db\AccountMapper::class,
            'BudgetMapper' => \OCA\FinanceTracker\Db\BudgetMapper::class,
            'TransactionMapper' => \OCA\FinanceTracker\Db\TransactionMapper::class,
            'InvestmentMapper' => \OCA\FinanceTracker\Db\InvestmentMapper::class
        ];

        foreach ($mappers as $serviceName => $mapperClass) {
            $context->registerService($serviceName, function($c) use ($mapperClass) {
                return new $mapperClass(
                    $c->query(IDBConnection::class)
                );
            });
        }

        // Controllers
        $controllers = [
            'PageController' => [\OCA\FinanceTracker\Controller\PageController::class, [self::APP_ID, IRequest::class]],
            'AccountController' => [
                \OCA\FinanceTracker\Controller\AccountController::class, 
                [self::APP_ID, IRequest::class, 'AccountMapper', IUserSession::class]
            ],
            'BudgetController' => [
                \OCA\FinanceTracker\Controller\BudgetController::class, 
                [self::APP_ID, IRequest::class, 'BudgetMapper', IUserSession::class]
            ],
            'TransactionController' => [
                \OCA\FinanceTracker\Controller\TransactionController::class, 
                [self::APP_ID, IRequest::class, 'TransactionMapper', IUserSession::class]
            ],
            'InvestmentController' => [
                \OCA\FinanceTracker\Controller\InvestmentController::class, 
                [self::APP_ID, IRequest::class, 'InvestmentMapper', IUserSession::class]
            ]
        ];

        foreach ($controllers as $serviceName => [$controllerClass, $dependencies]) {
            $context->registerService($serviceName, function($c) use ($controllerClass, $dependencies) {
                $args = [];
                foreach ($dependencies as $dep) {
                    if (is_string($dep)) {
                        if ($dep === self::APP_ID) {
                            $args[] = self::APP_ID;
                        } elseif ($dep === IRequest::class) {
                            $args[] = $c->query(IRequest::class);
                        } elseif ($dep === IUserSession::class) {
                            $args[] = $c->query(IUserSession::class)->getUser()->getUID();
                        } elseif (strpos($dep, 'Mapper') !== false) {
                            $args[] = $c->query($dep);
                        }
                    }
                }
                return new $controllerClass(...$args);
            });
        }
    }

    public function boot(IBootContext $context): void {
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
            $this->logError('Finance Tracker Navigation Registration Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
