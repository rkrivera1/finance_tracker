<?php
// Ensure we're in a Nextcloud testing environment
define('PHPUNIT_RUNNING', true);
define('OC', true);
define('OC_APP', true);

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Manually include Nextcloud core classes
$ocpPath = __DIR__ . '/../vendor/nextcloud/ocp';
if (file_exists($ocpPath)) {
    require_once $ocpPath . '/OCP/AppFramework/Db/Entity.php';
}

// Initialize test environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../build/logs/php-error.log');

// Mock OC class if not defined
if (!class_exists('OC')) {
    class OC {
        public static $server;
        public static function getUser() {
            return 'testuser';
        }
    }
}

// If Entity class is not available, create a mock
if (!class_exists('\OCP\AppFramework\Db\Entity')) {
    class MockEntity {
        protected $id;
        protected $userId;

        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            $this->id = $id;
            return $this;
        }

        public function getUserId() {
            return $this->userId;
        }

        public function setUserId($userId) {
            $this->userId = $userId;
            return $this;
        }
    }
}

// Basic dependency injection container mock
class MockContainer {
    private $services = [];

    public function register($serviceName, $service) {
        $this->services[$serviceName] = $service;
    }

    public function get($serviceName) {
        return $this->services[$serviceName] ?? null;
    }
}

// Ensure build logs directory exists
$logDir = __DIR__ . '/../build/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
