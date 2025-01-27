const sampleData = {
    accounts: [
        {
            id: 1,
            name: 'Main Checking',
            type: 'checking',
            balance: 5420.50
        },
        {
            id: 2,
            name: 'Savings Account',
            type: 'savings',
            balance: 12750.75
        },
        {
            id: 3,
            name: 'Credit Card',
            type: 'credit',
            balance: -1250.30
        }
    ],

    transactions: [
        {
            id: 1,
            date: '2024-01-28',
            description: 'Grocery Shopping',
            category: 'groceries',
            amount: -156.78,
            type: 'expense',
            accountId: 1
        },
        {
            id: 2,
            date: '2024-01-27',
            description: 'Salary Deposit',
            category: 'income',
            amount: 3500.00,
            type: 'income',
            accountId: 1
        },
        {
            id: 3,
            date: '2024-01-26',
            description: 'Netflix Subscription',
            category: 'entertainment',
            amount: -15.99,
            type: 'expense',
            accountId: 3
        }
    ],

    investments: [
        {
            id: 1,
            symbol: 'AAPL',
            name: 'Apple Inc.',
            shares: 10,
            purchasePrice: 150.25,
            currentPrice: 175.50,
            performance: {
                day: 1.5,
                month: 5.2,
                ytd: 8.7
            }
        },
        {
            id: 2,
            symbol: 'MSFT',
            name: 'Microsoft Corporation',
            shares: 15,
            purchasePrice: 220.75,
            currentPrice: 245.30,
            performance: {
                day: -0.8,
                month: 3.5,
                ytd: 6.2
            }
        }
    ],

    budgets: {
        totalBudget: 5000.00,
        totalSpent: 2750.50,
        totalRemaining: 2249.50,
        categories: [
            {
                id: 1,
                name: 'Housing',
                budget: 1500.00,
                spent: 1450.00,
                remaining: 50.00
            },
            {
                id: 2,
                name: 'Groceries',
                budget: 600.00,
                spent: 425.75,
                remaining: 174.25
            },
            {
                id: 3,
                name: 'Transportation',
                budget: 400.00,
                spent: 285.50,
                remaining: 114.50
            }
        ],
        goals: [
            {
                id: 1,
                type: 'saving',
                name: 'Emergency Fund',
                targetAmount: 10000.00,
                currentAmount: 6500.00,
                targetDate: '2024-06-30'
            },
            {
                id: 2,
                type: 'spending',
                name: 'Entertainment Budget',
                targetAmount: 300.00,
                currentAmount: 175.50,
                targetDate: '2024-02-29'
            }
        ],
        alerts: [
            {
                id: 1,
                severity: 'warning',
                message: 'Housing budget at 97% of limit',
                date: '2024-01-28'
            },
            {
                id: 2,
                severity: 'info',
                message: 'Entertainment spending within budget',
                date: '2024-01-27'
            }
        ]
    }
}; 