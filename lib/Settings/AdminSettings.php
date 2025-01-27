<?php
namespace OCA\FinanceTracker\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {
    private $config;
    private $appName;

    public function __construct(
        IConfig $config,
        $appName
    ) {
        $this->config = $config;
        $this->appName = $appName;
    }

    public function getForm(): TemplateResponse {
        return new TemplateResponse('finance_tracker', 'settings/admin', [
            'alpha_vantage_api_key' => $this->config->getAppValue(
                'finance_tracker',
                'alpha_vantage_api_key',
                ''
            ),
            'stock_update_interval' => $this->config->getAppValue(
                'finance_tracker',
                'stock_update_interval',
                '5'
            )
        ]);
    }

    public function getSection(): string {
        return 'finance_tracker';
    }

    public function getPriority(): int {
        return 10;
    }
}
