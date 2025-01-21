<?php
namespace OCA\FinanceTracker\Service;

use OCA\FinanceTracker\Db\TransactionMapper;
use OCA\FinanceTracker\Db\BudgetMapper;

class ReportService {
    private $transactionMapper;
    private $budgetMapper;

    public function __construct(TransactionMapper $transactionMapper, BudgetMapper $budgetMapper) {
        $this->transactionMapper = $transactionMapper;
        $this->budgetMapper = $budgetMapper;
    }

    /**
     * Generate monthly spending report
     * @param string $userId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public function generateMonthlyReport(string $userId, \DateTime $startDate, \DateTime $endDate): array {
        // Get all transactions in the date range
        $transactions = $this->transactionMapper->findAll($userId);
        
        // Filter transactions within the specified date range
        $filteredTransactions = array_filter($transactions, function($transaction) use ($startDate, $endDate) {
            $transactionDate = $transaction->getTransactionDate();
            return $transactionDate >= $startDate && $transactionDate <= $endDate;
        });

        // Group transactions by category
        $categorySummary = [];
        foreach ($filteredTransactions as $transaction) {
            $category = $transaction->getCategory();
            $amount = $transaction->getAmount();
            
            if (!isset($categorySummary[$category])) {
                $categorySummary[$category] = [
                    'total' => 0,
                    'transactions' => []
                ];
            }
            
            $categorySummary[$category]['total'] += $amount;
            $categorySummary[$category]['transactions'][] = $transaction;
        }

        // Get budgets for the period
        $activeBudgets = $this->budgetMapper->findActiveBudgets($userId, new \DateTime());

        // Compare spending to budgets
        $budgetComparison = [];
        foreach ($activeBudgets as $budget) {
            $category = $budget->getCategory();
            $budgetAmount = $budget->getBudgetAmount();
            $spent = $categorySummary[$category]['total'] ?? 0;
            
            $budgetComparison[$category] = [
                'budgetAmount' => $budgetAmount,
                'spent' => $spent,
                'remaining' => $budgetAmount - $spent,
                'percentageSpent' => ($spent / $budgetAmount) * 100
            ];
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'categorySummary' => $categorySummary,
            'budgetComparison' => $budgetComparison,
            'totalSpent' => array_sum(array_column($categorySummary, 'total'))
        ];
    }

    /**
     * Generate annual spending trends
     * @param string $userId
     * @param int $year
     * @return array
     */
    public function generateAnnualTrends(string $userId, int $year): array {
        $transactions = $this->transactionMapper->findAll($userId);
        
        // Filter transactions for the specified year
        $yearTransactions = array_filter($transactions, function($transaction) use ($year) {
            return $transaction->getTransactionDate()->format('Y') == $year;
        });

        // Monthly breakdown
        $monthlyTrends = array_fill(1, 12, 0);
        foreach ($yearTransactions as $transaction) {
            $month = $transaction->getTransactionDate()->format('n');
            $monthlyTrends[$month] += $transaction->getAmount();
        }

        // Category trends
        $categoryTrends = [];
        foreach ($yearTransactions as $transaction) {
            $category = $transaction->getCategory();
            if (!isset($categoryTrends[$category])) {
                $categoryTrends[$category] = 0;
            }
            $categoryTrends[$category] += $transaction->getAmount();
        }

        return [
            'year' => $year,
            'monthlyTrends' => $monthlyTrends,
            'categoryTrends' => $categoryTrends,
            'totalSpent' => array_sum($monthlyTrends)
        ];
    }
}
