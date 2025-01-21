<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Investment extends Entity {
    protected $userId;
    protected $name;
    protected $type;
    protected $quantity;
    protected $purchasePrice;
    protected $currentPrice;
    protected $purchaseDate;
    protected $sector;
    protected $riskLevel;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('name', 'string');
        $this->addType('type', 'string');
        $this->addType('quantity', 'float');
        $this->addType('purchasePrice', 'float');
        $this->addType('currentPrice', 'float');
        $this->addType('purchaseDate', 'datetime');
        $this->addType('sector', 'string');
        $this->addType('riskLevel', 'string');
    }

    /**
     * Calculate total investment value
     * @return float
     */
    public function getCurrentValue(): float {
        return $this->quantity * $this->currentPrice;
    }

    /**
     * Calculate total gain/loss
     * @return float
     */
    public function getGainLoss(): float {
        $purchaseValue = $this->quantity * $this->purchasePrice;
        return $this->getCurrentValue() - $purchaseValue;
    }

    /**
     * Calculate percentage gain/loss
     * @return float
     */
    public function getGainLossPercentage(): float {
        $purchaseValue = $this->quantity * $this->purchasePrice;
        if ($purchaseValue == 0) {
            return 0;
        }
        return ($this->getGainLoss() / $purchaseValue) * 100;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'quantity' => $this->getQuantity(),
            'purchasePrice' => $this->getPurchasePrice(),
            'currentPrice' => $this->getCurrentPrice(),
            'purchaseDate' => $this->getPurchaseDate(),
            'sector' => $this->getSector(),
            'riskLevel' => $this->getRiskLevel(),
            'currentValue' => $this->getCurrentValue(),
            'gainLoss' => $this->getGainLoss(),
            'gainLossPercentage' => $this->getGainLossPercentage()
        ];
    }

    // Getters and Setters
    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $validTypes = ['stock', 'bond', 'mutual_fund', 'etf', 'cryptocurrency'];
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException('Invalid investment type');
        }
        $this->type = $type;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }
        $this->quantity = $quantity;
    }

    public function getPurchasePrice() {
        return $this->purchasePrice;
    }

    public function setPurchasePrice($purchasePrice) {
        if ($purchasePrice < 0) {
            throw new \InvalidArgumentException('Purchase price cannot be negative');
        }
        $this->purchasePrice = $purchasePrice;
    }

    public function getCurrentPrice() {
        return $this->currentPrice;
    }

    public function setCurrentPrice($currentPrice) {
        if ($currentPrice < 0) {
            throw new \InvalidArgumentException('Current price cannot be negative');
        }
        $this->currentPrice = $currentPrice;
    }

    public function getPurchaseDate() {
        return $this->purchaseDate;
    }

    public function setPurchaseDate($purchaseDate) {
        $this->purchaseDate = $purchaseDate;
    }

    public function getSector() {
        return $this->sector;
    }

    public function setSector($sector) {
        $validSectors = [
            'technology', 'finance', 'healthcare', 
            'energy', 'consumer_goods', 'real_estate'
        ];

        if (!in_array($sector, $validSectors)) {
            throw new \InvalidArgumentException('Invalid investment sector');
        }
        $this->sector = $sector;
    }

    public function getRiskLevel() {
        return $this->riskLevel;
    }

    public function setRiskLevel($riskLevel) {
        $validRiskLevels = ['low', 'medium', 'high'];
        if (!in_array($riskLevel, $validRiskLevels)) {
            throw new \InvalidArgumentException('Invalid risk level');
        }
        $this->riskLevel = $riskLevel;
    }
}
