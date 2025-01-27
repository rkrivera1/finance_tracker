<?php

declare(strict_types=1);

if ((@include_once __DIR__ . '/vendor/autoload.php') === false) {
	throw new Exception('Cannot include autoload. Did you run install dependencies using composer?');
}

$app = new \OCA\FinanceTracker\AppInfo\Application();
$app->register();

// Register navigation entry
\OC::$server->getNavigationManager()->add(function () {
	return [
		'id' => 'finance_tracker',
		'order' => 10,
		'href' => \OC::$server->getURLGenerator()->linkToRoute('finance_tracker.page.index'),
		'icon' => \OC::$server->getURLGenerator()->imagePath('finance_tracker', 'app-dark.svg'),
		'name' => \OC::$server->getL10N('finance_tracker')->t('Finance Tracker')
	];
});