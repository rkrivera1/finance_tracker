<?php
namespace OCA\FinanceTracker\Migration;

use OCP\DB\IConnection;
use OCP\IUser;
use OCP\IUserManager;
use DateTime;

class SampleDataSeeder {
    private $db;
    private $userManager;

    public function __construct(IConnection $db, IUserManager $userManager) {
        $this->db = $db;
        $this->userManager = $userManager;
    }

    public function seedSampleData() {
        // Find a sample user
        $sampleUser = null;
        $this->userManager->callForAllUsers(function(IUser $user) use (&$sampleUser) {
            if ($sampleUser === null) {
                $sampleUser = $user;
            }
        });

        if (!$sampleUser) {
            throw new \Exception("No users found to seed sample data");
        }

        $userId = $sampleUser->getUID();

        // Seed Accounts
        $accounts = [
            ['name' => 'Checking Account', 'type' => 'checking', 'balance' => 5000.00],
            ['name' => 'Savings Account', 'type' => 'savings', 'balance' => 15000.00],
            ['name' => 'Credit Card', 'type' => 'credit', 'balance' => -500.00]
        ];
        $accountIds = $this->seedAccounts($userId, $accounts);

        // Seed Transactions
        $transactions = [
            [
                'account_id' => $accountIds[0],
                'date' => new DateTime('2024-01-15'),
                'amount' => -250.00,
                'category' => 'Groceries',
                'description' => 'Monthly grocery shopping'
            ],
            [
                'account_id' => $accountIds[0],
                'date' => new DateTime('2024-01-20'),
                'amount' => -150.00,
                'category' => 'Dining Out',
                'description' => 'Dinner with friends'
            ],
            [
                'account_id' => $accountIds[1],
                'date' => new DateTime('2024-01-10'),
                'amount' => 500.00,
                'category' => 'Salary',
                'description' => 'Monthly salary deposit'
            ]
        ];
        $this->seedTransactions($userId, $transactions);

        // Seed Investments
        $investments = [
            [
                'symbol' => 'AAPL',
                'name' => 'Apple Inc.',
                'shares' => 10.5,
                'purchase_price' => 180.50,
                'purchase_date' => new DateTime('2024-01-05')
            ],
            [
                'symbol' => 'GOOGL',
                'name' => 'Alphabet Inc.',
                'shares' => 5.25,
                'purchase_price' => 120.75,
                'purchase_date' => new DateTime('2024-01-10')
            ]
        ];
        $this->seedInvestments($userId, $investments);

        // Seed Budgets
        $budgets = [
            [
                'category' => 'Groceries',
                'amount' => 500.00,
                'period' => 'monthly'
            ],
            [
                'category' => 'Dining Out',
                'amount' => 200.00,
                'period' => 'monthly'
            ],
            [
                'category' => 'Entertainment',
                'amount' => 150.00,
                'period' => 'monthly'
            ]
        ];
        $this->seedBudgets($userId, $budgets);
    }

    private function seedAccounts(string $userId, array $accounts): array {
        $accountIds = [];
        $insertQuery = $this->db->getQueryBuilder();
        $insertQuery->insert('finance_accounts')
            ->setValue('user_id', $insertQuery->createNamedParameter($userId))
            ->setValue('name', $insertQuery->createNamedParameter(''))
            ->setValue('type', $insertQuery->createNamedParameter(''))
            ->setValue('balance', $insertQuery->createNamedParameter(0, \PDO::PARAM_STR));

        foreach ($accounts as $account) {
            $insertQuery->setParameter(1, $account['name'])
                        ->setParameter(2, $account['type'])
                        ->setParameter(3, $account['balance']);
            
            $insertQuery->executeStatement();
            $accountIds[] = $this->db->lastInsertId('finance_accounts');
        }

        return $accountIds;
    }

    private function seedTransactions(string $userId, array $transactions): void {
        $insertQuery = $this->db->getQueryBuilder();
        $insertQuery->insert('finance_transactions')
            ->setValue('user_id', $insertQuery->createNamedParameter($userId))
            ->setValue('account_id', $insertQuery->createNamedParameter(0, \PDO::PARAM_INT))
            ->setValue('date', $insertQuery->createNamedParameter(''))
            ->setValue('amount', $insertQuery->createNamedParameter(0, \PDO::PARAM_STR))
            ->setValue('category', $insertQuery->createNamedParameter(''))
            ->setValue('description', $insertQuery->createNamedParameter(''));

        foreach ($transactions as $transaction) {
            $insertQuery->setParameter(1, $transaction['account_id'])
                        ->setParameter(2, $transaction['date']->format('Y-m-d H:i:s'))
                        ->setParameter(3, $transaction['amount'])
                        ->setParameter(4, $transaction['category'])
                        ->setParameter(5, $transaction['description']);
            
            $insertQuery->executeStatement();
        }
    }

    private function seedInvestments(string $userId, array $investments): void {
        $insertQuery = $this->db->getQueryBuilder();
        $insertQuery->insert('finance_investments')
            ->setValue('user_id', $insertQuery->createNamedParameter($userId))
            ->setValue('symbol', $insertQuery->createNamedParameter(''))
            ->setValue('name', $insertQuery->createNamedParameter(''))
            ->setValue('shares', $insertQuery->createNamedParameter(0, \PDO::PARAM_STR))
            ->setValue('purchase_price', $insertQuery->createNamedParameter(0, \PDO::PARAM_STR))
            ->setValue('purchase_date', $insertQuery->createNamedParameter(''));

        foreach ($investments as $investment) {
            $insertQuery->setParameter(1, $investment['symbol'])
                        ->setParameter(2, $investment['name'])
                        ->setParameter(3, $investment['shares'])
                        ->setParameter(4, $investment['purchase_price'])
                        ->setParameter(5, $investment['purchase_date']->format('Y-m-d H:i:s'));
            
            $insertQuery->executeStatement();
        }
    }

    private function seedBudgets(string $userId, array $budgets): void {
        $insertQuery = $this->db->getQueryBuilder();
        $insertQuery->insert('finance_budgets')
            ->setValue('user_id', $insertQuery->createNamedParameter($userId))
            ->setValue('category', $insertQuery->createNamedParameter(''))
            ->setValue('amount', $insertQuery->createNamedParameter(0, \PDO::PARAM_STR))
            ->setValue('period', $insertQuery->createNamedParameter(''));

        foreach ($budgets as $budget) {
            $insertQuery->setParameter(1, $budget['category'])
                        ->setParameter(2, $budget['amount'])
                        ->setParameter(3, $budget['period']);
            
            $insertQuery->executeStatement();
        }
    }
}
