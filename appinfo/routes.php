<?php
return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'page#index', 'url' => '/index', 'verb' => 'GET'],
        
        // Account routes
        ['name' => 'account#index', 'url' => '/accounts', 'verb' => 'GET'],
        ['name' => 'account#create', 'url' => '/accounts', 'verb' => 'POST'],
        
        // Budget routes
        ['name' => 'budget#index', 'url' => '/budgets', 'verb' => 'GET'],
        ['name' => 'budget#create', 'url' => '/budgets', 'verb' => 'POST']
    ]
];
