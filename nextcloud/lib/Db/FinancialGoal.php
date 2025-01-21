<?php
namespace OCA\FinanceTracker\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class FinancialGoal extends Entity implements JsonSerializable {
    protected $userId;
    protected $title;
    protected $targetAmount;
    protected $currentAmount;
    protected $category;
    protected $startDate;
    protected $targetDate;
    protected $status;

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('targetAmount', 'float');
        $this->addType('currentAmount', 'float');
        $this->addType('startDate', 'datetime');
        $this->addType('targetDate', 'datetime');
    }

    public function updateProgress(float $amount) {
        $this->currentAmount += $amount;
        $this->updateStatus();
    }

    private function updateStatus() {
        $now = new \DateTime();
        
        if ($this->currentAmount >= $this->targetAmount) {
            $this->status = 'completed';
        } elseif ($now > $this->targetDate) {
            $this->status = 'failed';
        } else {
            $percentComplete = ($this->currentAmount / $this->targetAmount) * 100;
            
            if ($percentComplete >= 100) {
                $this->status = 'completed';
            } elseif ($percentComplete >= 75) {
                $this->status = 'on_track';
            } elseif ($percentComplete >= 50) {
                $this->status = 'in_progress';
            } else {
                $this->status = 'behind';
            }
        }
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'title' => $this->getTitle(),
            'targetAmount' => $this->getTargetAmount(),
            'currentAmount' => $this->getCurrentAmount(),
            'category' => $this->getCategory(),
            'startDate' => $this->getStartDate(),
            'targetDate' => $this->getTargetDate(),
            'status' => $this->getStatus(),
            'percentComplete' => round(($this->getCurrentAmount() / $this->getTargetAmount()) * 100, 2)
        ];
    }
}
