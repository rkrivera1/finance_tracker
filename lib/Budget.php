<?php
namespace OCA\FinanceTracker\Lib;

use JsonSerializable;
use DateTime;
use InvalidArgumentException;

class Budget implements JsonSerializable {
    private $id;
    private $userId;
    private $name;
    private $category;
    private $amount;
    private $startDate;
    private $endDate;
    private $currentSpending;

    // Validation constants
    private const MIN_BUDGET_AMOUNT = 0.01;
    private const MAX_BUDGET_AMOUNT = 1000000;
    private const MAX_BUDGET_NAME_LENGTH = 100;
    private const VALID_CATEGORIES = [
        'groceries', 'dining', 'entertainment', 
        'utilities', 'transportation', 'housing', 
        'healthcare', 'education', 'personal', 'other'
    ];

    public function __construct(
        $id = null, 
        $userId = null, 
        $name = '', 
        $category = '', 
        $amount = 0.0, 
        $startDate = null, 
        $endDate = null, 
        $currentSpending = 0.0
    ) {
        $this->validate($name, $category, $amount, $startDate, $endDate);
        
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
        $this->category = strtolower($category);
        $this->amount = round(floatval($amount), 2);
        $this->startDate = $startDate ? new DateTime($startDate) : new DateTime();
        $this->endDate = $endDate ? new DateTime($endDate) : null;
        $this->currentSpending = round(floatval($currentSpending), 2);
    }

    private function validate($name, $category, $amount, $startDate, $endDate) {
        // Validate name
        if (empty($name) || strlen($name) > self::MAX_BUDGET_NAME_LENGTH) {
            throw new InvalidArgumentException("Budget name must be between 1 and " . self::MAX_BUDGET_NAME_LENGTH . " characters.");
        }

        // Validate category
        if (!in_array(strtolower($category), self::VALID_CATEGORIES)) {
            throw new InvalidArgumentException("Invalid budget category. Must be one of: " . implode(', ', self::VALID_CATEGORIES));
        }

        // Validate amount
        $amount = floatval($amount);
        if ($amount < self::MIN_BUDGET_AMOUNT || $amount > self::MAX_BUDGET_AMOUNT) {
            throw new InvalidArgumentException("Budget amount must be between " . self::MIN_BUDGET_AMOUNT . " and " . self::MAX_BUDGET_AMOUNT);
        }

        // Validate dates
        if ($startDate && $endDate) {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            if ($start > $end) {
                throw new InvalidArgumentException("Start date must be before or equal to end date.");
            }
        }
    }

    // Enhanced getters with type safety
    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?string { return $this->userId; }
    public function getName(): string { return $this->name; }
    public function getCategory(): string { return $this->category; }
    public function getAmount(): float { return $this->amount; }
    public function getStartDate(): DateTime { return $this->startDate; }
    public function getEndDate(): ?DateTime { return $this->endDate; }
    public function getCurrentSpending(): float { return $this->currentSpending; }

    // Enhanced budget calculation methods
    public function getRemainingBudget(): float {
        return max(0, round($this->amount - $this->currentSpending, 2));
    }

    public function getPercentageSpent(): float {
        if ($this->amount == 0) return 0.0;
        return round(($this->currentSpending / $this->amount) * 100, 2);
    }

    public function isOverBudget(): bool {
        return $this->currentSpending > $this->amount;
    }

    public function getDaysRemaining(): ?int {
        if (!$this->endDate) return null;
        
        $now = new DateTime();
        if ($now > $this->endDate) return 0;
        
        return $now->diff($this->endDate)->days;
    }

    public function updateCurrentSpending(float $amount): void {
        $this->currentSpending = round($amount, 2);
    }

    // Predictive spending method
    public function getPredictedOverspending(): ?float {
        $daysRemaining = $this->getDaysRemaining();
        if ($daysRemaining === null || $daysRemaining === 0) return null;

        $dailySpendRate = $this->currentSpending / (new DateTime())->diff($this->startDate)->days;
        $projectedTotalSpending = $dailySpendRate * $this->startDate->diff($this->endDate)->days;

        return max(0, round($projectedTotalSpending - $this->amount, 2));
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'amount' => $this->amount,
            'startDate' => $this->startDate->format('Y-m-d'),
            'endDate' => $this->endDate ? $this->endDate->format('Y-m-d') : null,
            'currentSpending' => $this->currentSpending,
            'remainingBudget' => $this->getRemainingBudget(),
            'percentageSpent' => $this->getPercentageSpent(),
            'isOverBudget' => $this->isOverBudget(),
            'daysRemaining' => $this->getDaysRemaining(),
            'predictedOverspending' => $this->getPredictedOverspending()
        ];
    }

    // Static method for creating budgets with validation
    public static function create(
        string $userId, 
        string $name, 
        string $category, 
        float $amount, 
        ?string $startDate = null, 
        ?string $endDate = null
    ): self {
        return new self(
            null, 
            $userId, 
            $name, 
            $category, 
            $amount, 
            $startDate, 
            $endDate
        );
    }
}
