<?php
namespace OCA\FinanceTracker\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {
    /** @var IConfig */
    private $config;

    /** @var string */
    private $appName;

    public function __construct(
        IConfig $config,
        string $AppName
    ) {
        $this->config = $config;
        $this->appName = $AppName;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm(): TemplateResponse {
        $parameters = [
            'app_name' => $this->appName,
            'currency_default' => $this->config->getAppValue(
                $this->appName, 
                'default_currency', 
                'USD'
            ),
            'data_retention_period' => $this->config->getAppValue(
                $this->appName, 
                'data_retention_period', 
                '365'
            )
        ];

        return new TemplateResponse($this->appName, 'settings/admin', $parameters);
    }

    /**
     * @return string
     */
    public function getSection(): string {
        return 'finance';
    }

    /**
     * @return int
     */
    public function getPriority(): int {
        return 50;
    }
}
