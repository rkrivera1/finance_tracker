<?php
/**
 * Create routes for the Finance Tracker Nextcloud app
 */
return [
    'routes' => [
        // Main app route
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        
        // Transaction routes
        ['name' => 'transaction#index', 'url' => '/transactions', 'verb' => 'GET'],
        ['name' => 'transaction#listByCategory', 'url' => '/transactions/category/{category}', 'verb' => 'GET'],
        ['name' => 'transaction#create', 'url' => '/transactions', 'verb' => 'POST'],
        ['name' => 'transaction#destroy', 'url' => '/transactions/{id}', 'verb' => 'DELETE'],
        
        // Budget routes
        ['name' => 'budget#index', 'url' => '/budgets', 'verb' => 'GET'],
        ['name' => 'budget#status', 'url' => '/budgets/status/{category}', 'verb' => 'GET'],
        ['name' => 'budget#create', 'url' => '/budgets', 'verb' => 'POST'],
        ['name' => 'budget#destroy', 'url' => '/budgets/{id}', 'verb' => 'DELETE'],
        
        // Updated Report routes
        ['name' => 'report#monthlyReport', 'url' => '/reports/monthly', 'verb' => 'GET'],
        ['name' => 'report#annualTrends', 'url' => '/reports/annual/{year}', 'verb' => 'GET'],
        ['name' => 'report#spendingPrediction', 'url' => '/reports/prediction', 'verb' => 'GET'],
        
        // Settings routes
        ['name' => 'settings#savePersonalSettings', 'url' => '/settings/personal', 'verb' => 'POST'],
        ['name' => 'settings#saveAdminSettings', 'url' => '/settings/admin', 'verb' => 'POST'],
        
        // Financial Goal routes
        ['name' => 'financial_goal#index', 'url' => '/goals', 'verb' => 'GET'],
        ['name' => 'financial_goal#activeGoals', 'url' => '/goals/active', 'verb' => 'GET'],
        ['name' => 'financial_goal#create', 'url' => '/goals', 'verb' => 'POST'],
        ['name' => 'financial_goal#updateProgress', 'url' => '/goals/{goalId}/progress', 'verb' => 'PUT'],
        ['name' => 'financial_goal#destroy', 'url' => '/goals/{goalId}', 'verb' => 'DELETE'],
        ['name' => 'financial_goal#analyzeGoals', 'url' => '/goals/analysis', 'verb' => 'GET'],
        
        // Investment routes
        ['name' => 'investment#index', 'url' => '/investments', 'verb' => 'GET'],
        ['name' => 'investment#listByType', 'url' => '/investments/type/{type}', 'verb' => 'GET'],
        ['name' => 'investment#create', 'url' => '/investments', 'verb' => 'POST'],
        ['name' => 'investment#updatePrice', 'url' => '/investments/{id}/price', 'verb' => 'PUT'],
        ['name' => 'investment#destroy', 'url' => '/investments/{id}', 'verb' => 'DELETE'],
        ['name' => 'investment#portfolioRiskAnalysis', 'url' => '/investments/risk-analysis', 'verb' => 'GET'],
        ['name' => 'investment#portfolioRebalancing', 'url' => '/investments/rebalancing', 'verb' => 'GET'],
        
        // Debt routes
        ['name' => 'debt#index', 'url' => '/debts', 'verb' => 'GET'],
        ['name' => 'debt#create', 'url' => '/debts', 'verb' => 'POST'],
        ['name' => 'debt#makePayment', 'url' => '/debts/{debtId}/payment', 'verb' => 'PUT'],
        ['name' => 'debt#destroy', 'url' => '/debts/{debtId}', 'verb' => 'DELETE'],
        ['name' => 'debt#analyzePortfolio', 'url' => '/debts/analysis', 'verb' => 'GET'],
        ['name' => 'debt#repaymentStrategy', 'url' => '/debts/strategy', 'verb' => 'GET'],
        
        // Currency routes
        ['name' => 'currency#convert', 'url' => '/currency/convert', 'verb' => 'GET'],
        ['name' => 'currency#getSupportedCurrencies', 'url' => '/currency/supported', 'verb' => 'GET'],
        ['name' => 'currency#getExchangeRate', 'url' => '/currency/rate', 'verb' => 'GET']
    ]
];
