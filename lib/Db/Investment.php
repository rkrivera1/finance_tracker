<?php
namespace OCA\FinanceTracker\Db;

use OCP\AppFramework\Db\Entity;

class Investment extends Entity {
    protected $id;
    protected $userId;
    protected $name;
    protected $ticker;
    protected $shares;
    protected $purchasePrice;
    protected $purchaseDate;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('userId', 'string');
        $this->addType('name', 'string');
        $this->addType('ticker', 'string');
        $this->addType('shares', 'float');
        $this->addType('purchasePrice', 'float');
        $this->addType('purchaseDate', 'date');
    }

    public function setUserId(string $userId) {
        $this->userId = $userId;
    }

    public function getUserId(): string {
        return $this->userId;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setTicker(string $ticker) {
        $this->ticker = $ticker;
    }

    public function getTicker(): string {
        return $this->ticker;
    }

    public function setShares(float $shares) {
        $this->shares = $shares;
    }

    public function getShares(): float {
        return $this->shares;
    }

    public function setPurchasePrice(float $purchasePrice) {
        $this->purchasePrice = $purchasePrice;
    }

    public function getPurchasePrice(): float {
        return $this->purchasePrice;
    }

    public function setPurchaseDate(\DateTime $purchaseDate) {
        $this->purchaseDate = $purchaseDate;
    }

    public function getPurchaseDate(): \DateTime {
        return $this->purchaseDate;
    }
}
