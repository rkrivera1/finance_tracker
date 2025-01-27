<?php
return [
    'apps' => [
        'finance_tracker' => [
            'name' => 'Finance Tracker',
            'icon' => 'money',
            'order' => 10,
            'route' => 'finance_tracker.page.index',
            'sections' => [
                [
                    'id' => 'dashboard',
                    'name' => 'Dashboard',
                    'icon' => 'fas fa-home',
                    'href' => '#dashboard',
                    'active' => true
                ],
                [
                    'id' => 'accounts',
                    'name' => 'Accounts',
                    'icon' => 'fas fa-wallet',
                    'href' => '#accounts',
                    'active' => false
                ],
                [
                    'id' => 'transactions',
                    'name' => 'Transactions',
                    'icon' => 'fas fa-exchange-alt',
                    'href' => '#transactions',
                    'active' => false
                ],
                [
                    'id' => 'investments',
                    'name' => 'Investments',
                    'icon' => 'fas fa-chart-line',
                    'href' => '#investments',
                    'active' => false
                ],
                [
                    'id' => 'budget',
                    'name' => 'Budget',
                    'icon' => 'fas fa-piggy-bank',
                    'href' => '#budget',
                    'active' => false
                ],
                [
                    'id' => 'reports',
                    'name' => 'Reports',
                    'icon' => 'fas fa-chart-bar',
                    'href' => '#reports',
                    'active' => false
                ]
            ]
        ]
    ]
];
