<?php
namespace OCA\FinanceTracker\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends BaseController {
    /** @var IConfig */
    private $config;

    /** @var IUserSession */
    private $userSession;

    public function __construct(
        IRequest $request,
        IConfig $config,
        IUserSession $userSession
    ) {
        parent::__construct('finance_tracker', $request);
        $this->config = $config;
        $this->userSession = $userSession;
    }

    /**
     * Save admin-level settings
     * 
     * @NoAdminRequired
     * @PasswordConfirmationRequired
     * @param array $settings
     * @return JSONResponse
     */
    public function saveAdmin(array $settings): JSONResponse {
        try {
            // Validate admin settings
            $validatedSettings = $this->validateAdminSettings($settings);

            // Save each setting
            foreach ($validatedSettings as $key => $value) {
                $this->config->setAppValue('finance_tracker', $key, $value);
            }

            return $this->success([
                'message' => 'Admin settings saved successfully',
                'settings' => $validatedSettings
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), Http::STATUS_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->error('Failed to save admin settings', Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save personal-level settings
     * 
     * @NoAdminRequired
     * @param array $settings
     * @return JSONResponse
     */
    public function savePersonal(array $settings): JSONResponse {
        try {
            // Get current user
            $user = $this->userSession->getUser();
            if (!$user) {
                return $this->error('User not authenticated', Http::STATUS_UNAUTHORIZED);
            }
            $userId = $user->getUID();

            // Validate personal settings
            $validatedSettings = $this->validatePersonalSettings($settings);

            // Save each setting
            foreach ($validatedSettings as $key => $value) {
                $this->config->setUserValue(
                    $userId, 
                    'finance_tracker', 
                    $key, 
                    $value
                );
            }

            return $this->success([
                'message' => 'Personal settings saved successfully',
                'settings' => $validatedSettings
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), Http::STATUS_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->error('Failed to save personal settings', Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validate admin settings
     * 
     * @param array $settings
     * @return array Validated and sanitized settings
     * @throws \InvalidArgumentException
     */
    private function validateAdminSettings(array $settings): array {
        $validated = [];

        // Validate default currency
        if (isset($settings['default_currency'])) {
            $currencies = ['USD', 'EUR', 'GBP', 'JPY'];
            $currency = strtoupper($settings['default_currency']);
            if (!in_array($currency, $currencies)) {
                throw new \InvalidArgumentException('Invalid default currency');
            }
            $validated['default_currency'] = $currency;
        }

        // Validate data retention period
        if (isset($settings['data_retention_period'])) {
            $retention = filter_var(
                $settings['data_retention_period'], 
                FILTER_VALIDATE_INT, 
                [
                    'options' => [
                        'min_range' => 30, 
                        'max_range' => 365
                    ]
                ]
            );
            if ($retention === false) {
                throw new \InvalidArgumentException('Invalid data retention period');
            }
            $validated['data_retention_period'] = $retention;
        }

        // Validate anonymize data
        if (isset($settings['anonymize_data'])) {
            $validated['anonymize_data'] = filter_var(
                $settings['anonymize_data'], 
                FILTER_VALIDATE_BOOLEAN, 
                FILTER_NULL_ON_FAILURE
            ) ?? false;
        }

        return $validated;
    }

    /**
     * Validate personal settings
     * 
     * @param array $settings
     * @return array Validated and sanitized settings
     * @throws \InvalidArgumentException
     */
    private function validatePersonalSettings(array $settings): array {
        $validated = [];

        // Validate preferred currency
        if (isset($settings['preferred_currency'])) {
            $currencies = ['USD', 'EUR', 'GBP', 'JPY'];
            $currency = strtoupper($settings['preferred_currency']);
            if (!in_array($currency, $currencies)) {
                throw new \InvalidArgumentException('Invalid preferred currency');
            }
            $validated['preferred_currency'] = $currency;
        }

        // Validate budget notification threshold
        if (isset($settings['budget_notification_threshold'])) {
            $threshold = filter_var(
                $settings['budget_notification_threshold'], 
                FILTER_VALIDATE_INT, 
                [
                    'options' => [
                        'min_range' => 50, 
                        'max_range' => 100
                    ]
                ]
            );
            if ($threshold === false) {
                throw new \InvalidArgumentException('Invalid budget notification threshold');
            }
            $validated['budget_notification_threshold'] = $threshold;
        }

        // Validate notification toggles
        $notificationSettings = [
            'budget_notifications',
            'transaction_notifications',
            'anonymize_personal_data'
        ];

        foreach ($notificationSettings as $setting) {
            if (isset($settings[$setting])) {
                $validated[$setting] = filter_var(
                    $settings[$setting], 
                    FILTER_VALIDATE_BOOLEAN, 
                    FILTER_NULL_ON_FAILURE
                ) ?? false;
            }
        }

        return $validated;
    }
}
