<?php
namespace OCA\FinanceTracker\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IConfig;
use OCP\IUserSession;

class PersonalSettings implements ISettings {
    private $config;
    private $userSession;
    private $appName;

    public function __construct(
        IConfig $config, 
        IUserSession $userSession, 
        $AppName
    ) {
        $this->config = $config;
        $this->userSession = $userSession;
        $this->appName = $AppName;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm() {
        $user = $this->userSession->getUser();
        
        if (!$user) {
            return new TemplateResponse($this->appName, 'settings/personal', []);
        }

        $userId = $user->getUID();

        $personalSettings = [
            'default_currency' => $this->config->getUserValue(
                $userId, 
                $this->appName, 
                'default_currency', 
                'USD'
            ),
            'transaction_categories' => $this->config->getUserValue(
                $userId, 
                $this->appName, 
                'transaction_categories', 
                json_encode([
                    'Groceries', 'Dining', 'Transportation', 
                    'Entertainment', 'Utilities', 'Rent/Mortgage'
                ])
            ),
            'budget_notification_threshold' => $this->config->getUserValue(
                $userId, 
                $this->appName, 
                'budget_notification_threshold', 
                '80'
            ),
            'financial_goal_tracking' => $this->config->getUserValue(
                $userId, 
                $this->appName, 
                'financial_goal_tracking', 
                'false'
            )
        ];

        return new TemplateResponse(
            $this->appName, 
            'settings/personal', 
            $personalSettings
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
     * Save personal settings
     * @param array $params
     * @return void
     */
    public function savePersonalSettings(array $params) {
        $user = $this->userSession->getUser();
        
        if (!$user) {
            return;
        }

        $userId = $user->getUID();

        // Validate and save each setting
        if (isset($params['default_currency'])) {
            $this->config->setUserValue(
                $userId, 
                $this->appName, 
                'default_currency', 
                $params['default_currency']
            );
        }

        if (isset($params['transaction_categories'])) {
            // Ensure categories are a valid JSON array
            $categories = json_decode($params['transaction_categories'], true);
            if (is_array($categories)) {
                $this->config->setUserValue(
                    $userId, 
                    $this->appName, 
                    'transaction_categories', 
                    json_encode($categories)
                );
            }
        }

        if (isset($params['budget_notification_threshold'])) {
            $threshold = intval($params['budget_notification_threshold']);
            if ($threshold >= 0 && $threshold <= 100) {
                $this->config->setUserValue(
                    $userId, 
                    $this->appName, 
                    'budget_notification_threshold', 
                    strval($threshold)
                );
            }
        }

        if (isset($params['financial_goal_tracking'])) {
            $this->config->setUserValue(
                $userId, 
                $this->appName, 
                'financial_goal_tracking', 
                $params['financial_goal_tracking'] ? 'true' : 'false'
            );
        }
    }
}
