<?php
namespace OCA\FinanceTracker\Service;

use OCP\Notification\IManager;
use OCP\IConfig;
use OCP\IUser;
use OCP\IUserManager;
use OCA\FinanceTracker\Db\BudgetMapper;
use OCA\FinanceTracker\Db\TransactionMapper;

class NotificationService {
    private $notificationManager;
    private $config;
    private $userManager;
    private $budgetMapper;
    private $transactionMapper;

    public function __construct(
        IManager $notificationManager,
        IConfig $config,
        IUserManager $userManager,
        BudgetMapper $budgetMapper,
        TransactionMapper $transactionMapper
    ) {
        $this->notificationManager = $notificationManager;
        $this->config = $config;
        $this->userManager = $userManager;
        $this->budgetMapper = $budgetMapper;
        $this->transactionMapper = $transactionMapper;
    }

    /**
     * Send budget threshold notifications to users
     */
    public function sendBudgetAlerts() {
        $this->userManager->callForAllUsers(function(IUser $user) {
            $userId = $user->getUID();
            
            // Get user's budget notification threshold
            $threshold = $this->config->getUserValue(
                $userId, 
                'finance_tracker', 
                'budget_notification_threshold', 
                '80'
            );
            $threshold = intval($threshold);

            // Get active budgets for the user
            $budgets = $this->budgetMapper->findActiveBudgets($userId, new \DateTime());

            foreach ($budgets as $budget) {
                $category = $budget->getCategory();
                $budgetAmount = $budget->getBudgetAmount();

                // Get transactions for this category
                $transactions = $this->transactionMapper->findByCategory($userId, $category);
                
                // Calculate total spent
                $totalSpent = array_reduce($transactions, function($carry, $transaction) use ($budget) {
                    $transactionDate = $transaction->getTransactionDate();
                    if ($transactionDate >= $budget->getStartDate() && 
                        $transactionDate <= $budget->getEndDate()) {
                        return $carry + $transaction->getAmount();
                    }
                    return $carry;
                }, 0);

                // Calculate percentage spent
                $percentageSpent = ($totalSpent / $budgetAmount) * 100;

                // Check if threshold is exceeded
                if ($percentageSpent >= $threshold) {
                    $this->createBudgetNotification(
                        $userId, 
                        $category, 
                        $budgetAmount, 
                        $totalSpent, 
                        $percentageSpent
                    );
                }
            }
        });
    }

    /**
     * Create a budget threshold notification
     * @param string $userId
     * @param string $category
     * @param float $budgetAmount
     * @param float $totalSpent
     * @param float $percentageSpent
     */
    private function createBudgetNotification(
        string $userId, 
        string $category, 
        float $budgetAmount, 
        float $totalSpent, 
        float $percentageSpent
    ) {
        $notification = $this->notificationManager->createNotification();

        $notification->setApp('finance_tracker')
            ->setUser($userId)
            ->setDateTime(new \DateTime())
            ->setObject('budget', $category)
            ->setSubject('budget_threshold_exceeded', [
                'category' => $category,
                'budget_amount' => $budgetAmount,
                'total_spent' => $totalSpent,
                'percentage_spent' => round($percentageSpent, 2)
            ])
            ->setPriority(INotification::PRIORITY_HIGH);

        $this->notificationManager->send($notification);
    }

    /**
     * Send monthly financial summary
     */
    public function sendMonthlySummary() {
        $this->userManager->callForAllUsers(function(IUser $user) {
            $userId = $user->getUID();
            
            // Check if user has enabled financial goal tracking
            $financialGoalTrackingEnabled = $this->config->getUserValue(
                $userId, 
                'finance_tracker', 
                'financial_goal_tracking', 
                'false'
            );

            if ($financialGoalTrackingEnabled !== 'true') {
                return;
            }

            // Get transactions for the past month
            $endDate = new \DateTime();
            $startDate = clone $endDate;
            $startDate->modify('-1 month');

            $transactions = $this->transactionMapper->findAll($userId);
            
            // Filter transactions for the past month
            $monthlyTransactions = array_filter($transactions, function($transaction) use ($startDate, $endDate) {
                $transactionDate = $transaction->getTransactionDate();
                return $transactionDate >= $startDate && $transactionDate <= $endDate;
            });

            // Calculate total spent by category
            $categorySummary = [];
            foreach ($monthlyTransactions as $transaction) {
                $category = $transaction->getCategory();
                if (!isset($categorySummary[$category])) {
                    $categorySummary[$category] = 0;
                }
                $categorySummary[$category] += $transaction->getAmount();
            }

            // Create monthly summary notification
            $this->createMonthlySummaryNotification(
                $userId, 
                $categorySummary, 
                array_sum($categorySummary)
            );
        });
    }

    /**
     * Create a monthly summary notification
     * @param string $userId
     * @param array $categorySummary
     * @param float $totalSpent
     */
    private function createMonthlySummaryNotification(
        string $userId, 
        array $categorySummary, 
        float $totalSpent
    ) {
        $notification = $this->notificationManager->createNotification();

        $notification->setApp('finance_tracker')
            ->setUser($userId)
            ->setDateTime(new \DateTime())
            ->setObject('monthly_summary', 'finance')
            ->setSubject('monthly_financial_summary', [
                'total_spent' => round($totalSpent, 2),
                'category_summary' => $categorySummary
            ])
            ->setPriority(INotification::PRIORITY_NORMAL);

        $this->notificationManager->send($notification);
    }
}
