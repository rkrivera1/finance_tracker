<?php
namespace OCA\FinanceTracker\Service;

use Exception;
use OCA\FinanceTracker\Db\FinancialGoal;
use OCA\FinanceTracker\Db\FinancialGoalMapper;
use OCA\FinanceTracker\Db\TransactionMapper;

class FinancialGoalService {
    private $goalMapper;
    private $transactionMapper;

    public function __construct(
        FinancialGoalMapper $goalMapper, 
        TransactionMapper $transactionMapper
    ) {
        $this->goalMapper = $goalMapper;
        $this->transactionMapper = $transactionMapper;
    }

    /**
     * Create a new financial goal
     * @param string $userId
     * @param string $title
     * @param float $targetAmount
     * @param string $category
     * @param \DateTime $startDate
     * @param \DateTime $targetDate
     * @return FinancialGoal
     * @throws Exception
     */
    public function create(
        string $userId, 
        string $title, 
        float $targetAmount, 
        string $category,
        \DateTime $startDate, 
        \DateTime $targetDate
    ): FinancialGoal {
        // Validate goal parameters
        if ($targetAmount <= 0) {
            throw new Exception('Target amount must be positive');
        }

        if ($startDate >= $targetDate) {
            throw new Exception('Target date must be after start date');
        }

        $goal = new FinancialGoal();
        $goal->setUserId($userId);
        $goal->setTitle($title);
        $goal->setTargetAmount($targetAmount);
        $goal->setCurrentAmount(0);
        $goal->setCategory($category);
        $goal->setStartDate($startDate);
        $goal->setTargetDate($targetDate);
        $goal->setStatus('in_progress');

        return $this->goalMapper->insert($goal);
    }

    /**
     * Get all financial goals for a user
     * @param string $userId
     * @return FinancialGoal[]
     */
    public function findAll(string $userId): array {
        return $this->goalMapper->findAll($userId);
    }

    /**
     * Get active financial goals for a user
     * @param string $userId
     * @return FinancialGoal[]
     */
    public function findActiveGoals(string $userId): array {
        return $this->goalMapper->findActiveGoals($userId);
    }

    /**
     * Update goal progress based on transactions
     * @param int $goalId
     * @param string $userId
     * @return FinancialGoal
     * @throws Exception
     */
    public function updateGoalProgress(int $goalId, string $userId): FinancialGoal {
        // Retrieve the goal
        $goal = $this->goalMapper->find($goalId);

        // Ensure the goal belongs to the user
        if ($goal->getUserId() !== $userId) {
            throw new Exception('Not authorized to update this goal');
        }

        // Get transactions in the goal's category
        $transactions = $this->transactionMapper->findByCategory($userId, $goal->getCategory());

        // Calculate progress for transactions within goal period
        $progressAmount = array_reduce($transactions, function($carry, $transaction) use ($goal) {
            $transactionDate = $transaction->getTransactionDate();
            if ($transactionDate >= $goal->getStartDate() && 
                $transactionDate <= $goal->getTargetDate()) {
                return $carry + $transaction->getAmount();
            }
            return $carry;
        }, 0);

        // Update goal progress
        return $this->goalMapper->updateProgress($goal, $progressAmount);
    }

    /**
     * Delete a financial goal
     * @param int $goalId
     * @param string $userId
     * @throws Exception
     */
    public function delete(int $goalId, string $userId) {
        try {
            $goal = $this->goalMapper->find($goalId);
            
            // Ensure the goal belongs to the user
            if ($goal->getUserId() !== $userId) {
                throw new Exception('Not authorized to delete this goal');
            }

            $this->goalMapper->delete($goal);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
            throw new Exception('Goal not found');
        }
    }

    /**
     * Analyze goal achievement rates
     * @param string $userId
     * @return array
     */
    public function analyzeGoalAchievement(string $userId): array {
        $goals = $this->goalMapper->findAll($userId);

        $analysis = [
            'total_goals' => count($goals),
            'completed_goals' => 0,
            'failed_goals' => 0,
            'in_progress_goals' => 0,
            'average_completion_rate' => 0
        ];

        $completionRates = [];

        foreach ($goals as $goal) {
            switch ($goal->getStatus()) {
                case 'completed':
                    $analysis['completed_goals']++;
                    break;
                case 'failed':
                    $analysis['failed_goals']++;
                    break;
                default:
                    $analysis['in_progress_goals']++;
            }

            $completionRate = ($goal->getCurrentAmount() / $goal->getTargetAmount()) * 100;
            $completionRates[] = $completionRate;
        }

        // Calculate average completion rate
        $analysis['average_completion_rate'] = count($completionRates) > 0 
            ? array_sum($completionRates) / count($completionRates) 
            : 0;

        return $analysis;
    }
}
