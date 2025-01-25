<?php
return [
    'routes' => [
        // Page Routes
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

        // Account Routes
        ['name' => 'account#index', 'url' => '/accounts', 'verb' => 'GET'],
        ['name' => 'account#create', 'url' => '/accounts', 'verb' => 'POST'],

        // Budget Routes
        ['name' => 'budget#index', 'url' => '/budgets', 'verb' => 'GET'],
        ['name' => 'budget#create', 'url' => '/budgets', 'verb' => 'POST'],

        // Transaction Routes
        ['name' => 'transaction#index', 'url' => '/transactions', 'verb' => 'GET'],
        ['name' => 'transaction#create', 'url' => '/transactions', 'verb' => 'POST'],

        // Investment Routes
        ['name' => 'investment#index', 'url' => '/investments', 'verb' => 'GET'],
        ['name' => 'investment#create', 'url' => '/investments', 'verb' => 'POST']
    ]
];
