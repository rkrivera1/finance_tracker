<?php
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
    public const APP_ID = 'finance_tracker';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        // Register routes, controllers, etc.
    }

    public function boot(IBootContext $context): void {
        // Any bootstrapping logic
    }
}
