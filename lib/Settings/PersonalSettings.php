<?php
namespace OCA\FinanceTracker\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IUserSession;
use OCP\Settings\ISettings;

class PersonalSettings implements ISettings {
    /** @var IConfig */
    private $config;

    /** @var IUserSession */
    private $userSession;

    /** @var string */
    private $appName;

    public function __construct(
        IConfig $config,
        IUserSession $userSession,
        string $AppName
    ) {
        $this->config = $config;
        $this->userSession = $userSession;
        $this->appName = $AppName;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm(): TemplateResponse {
        $user = $this->userSession->getUser();
        $parameters = [
            'app_name' => $this->appName,
            'preferred_currency' => $this->config->getUserValue(
                $user->getUID(), 
                $this->appName, 
                'preferred_currency', 
                'USD'
            ),
            'budget_notification' => $this->config->getUserValue(
                $user->getUID(), 
                $this->appName, 
                'budget_notification', 
                'true'
            )
        ];

        return new TemplateResponse($this->appName, 'settings/personal', $parameters);
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
