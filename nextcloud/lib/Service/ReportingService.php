<?php
namespace OCA\FinanceTracker\Service;

use OCA\FinanceTracker\Db\TransactionMapper;
use OCA\FinanceTracker\Db\BudgetMapper;

class ReportingService {
    private $transactionMapper;
    private $budgetMapper;

    public function __construct(
        TransactionMapper $transactionMapper,
        BudgetMapper $budgetMapper
    ) {
        $this->transactionMapper = $transactionMapper;
        $this->budgetMapper = $budgetMapper;
    }

    /**
     * Generate monthly spending report
     * @param string $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function generateMonthlyReport(string $userId, int $year, int $month): array {
        // Create start and end dates for the month
        $startDate = new \DateTime("{$year}-{$month}-01");
        $endDate = (clone $startDate)->modify('last day of this month');

        // Get transactions for the month
        $transactions = $this->transactionMapper->findByDateRange(
            $userId, 
            $startDate, 
            $endDate
        );

        // Categorize spending
        $categorizedSpending = $this->categorizeSpending($transactions);

        // Get budget for the month
        $monthlyBudget = $this->budgetMapper->findByMonth($userId, $year, $month);

        return [
            'totalSpending' => array_sum(array_column($categorizedSpending, 'total')),
            'categorizedSpending' => $categorizedSpending,
            'monthlyBudget' => $monthlyBudget ? $monthlyBudget->getAmount() : null,
            'budgetStatus' => $this->calculateBudgetStatus($categorizedSpending, $monthlyBudget)
        ];
    }

    /**
     * Generate annual spending trends
     * @param string $userId
     * @param int $year
     * @return array
     */
    public function generateAnnualTrends(string $userId, int $year): array {
        $annualTrends = [];

        // Analyze spending for each month
        for ($month = 1; $month <= 12; $month++) {
            $monthReport = $this->generateMonthlyReport($userId, $year, $month);
            $annualTrends[] = [
                'month' => $month,
                'totalSpending' => $monthReport['totalSpending'],
                'categorizedSpending' => $monthReport['categorizedSpending']
            ];
        }

        return [
            'year' => $year,
            'monthlyTrends' => $annualTrends,
            'yearlyTotal' => array_sum(array_column($annualTrends, 'totalSpending')),
            'averageMonthlySpending' => array_sum(array_column($annualTrends, 'totalSpending')) / 12
        ];
    }

    /**
     * Categorize spending from transactions
     * @param array $transactions
     * @return array
     */
    private function categorizeSpending(array $transactions): array {
        $categorizedSpending = [];

        foreach ($transactions as $transaction) {
            $category = $transaction->getCategory();
            $amount = $transaction->getAmount();

            if (!isset($categorizedSpending[$category])) {
                $categorizedSpending[$category] = [
                    'category' => $category,
                    'total' => 0,
                    'transactions' => []
                ];
            }

            $categorizedSpending[$category]['total'] += $amount;
            $categorizedSpending[$category]['transactions'][] = $transaction;
        }

        // Sort categories by total spending (descending)
        uasort($categorizedSpending, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return $categorizedSpending;
    }

    /**
     * Calculate budget status
     * @param array $categorizedSpending
     * @param mixed $monthlyBudget
     * @return string
     */
    private function calculateBudgetStatus(array $categorizedSpending, $monthlyBudget): string {
        if (!$monthlyBudget) {
            return 'no_budget';
        }

        $totalSpending = array_sum(array_column($categorizedSpending, 'total'));
        $budgetAmount = $monthlyBudget->getAmount();

        $percentageSpent = ($totalSpending / $budgetAmount) * 100;

        if ($percentageSpent <= 70) {
            return 'under_budget';
        } elseif ($percentageSpent <= 100) {
            return 'near_budget';
        } else {
            return 'over_budget';
        }
    }

    /**
     * Predict future spending based on historical data
     * @param string $userId
     * @param int $months
     * @return array
     */
    public function predictFutureSpending(string $userId, int $months = 3): array {
        $predictions = [];
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        // Analyze past 12 months of data
        $historicalData = $this->generateAnnualTrends($userId, $currentYear);

        // Simple moving average prediction
        $monthlyAverage = $historicalData['averageMonthlySpending'];

        for ($i = 1; $i <= $months; $i++) {
            $predictionMonth = $currentMonth + $i;
            $predictionYear = $currentYear + floor(($predictionMonth - 1) / 12);
            $predictionMonth = (($predictionMonth - 1) % 12) + 1;

            $predictions[] = [
                'year' => $predictionYear,
                'month' => $predictionMonth,
                'predictedSpending' => $monthlyAverage
            ];
        }

        return [
            'historicalAverage' => $monthlyAverage,
            'futurePredictions' => $predictions
        ];
    }
}
