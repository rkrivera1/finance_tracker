<?php
namespace OCA\FinanceTracker\Lib;

use JsonSerializable;

class Investment implements JsonSerializable {
    private $id;
    private $userId;
    private $symbol;
    private $name;
    private $quantity;
    private $purchasePrice;
    private $purchaseDate;
    private $currentPrice;
    private $lastUpdated;

    public function __construct(
        ?int $id = null,
        ?string $userId = null,
        ?string $symbol = null,
        ?string $name = null,
        ?float $quantity = null,
        ?float $purchasePrice = null,
        ?string $purchaseDate = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->symbol = $symbol;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->purchasePrice = $purchasePrice;
        $this->purchaseDate = $purchaseDate;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?string { return $this->userId; }
    public function getSymbol(): ?string { return $this->symbol; }
    public function getName(): ?string { return $this->name; }
    public function getQuantity(): ?float { return $this->quantity; }
    public function getPurchasePrice(): ?float { return $this->purchasePrice; }
    public function getPurchaseDate(): ?string { return $this->purchaseDate; }
    public function getCurrentPrice(): ?float { return $this->currentPrice; }
    public function getLastUpdated(): ?string { return $this->lastUpdated; }

    // Setters
    public function setCurrentPrice(?float $currentPrice): void {
        $this->currentPrice = $currentPrice;
        $this->lastUpdated = date('Y-m-d H:i:s');
    }

    // Calculate investment performance
    public function calculateTotalValue(): float {
        return $this->quantity * ($this->currentPrice ?? $this->purchasePrice);
    }

    public function calculateGainLoss(): float {
        if ($this->currentPrice === null) {
            return 0.0;
        }
        
        $totalPurchaseCost = $this->quantity * $this->purchasePrice;
        $totalCurrentValue = $this->calculateTotalValue();
        
        return $totalCurrentValue - $totalPurchaseCost;
    }

    public function calculateGainLossPercentage(): float {
        if ($this->currentPrice === null || $this->purchasePrice === 0) {
            return 0.0;
        }
        
        $gainLoss = $this->calculateGainLoss();
        $totalPurchaseCost = $this->quantity * $this->purchasePrice;
        
        return ($gainLoss / $totalPurchaseCost) * 100;
    }

    // Validation method
    public function validate(): bool {
        // Basic validation
        if (empty($this->symbol) || $this->quantity <= 0 || $this->purchasePrice <= 0) {
            return false;
        }
        return true;
    }

    // JsonSerializable implementation
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'symbol' => $this->symbol,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'purchasePrice' => $this->purchasePrice,
            'purchaseDate' => $this->purchaseDate,
            'currentPrice' => $this->currentPrice,
            'lastUpdated' => $this->lastUpdated,
            'totalValue' => $this->calculateTotalValue(),
            'gainLoss' => $this->calculateGainLoss(),
            'gainLossPercentage' => $this->calculateGainLossPercentage()
        ];
    }
}
