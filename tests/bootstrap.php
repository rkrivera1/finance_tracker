<?php
// Ensure we're in a Nextcloud testing environment
define('PHPUNIT_RUNNING', true);
define('OC', true);
define('OC_APP', true);

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Manually include Nextcloud core classes
require_once __DIR__ . '/../vendor/nextcloud/ocp/OCP/AppFramework/Db/Entity.php';

// Initialize test environment
error_reporting(E_ALL);
ini_set('display_errors', '1');

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
        }

        public function getUserId() {
            return $this->userId;
        }

        public function setUserId($userId) {
            $this->userId = $userId;
        }
    }
}
