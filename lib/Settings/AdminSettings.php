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
        $parameters = [
            // Stock API Provider Settings
            'alpha_vantage_api_key' => $this->config->getAppValue(
                $this->appName, 
                'alpha_vantage_api_key', 
                ''
            ),
            'finnhub_api_key' => $this->config->getAppValue(
                $this->appName, 
                'finnhub_api_key', 
                ''
            ),
            'twelve_data_api_key' => $this->config->getAppValue(
                $this->appName, 
                'twelve_data_api_key', 
                ''
            ),
            
            // Additional stock tracking settings
            'stock_update_frequency' => $this->config->getAppValue(
                $this->appName, 
                'stock_update_frequency', 
                '5' // Default 5 minutes
            ),
            'stock_tracking_enabled' => $this->config->getAppValue(
                $this->appName, 
                'stock_tracking_enabled', 
                'true'
            )
        ];

        return new TemplateResponse(
            $this->appName, 
            'settings/admin', 
            $parameters
        );
    }

    public function getSection(): string {
        return 'finance_tracker';
    }

    public function getPriority(): int {
        return 10;
    }
}
