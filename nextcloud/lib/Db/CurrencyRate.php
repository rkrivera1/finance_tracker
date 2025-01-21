<?php
namespace OCA\FinanceTracker\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class CurrencyRate extends Entity implements JsonSerializable {
    protected $baseCurrency;
    protected $targetCurrency;
    protected $exchangeRate;
    protected $lastUpdated;
    protected $source;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('exchangeRate', 'float');
        $this->addType('lastUpdated', 'datetime');
    }

    public function isStale(\DateInterval $maxAge): bool {
        $now = new \DateTime();
        $lastUpdated = $this->getLastUpdated();
        
        return $now->diff($lastUpdated) > $maxAge;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'baseCurrency' => $this->getBaseCurrency(),
            'targetCurrency' => $this->getTargetCurrency(),
            'exchangeRate' => $this->getExchangeRate(),
            'lastUpdated' => $this->getLastUpdated(),
            'source' => $this->getSource()
        ];
    }
}
