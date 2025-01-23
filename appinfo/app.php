<?php
namespace OCA\FinanceTracker\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\INavigationManager;
use OCP\IURLGenerator;

class Application extends App implements IBootstrap {
    public function __construct(array $urlParams = []) {
        parent::__construct('finance_tracker', $urlParams);
    }

    public function register(IRegistrationContext $context): void {
    }

    public function boot(IBootContext $context): void {
        $container = $this->getContainer();
        $container->query(INavigationManager::class)->add(function () use ($container) {
            $urlGenerator = $container->query(IURLGenerator::class);
            return [
                'id' => 'finance_tracker',
                'order' => 10,
                'href' => $urlGenerator->linkToRoute('finance_tracker.page.index'),
                'icon' => $urlGenerator->imagePath('finance_tracker', 'app.svg'),
                'name' => 'Finance Tracker',
            ];
        });
    }
}
