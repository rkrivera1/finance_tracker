<?php
namespace OCA\FinanceTracker\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity as NextcloudEntity;

abstract class Entity extends NextcloudEntity implements JsonSerializable {
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
