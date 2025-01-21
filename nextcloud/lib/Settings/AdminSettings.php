<?php
namespace OCA\FinanceTracker\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IConfig;

class AdminSettings implements ISettings {
    private $config;
    private $appName;

    public function __construct(
        IConfig $config, 
        $AppName
    ) {
        $this->config = $config;
        $this->appName = $AppName;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm() {
        $adminSettings = [
            'global_currency' => $this->config->getAppValue(
                $this->appName, 
                'global_currency', 
                'USD'
            ),
            'data_retention_period' => $this->config->getAppValue(
                $this->appName, 
                'data_retention_period', 
                '365'
            ),
            'enable_multi_currency' => $this->config->getAppValue(
                $this->appName, 
                'enable_multi_currency', 
                'true'
            ),
            'default_budget_categories' => $this->config->getAppValue(
                $this->appName, 
                'default_budget_categories', 
                json_encode([
                    'Housing', 'Transportation', 'Food', 
                    'Utilities', 'Healthcare', 'Personal', 
                    'Education', 'Entertainment', 'Savings'
                ])
            ),
            'enable_anonymous_stats' => $this->config->getAppValue(
                $this->appName, 
                'enable_anonymous_stats', 
                'false'
            )
        ];

        return new TemplateResponse(
            $this->appName, 
            'settings/admin', 
            $adminSettings
        );
    }

    /**
     * @return string
     */
    public function getSection() {
        return 'finance_tracker';
    }

    /**
     * @return int
     */
    public function getPriority() {
        return 50;
    }

    /**
     * Save admin settings
     * @param array $params
     * @return void
     */
    public function saveAdminSettings(array $params) {
        // Validate and save global currency
        if (isset($params['global_currency'])) {
            $this->config->setAppValue(
                $this->appName, 
                'global_currency', 
                $params['global_currency']
            );
        }

        // Validate and save data retention period
        if (isset($params['data_retention_period'])) {
            $retentionPeriod = intval($params['data_retention_period']);
            if ($retentionPeriod > 0 && $retentionPeriod <= 3650) {
                $this->config->setAppValue(
                    $this->appName, 
                    'data_retention_period', 
                    strval($retentionPeriod)
                );
            }
        }

        // Enable/disable multi-currency support
        if (isset($params['enable_multi_currency'])) {
            $this->config->setAppValue(
                $this->appName, 
                'enable_multi_currency', 
                $params['enable_multi_currency'] ? 'true' : 'false'
            );
        }

        // Update default budget categories
        if (isset($params['default_budget_categories'])) {
            $categories = json_decode($params['default_budget_categories'], true);
            if (is_array($categories)) {
                $this->config->setAppValue(
                    $this->appName, 
                    'default_budget_categories', 
                    json_encode($categories)
                );
            }
        }

        // Enable/disable anonymous usage statistics
        if (isset($params['enable_anonymous_stats'])) {
            $this->config->setAppValue(
                $this->appName, 
                'enable_anonymous_stats', 
                $params['enable_anonymous_stats'] ? 'true' : 'false'
            );
        }
    }

    /**
     * Validate and sanitize input settings
     * @param array $params
     * @return array
     */
    private function sanitizeSettings(array $params): array {
        $sanitized = [];

        // Implement input validation and sanitization
        if (isset($params['global_currency'])) {
            // Basic currency code validation (3-letter ISO code)
            $currency = strtoupper(trim($params['global_currency']));
            if (preg_match('/^[A-Z]{3}$/', $currency)) {
                $sanitized['global_currency'] = $currency;
            }
        }

        // Add more validation for other settings...

        return $sanitized;
    }
}
