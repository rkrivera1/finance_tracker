<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Settings\PersonalSettings;
use OCA\FinanceTracker\Settings\AdminSettings;
use OCP\AppFramework\Http;

class SettingsController extends Controller {
    private $personalSettings;
    private $adminSettings;

    public function __construct(
        $AppName,
        IRequest $request,
        PersonalSettings $personalSettings,
        AdminSettings $adminSettings
    ) {
        parent::__construct($AppName, $request);
        $this->personalSettings = $personalSettings;
        $this->adminSettings = $adminSettings;
    }

    /**
     * @NoAdminRequired
     * @param array $settings
     * @return DataResponse
     */
    public function savePersonalSettings(array $settings) {
        try {
            $this->personalSettings->savePersonalSettings($settings);
            return new DataResponse([
                'message' => 'Personal settings saved successfully'
            ], Http::STATUS_OK);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @param array $settings
     * @return DataResponse
     */
    public function saveAdminSettings(array $settings) {
        try {
            $this->adminSettings->saveAdminSettings($settings);
            return new DataResponse([
                'message' => 'Admin settings saved successfully'
            ], Http::STATUS_OK);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
