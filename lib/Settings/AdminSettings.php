<?php
namespace OCA\FinanceTracker\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IConfig;

class AdminSettings implements ISettings {
    private $config;

    public function __construct(IConfig $config) {
        $this->config = $config;
    }

    public function getForm() {
        return new TemplateResponse('finance_tracker', 'admin', []);
    }

    public function getSection() {
        return 'additional';
    }

    public function getPriority() {
        return 50;
    }
}

