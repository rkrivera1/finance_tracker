<?php
script('finance_tracker', 'script');
style('finance_tracker', 'style');
?>
<div id="app" class="nc-app finance-tracker-app">
    <div id="app-navigation" class="app-navigation">
        <ul class="with-icon">
            <li class="nav-item">
                <a href="#dashboard" class="nav-icon-text" data-section="dashboard">
                    <span class="icon-home"></span>
                    <span class="nav-text"><?php p($l->t('Dashboard')); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#transactions" class="nav-icon-text" data-section="transactions">
                    <span class="icon-category"></span>
                    <span class="nav-text"><?php p($l->t('Transactions')); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#investments" class="nav-icon-text" data-section="investments">
                    <span class="icon-stock"></span>
                    <span class="nav-text"><?php p($l->t('Investments')); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#budget" class="nav-icon-text" data-section="budget">
                    <span class="icon-budget"></span>
                    <span class="nav-text"><?php p($l->t('Budget')); ?></span>
                </a>
            </li>
        </ul>
    </div>

    <div id="app-content" class="app-content">
        <div class="finance-sections">
            <!-- Dashboard Section -->
            <section id="dashboard-section" class="finance-section active">
                <div class="finance-section-content">
                    <h2><?php p($l->t('Financial Dashboard')); ?></h2>
                    <div class="dashboard-overview">
                        <div class="dashboard-card card">
                            <h3 class="card-title"><?php p($l->t('Account Summary')); ?></h3>
                            <div id="dashboard-accounts-summary" class="card-body">
                                <div class="loading-indicator">
                                    <span class="icon-loading"></span>
                                    <?php p($l->t('Loading account summary...')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card card">
                            <h3 class="card-title"><?php p($l->t('Recent Transactions')); ?></h3>
                            <div id="dashboard-recent-transactions" class="card-body">
                                <div class="loading-indicator">
                                    <span class="icon-loading"></span>
                                    <?php p($l->t('Loading recent transactions...')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Transactions Section -->
            <section id="transactions-section" class="finance-section" style="display:none;">
                <div class="finance-section-content">
                    <h2><?php p($l->t('Transactions')); ?></h2>
                    <div class="transactions-header">
                        <div class="transactions-search">
                            <input type="text" id="transactions-search-input" placeholder="<?php p($l->t('Search transactions')); ?>">
                            <button id="search-transactions-btn" class="btn primary">
                                <?php p($l->t('Search')); ?>
                            </button>
                        </div>
                    </div>
                    <div id="transactions-list">
                        <!-- Transactions will be dynamically populated -->
                    </div>
                </div>
            </section>

            <!-- Investments Section -->
            <section id="investments-section" class="finance-section" style="display:none;">
                <div class="finance-section-content">
                    <h2><?php p($l->t('Investments')); ?></h2>
                    <div class="investments-search">
                        <input type="text" id="stock-search-input" placeholder="<?php p($l->t('Search stocks')); ?>">
                        <button id="stock-search-btn" class="btn primary">
                            <?php p($l->t('Search Stocks')); ?>
                        </button>
                    </div>
                    <div id="investments-list">
                        <!-- Investment data will be dynamically populated -->
                    </div>
                </div>
            </section>

            <!-- Budget Section -->
            <section id="budget-section" class="finance-section" style="display:none;">
                <div class="finance-section-content">
                    <h2><?php p($l->t('Budget Management')); ?></h2>
                    <div class="budget-header">
                        <select id="budget-period">
                            <option value="monthly"><?php p($l->t('Monthly')); ?></option>
                            <option value="yearly"><?php p($l->t('Yearly')); ?></option>
                        </select>
                        <input type="month" id="budget-month" value="<?php echo date('Y-m'); ?>">
                        <button id="add-budget-btn" class="btn primary">
                            <?php p($l->t('Add Budget')); ?>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Utility Functions
    function showNotification(message, type = 'success') {
        // Create Nextcloud-style notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    function showLoading(element) {
        element.classList.add('icon-loading');
        element.disabled = true;
    }

    function hideLoading(element) {
        element.classList.remove('icon-loading');
        element.disabled = false;
    }

    // Ensure all elements are present before initializing
    function initializeApp() {
        const navItems = document.querySelectorAll('#app-navigation .nav-item a');
        const sections = document.querySelectorAll('.finance-section');

        if (navItems.length === 0 || sections.length === 0) {
            console.warn('Navigation or sections not fully loaded. Retrying...');
            setTimeout(initializeApp, 100);
            return;
        }

        function activateSection(sectionId) {
            sections.forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            navItems.forEach(navItem => navItem.closest('.nav-item').classList.remove('active'));

            const activeSection = document.getElementById(`${sectionId}-section`);
            const activeNavItem = document.querySelector(`[data-section="${sectionId}"]`).closest('.nav-item');
            
            if (activeSection && activeNavItem) {
                activeSection.style.display = 'block';
                activeSection.classList.add('active');
                activeNavItem.classList.add('active');
            }
        }

        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-section');
                activateSection(sectionId);
                history.pushState(null, '', `#${sectionId}`);
            });
        });

        // Initial section activation
        const initialHash = window.location.hash.substring(1);
        activateSection(initialHash || 'dashboard');

        // Stock Search Functionality
        const stockSearchBtn = document.getElementById('stock-search-btn');
        const stockSearchInput = document.getElementById('stock-search-input');
        const stockSearchResults = document.getElementById('stock-search-results');

        stockSearchBtn.addEventListener('click', function() {
            const searchTerm = stockSearchInput.value.trim();
            if (!searchTerm) {
                showNotification('Please enter a stock symbol or company name', 'error');
                return;
            }

            showLoading(stockSearchBtn);
            stockSearchResults.classList.remove('hidden');

            // Simulated API call (replace with actual API integration)
            setTimeout(() => {
                hideLoading(stockSearchBtn);
                const resultsBody = document.getElementById('stock-search-results-body');
                
                // Check if results exist
                if (Math.random() > 0.5) {
                    resultsBody.innerHTML = `
                        <tr>
                            <td>AAPL</td>
                            <td>Apple Inc.</td>
                            <td>$175.23</td>
                            <td>+0.5%</td>
                            <td>Strong</td>
                            <td>25M</td>
                            <td>
                                <button class="btn btn-small stock-add-btn" data-symbol="AAPL">Add</button>
                            </td>
                        </tr>
                    `;
                    showNotification('Stock search completed successfully');
                } else {
                    resultsBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="no-results">No stocks found matching your search</td>
                        </tr>
                    `;
                    showNotification('No stocks found', 'warning');
                }

                // Add event listeners to "Add" buttons
                document.querySelectorAll('.stock-add-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const symbol = this.getAttribute('data-symbol');
                        showNotification(`Added ${symbol} to watchlist`);
                        this.disabled = true;
                        this.textContent = 'Added';
                    });
                });
            }, 1000);
        });

        // Transaction Search and Filtering
        const transactionSearchBtn = document.getElementById('search-transactions-btn');
        const transactionSearchInput = document.getElementById('transactions-search-input');
        const transactionsTable = document.getElementById('transactions-table-body');
        const transactionFilters = {
            account: document.getElementById('transaction-account-filter'),
            category: document.getElementById('transaction-category-filter'),
            startDate: document.getElementById('transaction-start-date'),
            endDate: document.getElementById('transaction-end-date')
        };

        transactionSearchBtn.addEventListener('click', function() {
            const searchTerm = transactionSearchInput.value.trim();
            const filters = {
                account: transactionFilters.account.value,
                category: transactionFilters.category.value,
                startDate: transactionFilters.startDate.value,
                endDate: transactionFilters.endDate.value
            };

            showLoading(transactionSearchBtn);

            // Simulated filtering logic
            setTimeout(() => {
                hideLoading(transactionSearchBtn);
                
                // Simulate filtering based on inputs
                const filteredTransactions = [
                    {
                        date: '2023-11-15',
                        description: 'Grocery Shopping',
                        category: 'Groceries',
                        amount: '$85.50',
                        type: 'Expense'
                    },
                    {
                        date: '2023-11-10',
                        description: 'Salary Deposit',
                        category: 'Income',
                        amount: '$3500.00',
                        type: 'Income'
                    }
                ];

                if (filteredTransactions.length > 0) {
                    transactionsTable.innerHTML = filteredTransactions.map(transaction => `
                        <tr>
                            <td>${transaction.date}</td>
                            <td>${transaction.description}</td>
                            <td>${transaction.category}</td>
                            <td>${transaction.amount}</td>
                            <td>${transaction.type}</td>
                            <td>
                                <button class="btn btn-small transaction-edit-btn">Edit</button>
                                <button class="btn btn-small btn-danger transaction-delete-btn">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                    showNotification(`Found ${filteredTransactions.length} transactions`);
                } else {
                    transactionsTable.innerHTML = `
                        <tr>
                            <td colspan="6" class="no-results">No transactions found matching your search</td>
                        </tr>
                    `;
                    showNotification('No transactions found', 'warning');
                }

                // Add event listeners for edit and delete buttons
                document.querySelectorAll('.transaction-edit-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const description = row.querySelector('td:nth-child(2)').textContent;
                        showNotification(`Preparing to edit transaction: ${description}`);
                        // TODO: Open edit modal with transaction details
                    });
                });

                document.querySelectorAll('.transaction-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const description = row.querySelector('td:nth-child(2)').textContent;
                        
                        // Confirm deletion
                        if (confirm(`Are you sure you want to delete the transaction: ${description}?`)) {
                            row.remove();
                            showNotification(`Deleted transaction: ${description}`);
                        }
                    });
                });
            }, 800);
        });

        // Add Transaction Button
        const addTransactionBtn = document.getElementById('add-transaction-btn');
        if (addTransactionBtn) {
            addTransactionBtn.addEventListener('click', function() {
                // Open transaction modal or form
                const transactionModal = document.getElementById('transaction-modal');
                if (transactionModal) {
                    transactionModal.classList.remove('hidden');
                    showNotification('Add Transaction form opened');
                }
            });
        }

        // Budget Management
        const addBudgetBtn = document.getElementById('add-budget-btn');
        if (addBudgetBtn) {
            addBudgetBtn.addEventListener('click', function() {
                const budgetPeriod = document.getElementById('budget-period').value;
                const budgetMonth = document.getElementById('budget-month').value;
                
                showNotification(`Preparing to add ${budgetPeriod} budget for ${budgetMonth}`);
                // TODO: Open budget creation modal or form
            });
        }

        // Investment Add Button
        const addInvestmentBtn = document.getElementById('add-investment-btn');
        if (addInvestmentBtn) {
            addInvestmentBtn.addEventListener('click', function() {
                showNotification('Open investment addition form');
                // TODO: Open investment addition modal or form
            });
        }

        // CSS for notifications
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 10px 15px;
                border-radius: 4px;
                color: white;
                z-index: 1000;
                transition: opacity 0.5s;
            }
            .notification.success {
                background-color: #46ba61;
            }
            .notification.error {
                background-color: #dc3545;
            }
            .notification.warning {
                background-color: #f0ad4e;
            }
            .notification.fade-out {
                opacity: 0;
            }
            .no-results {
                text-align: center;
                color: #888;
                padding: 20px;
            }
        `;
        document.head.appendChild(styleSheet);
    }

    // Start initialization
    initializeApp();
});
</script>