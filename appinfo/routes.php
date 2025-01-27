<?php
return [
    'routes' => [
        // Page Routes
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

        // Account Routes
        ['name' => 'account#index', 'url' => '/accounts', 'verb' => 'GET'],
        ['name' => 'account#show', 'url' => '/accounts/{id}', 'verb' => 'GET'],
        ['name' => 'account#create', 'url' => '/accounts', 'verb' => 'POST'],
        ['name' => 'account#update', 'url' => '/accounts/{id}', 'verb' => 'PUT'],
        ['name' => 'account#delete', 'url' => '/accounts/{id}', 'verb' => 'DELETE'],

        // Transaction Routes
        ['name' => 'transaction#index', 'url' => '/transactions', 'verb' => 'GET'],
        ['name' => 'transaction#show', 'url' => '/transactions/{id}', 'verb' => 'GET'],
        ['name' => 'transaction#create', 'url' => '/transactions', 'verb' => 'POST'],
        ['name' => 'transaction#update', 'url' => '/transactions/{id}', 'verb' => 'PUT'],
        ['name' => 'transaction#delete', 'url' => '/transactions/{id}', 'verb' => 'DELETE'],

        // Budget Routes
        ['name' => 'budget#index', 'url' => '/budgets', 'verb' => 'GET'],
        ['name' => 'budget#show', 'url' => '/budgets/{id}', 'verb' => 'GET'],
        ['name' => 'budget#create', 'url' => '/budgets', 'verb' => 'POST'],
        ['name' => 'budget#update', 'url' => '/budgets/{id}', 'verb' => 'PUT'],
        ['name' => 'budget#delete', 'url' => '/budgets/{id}', 'verb' => 'DELETE'],
        ['name' => 'budget#performance', 'url' => '/budgets/performance', 'verb' => 'GET'],

        // Investment Routes
        ['name' => 'investment#index', 'url' => '/investments', 'verb' => 'GET'],
        ['name' => 'investment#show', 'url' => '/investments/{id}', 'verb' => 'GET'],
        ['name' => 'investment#create', 'url' => '/investments', 'verb' => 'POST'],
        ['name' => 'investment#update', 'url' => '/investments/{id}', 'verb' => 'PUT'],
        ['name' => 'investment#delete', 'url' => '/investments/{id}', 'verb' => 'DELETE'],
        ['name' => 'investment#performance', 'url' => '/investments/performance', 'verb' => 'GET'],

        // Settings Routes
        ['name' => 'settings#index', 'url' => '/settings', 'verb' => 'GET'],
        ['name' => 'settings#show', 'url' => '/settings/{id}', 'verb' => 'GET'],
        ['name' => 'settings#create', 'url' => '/settings', 'verb' => 'POST'],
        ['name' => 'settings#update', 'url' => '/settings/{id}', 'verb' => 'PUT'],
        ['name' => 'settings#delete', 'url' => '/settings/{id}', 'verb' => 'DELETE'],
        ['name' => 'settings#saveAdmin', 'url' => '/settings/admin', 'verb' => 'POST'],
        ['name' => 'settings#savePersonal', 'url' => '/settings/personal', 'verb' => 'POST']
    ]
];
