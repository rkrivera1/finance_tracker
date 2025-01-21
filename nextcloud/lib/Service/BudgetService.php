<?php
namespace OCA\FinanceTracker\Service;

use Exception;
use OCA\FinanceTracker\Db\Budget;
use OCA\FinanceTracker\Db\BudgetMapper;
use OCA\FinanceTracker\Db\TransactionMapper;
use OCP\AppFramework\Db\DoesNotExistException;

class BudgetService {
    private $budgetMapper;
    private $transactionMapper;

    public function __construct(BudgetMapper $budgetMapper, TransactionMapper $transactionMapper) {
        $this->budgetMapper = $budgetMapper;
        $this->transactionMapper = $transactionMapper;
    }

    /**
     * Get all budgets for a user
     * @param string $userId
     * @return Budget[]
     */
    public function findAll(string $userId): array {
        return $this->budgetMapper->findAll($userId);
    }

    /**
     * Create a new budget
     * @param string $userId
     * @param string $category
     * @param float $budgetAmount
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Budget
     * @throws Exception
     */
    public function create(
        string $userId, 
        string $category, 
        float $budgetAmount, 
        \DateTime $startDate, 
        \DateTime $endDate
    ): Budget {
        // Validate budget parameters
        if ($budgetAmount <= 0) {
            throw new Exception('Budget amount must be positive');
        }

        if ($startDate > $endDate) {
            throw new Exception('Start date must be before end date');
        }

        // Check for existing budget in the same category and time period
        $existingBudget = $this->budgetMapper->findByCategory($userId, $category);
        if ($existingBudget) {
            throw new Exception('A budget for this category already exists');
        }

        $budget = new Budget();
        $budget->setUserId($userId);
        $budget->setCategory($category);
        $budget->setBudgetAmount($budgetAmount);
        $budget->setStartDate($startDate);
        $budget->setEndDate($endDate);

        return $this->budgetMapper->insert($budget);
    }

    /**
     * Get budget status for a specific category
     * @param string $userId
     * @param string $category
     * @return array
     */
    public function getBudgetStatus(string $userId, string $category): array {
        $currentDate = new \DateTime();
        
        // Find active budget for the category
        $budget = $this->budgetMapper->findByCategory($userId, $category);
        
        if (!$budget) {
            return [
                'status' => 'no_budget',
                'message' => 'No budget set for this category'
            ];
        }

        // Get transactions for this category in the budget period
        $transactions = $this->transactionMapper->findByCategory($userId, $category);
        
        $totalSpent = array_reduce($transactions, function($carry, $transaction) use ($budget) {
            // Only count transactions within budget period
            $transactionDate = $transaction->getTransactionDate();
            if ($transactionDate >= $budget->getStartDate() && $transactionDate <= $budget->getEndDate()) {
                return $carry + $transaction->getAmount();
            }
            return $carry;
        }, 0);

        $remainingBudget = $budget->getBudgetAmount() - $totalSpent;
        $percentageSpent = ($totalSpent / $budget->getBudgetAmount()) * 100;

        return [
            'status' => 'active',
            'budgetAmount' => $budget->getBudgetAmount(),
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget,
            'percentageSpent' => $percentageSpent,
            'startDate' => $budget->getStartDate(),
            'endDate' => $budget->getEndDate()
        ];
    }

    /**
     * Delete a budget
     * @param int $id
     * @param string $userId
     * @throws DoesNotExistException
     */
    public function delete(int $id, string $userId) {
        try {
            $budget = $this->budgetMapper->findEntity($id);
            
            // Ensure the budget belongs to the user
            if ($budget->getUserId() !== $userId) {
                throw new Exception('Not authorized to delete this budget');
            }

            $this->budgetMapper->delete($budget);
        } catch (DoesNotExistException $e) {
            throw $e;
        }
    }
}
