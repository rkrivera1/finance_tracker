<?php
namespace OCA\FinanceTracker\Lib;

use DateTime;
use InvalidArgumentException;
use OCA\FinanceTracker\Db\TransactionMapper;
use OCA\FinanceTracker\Db\BudgetMapper;
use OCA\FinanceTracker\Db\AccountMapper;

class ReportService {
    private $transactionMapper;
    private $budgetMapper;
    private $accountMapper;

    // New constants for report types and export formats
    public const REPORT_TYPES = [
        'financial_overview',
        'trend_analysis',
        'budget_performance',
        'investment_performance',
        'tax_projection'
    ];

    public const EXPORT_FORMATS = [
        'csv',
        'json',
        'pdf'
    ];

    public function __construct(
        TransactionMapper $transactionMapper,
        BudgetMapper $budgetMapper,
        AccountMapper $accountMapper
    ) {
        $this->transactionMapper = $transactionMapper;
        $this->budgetMapper = $budgetMapper;
        $this->accountMapper = $accountMapper;
    }

    /**
     * Generate a comprehensive financial overview report
     * 
     * @param string $userId
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function generateFinancialOverview(
        string $userId, 
        ?DateTime $startDate = null, 
        ?DateTime $endDate = null
    ): array {
        // Set default date range if not provided (last 3 months)
        $startDate = $startDate ?? (new DateTime())->modify('-3 months');
        $endDate = $endDate ?? new DateTime();

        // Income and Expense Analysis
        $transactions = $this->transactionMapper->findByUserAndDateRange(
            $userId, 
            $startDate, 
            $endDate
        );

        $incomeByCategory = [];
        $expenseByCategory = [];
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($transactions as $transaction) {
            $category = $transaction->getCategory();
            $amount = $transaction->getAmount();

            if ($transaction->getType() === 'income') {
                $incomeByCategory[$category] = 
                    ($incomeByCategory[$category] ?? 0) + $amount;
                $totalIncome += $amount;
            } else {
                $expenseByCategory[$category] = 
                    ($expenseByCategory[$category] ?? 0) + $amount;
                $totalExpense += $amount;
            }
        }

        // Budget Comparison
        $budgets = $this->budgetMapper->findByUser($userId);
        $budgetComparison = [];

        foreach ($budgets as $budget) {
            $budgetComparison[] = [
                'name' => $budget->getName(),
                'category' => $budget->getCategory(),
                'budgetAmount' => $budget->getAmount(),
                'currentSpending' => $budget->getCurrentSpending(),
                'percentageSpent' => $budget->getPercentageSpent(),
                'isOverBudget' => $budget->isOverBudget()
            ];
        }

        // Account Balances
        $accounts = $this->accountMapper->findByUser($userId);
        $accountSummary = [];

        foreach ($accounts as $account) {
            $accountSummary[] = [
                'name' => $account->getName(),
                'type' => $account->getType(),
                'balance' => $account->getBalance()
            ];
        }

        return [
            'overview' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'netCashFlow' => $totalIncome - $totalExpense
            ],
            'incomeByCategory' => $incomeByCategory,
            'expenseByCategory' => $expenseByCategory,
            'budgetComparison' => $budgetComparison,
            'accountSummary' => $accountSummary,
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ];
    }

    /**
     * Generate a trend analysis report
     * 
     * @param string $userId
     * @param int $monthsBack Number of months to analyze
     * @return array
     */
    public function generateTrendAnalysis(
        string $userId, 
        int $monthsBack = 6
    ): array {
        $endDate = new DateTime();
        $startDate = (clone $endDate)->modify("-{$monthsBack} months");

        $monthlyTrends = [];

        // Iterate through each month
        $currentMonth = clone $startDate;
        while ($currentMonth <= $endDate) {
            $monthStart = clone $currentMonth;
            $monthEnd = (clone $currentMonth)->modify('last day of this month');

            $monthTransactions = $this->transactionMapper->findByUserAndDateRange(
                $userId, 
                $monthStart, 
                $monthEnd
            );

            $monthIncome = 0;
            $monthExpense = 0;
            $categoryBreakdown = [];

            foreach ($monthTransactions as $transaction) {
                $category = $transaction->getCategory();
                $amount = $transaction->getAmount();

                if ($transaction->getType() === 'income') {
                    $monthIncome += $amount;
                } else {
                    $monthExpense += $amount;
                    $categoryBreakdown[$category] = 
                        ($categoryBreakdown[$category] ?? 0) + $amount;
                }
            }

            $monthlyTrends[] = [
                'month' => $monthStart->format('Y-m'),
                'income' => $monthIncome,
                'expense' => $monthExpense,
                'netCashFlow' => $monthIncome - $monthExpense,
                'categoryBreakdown' => $categoryBreakdown
            ];

            $currentMonth->modify('next month');
        }

        return [
            'monthlyTrends' => $monthlyTrends,
            'analysisRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ];
    }

    /**
     * Generate a predictive financial health report
     * 
     * @param string $userId
     * @return array
     */
    public function generateFinancialHealthReport(string $userId): array {
        $overview = $this->generateFinancialOverview($userId);
        $trendAnalysis = $this->generateTrendAnalysis($userId);

        // Calculate financial health indicators
        $averageMonthlyIncome = array_sum(
            array_column($trendAnalysis['monthlyTrends'], 'income')
        ) / count($trendAnalysis['monthlyTrends']);

        $averageMonthlyExpense = array_sum(
            array_column($trendAnalysis['monthlyTrends'], 'expense')
        ) / count($trendAnalysis['monthlyTrends']);

        $savingsRate = $averageMonthlyIncome > 0 
            ? (($averageMonthlyIncome - $averageMonthlyExpense) / $averageMonthlyIncome) * 100 
            : 0;

        return [
            'financialHealth' => [
                'averageMonthlyIncome' => $averageMonthlyIncome,
                'averageMonthlyExpense' => $averageMonthlyExpense,
                'savingsRate' => round($savingsRate, 2),
                'totalBudgets' => count($overview['budgetComparison']),
                'overBudgetCategories' => array_filter(
                    $overview['budgetComparison'], 
                    fn($budget) => $budget['isOverBudget']
                )
            ],
            'overview' => $overview,
            'trendAnalysis' => $trendAnalysis
        ];
    }

    /**
     * Generate an advanced investment performance report
     * 
     * @param string $userId
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function generateInvestmentPerformanceReport(
        string $userId, 
        ?DateTime $startDate = null, 
        ?DateTime $endDate = null
    ): array {
        $startDate = $startDate ?? (new DateTime())->modify('-1 year');
        $endDate = $endDate ?? new DateTime();

        // Fetch investment transactions
        $investmentTransactions = $this->transactionMapper->findInvestmentTransactions(
            $userId, 
            $startDate, 
            $endDate
        );

        $performanceByAsset = [];
        $totalInvestmentValue = 0;
        $totalGainLoss = 0;

        foreach ($investmentTransactions as $transaction) {
            $asset = $transaction->getAsset();
            
            if (!isset($performanceByAsset[$asset])) {
                $performanceByAsset[$asset] = [
                    'purchases' => 0,
                    'sales' => 0,
                    'currentValue' => 0,
                    'totalGainLoss' => 0,
                    'performancePercentage' => 0
                ];
            }

            if ($transaction->getType() === 'investment_purchase') {
                $performanceByAsset[$asset]['purchases'] += $transaction->getAmount();
            } elseif ($transaction->getType() === 'investment_sale') {
                $performanceByAsset[$asset]['sales'] += $transaction->getAmount();
                
                // Calculate gain/loss
                $gainLoss = $transaction->getAmount() - 
                    $performanceByAsset[$asset]['purchases'];
                
                $performanceByAsset[$asset]['totalGainLoss'] += $gainLoss;
                $performanceByAsset[$asset]['performancePercentage'] = 
                    $performanceByAsset[$asset]['purchases'] > 0
                    ? ($gainLoss / $performanceByAsset[$asset]['purchases']) * 100
                    : 0;
            }
        }

        return [
            'investmentPerformance' => $performanceByAsset,
            'dateRange' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ];
    }

    /**
     * Generate a tax projection report
     * 
     * @param string $userId
     * @param int $taxYear
     * @return array
     */
    public function generateTaxProjectionReport(
        string $userId, 
        int $taxYear = null
    ): array {
        $taxYear = $taxYear ?? (int)date('Y');
        
        $startDate = new DateTime("{$taxYear}-01-01");
        $endDate = new DateTime("{$taxYear}-12-31");

        // Fetch all taxable transactions
        $taxableTransactions = $this->transactionMapper->findTaxableTransactions(
            $userId, 
            $startDate, 
            $endDate
        );

        $taxableCategoryBreakdown = [];
        $totalTaxableIncome = 0;
        $totalDeductions = 0;

        foreach ($taxableTransactions as $transaction) {
            $category = $transaction->getCategory();
            $amount = $transaction->getAmount();

            if ($transaction->isTaxable()) {
                $taxableCategoryBreakdown[$category] = 
                    ($taxableCategoryBreakdown[$category] ?? 0) + $amount;
                
                if ($transaction->getType() === 'income') {
                    $totalTaxableIncome += $amount;
                } else {
                    $totalDeductions += $amount;
                }
            }
        }

        // Simple tax calculation (this would need to be customized)
        $estimatedTaxRate = $this->calculateTaxRate($totalTaxableIncome);
        $estimatedTaxLiability = $totalTaxableIncome * $estimatedTaxRate;

        return [
            'taxYear' => $taxYear,
            'totalTaxableIncome' => $totalTaxableIncome,
            'totalDeductions' => $totalDeductions,
            'taxableCategoryBreakdown' => $taxableCategoryBreakdown,
            'estimatedTaxRate' => $estimatedTaxRate,
            'estimatedTaxLiability' => $estimatedTaxLiability
        ];
    }

    /**
     * Export report to specified format
     * 
     * @param string $reportType
     * @param array $reportData
     * @param string $format
     * @return string Exported report content
     */
    public function exportReport(
        string $reportType, 
        array $reportData, 
        string $format = 'csv'
    ): string {
        // Validate inputs
        if (!in_array($reportType, self::REPORT_TYPES)) {
            throw new InvalidArgumentException("Invalid report type");
        }

        if (!in_array($format, self::EXPORT_FORMATS)) {
            throw new InvalidArgumentException("Invalid export format");
        }

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($reportType, $reportData);
            case 'json':
                return json_encode($reportData, JSON_PRETTY_PRINT);
            case 'pdf':
                return $this->exportToPDF($reportType, $reportData);
            default:
                throw new InvalidArgumentException("Export format not implemented");
        }
    }

    /**
     * Export report to CSV
     * 
     * @param string $reportType
     * @param array $reportData
     * @return string CSV content
     */
    private function exportToCSV(string $reportType, array $reportData): string {
        $csv = [];
        
        // Generate CSV headers and rows based on report type
        switch ($reportType) {
            case 'financial_overview':
                $csv[] = ['Category', 'Amount', 'Percentage'];
                foreach ($reportData['expenseByCategory'] as $category => $amount) {
                    $percentage = ($amount / $reportData['overview']['totalExpense']) * 100;
                    $csv[] = [$category, $amount, round($percentage, 2)];
                }
                break;

            case 'investment_performance':
                $csv[] = ['Asset', 'Purchases', 'Sales', 'Gain/Loss', 'Performance %'];
                foreach ($reportData['investmentPerformance'] as $asset => $performance) {
                    $csv[] = [
                        $asset, 
                        $performance['purchases'], 
                        $performance['sales'], 
                        $performance['totalGainLoss'], 
                        round($performance['performancePercentage'], 2)
                    ];
                }
                break;

            // Add more report type exports as needed
        }

        // Convert to CSV string
        $output = '';
        foreach ($csv as $row) {
            $output .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return $output;
    }

    /**
     * Export report to PDF (stub - would require a PDF generation library)
     * 
     * @param string $reportType
     * @param array $reportData
     * @return string PDF content (placeholder)
     */
    private function exportToPDF(string $reportType, array $reportData): string {
        // This is a stub. In a real implementation, you'd use a PDF generation library
        return "PDF export not implemented for {$reportType}";
    }

    /**
     * Calculate a simple tax rate (this is a very basic implementation)
     * 
     * @param float $income
     * @return float
     */
    private function calculateTaxRate(float $income): float {
        // Very simplified tax bracket calculation
        if ($income <= 10000) return 0.10;
        if ($income <= 40000) return 0.12;
        if ($income <= 85000) return 0.22;
        if ($income <= 160000) return 0.24;
        return 0.32;
    }
}
