<?php
script('finance_tracker', 'script');
style('finance_tracker', 'style');
?>
<div id="content" class="app-financetracker">
    <div id="app-navigation-toggle" class="icon-menu"></div>
    
    <div id="app" class="finance-tracker-app">
        <div id="app-navigation" class="app-navigation">
            <ul class="app-navigation-list">
                <li class="app-navigation-item">
                    <a href="#" class="app-navigation-entry-link" data-section="dashboard">
                        <span class="app-navigation-entry-icon icon-home"></span>
                        <span class="app-navigation-entry-text"><?php p($l->t('Dashboard')); ?></span>
                    </a>
                </li>
                <?php print_unescaped($this->inc('navigation')); ?>
            </ul>
        </div>

        <div id="app-content" class="app-content">
            <div class="finance-sections">
                <!-- Dashboard Section -->
                <section id="dashboard-section" class="finance-section">
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
                            <div class="dashboard-card card">
                                <h3 class="card-title"><?php p($l->t('Budget Overview')); ?></h3>
                                <div id="dashboard-budget-overview" class="card-body">
                                    <div class="loading-indicator">
                                        <span class="icon-loading"></span>
                                        <?php p($l->t('Loading budget overview...')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Accounts Section -->
                <section id="accounts-section" class="finance-section" style="display: none;">
                    <div class="finance-section-content">
                        <h2><?php p($l->t('Accounts')); ?></h2>
                        <div class="accounts-list"></div>
                        <button id="add-account-btn" class="primary"><?php p($l->t('Add Account')); ?></button>
                    </div>
                </section>

                <!-- Transactions Section -->
                <section id="transactions-section" class="finance-section" style="display: none;">
                    <div class="finance-section-content">
                        <h2><?php p($l->t('Transactions')); ?></h2>
                        
                        <div class="transactions-header">
                            <div class="transactions-search">
                                <input 
                                    type="text" 
                                    id="transactions-search-input" 
                                    class="input-field"
                                    placeholder="<?php p($l->t('Search transactions...')); ?>"
                                >
                                <select id="transactions-search-filter" class="select-field">
                                    <option value="all"><?php p($l->t('All Fields')); ?></option>
                                    <option value="description"><?php p($l->t('Description')); ?></option>
                                    <option value="category"><?php p($l->t('Category')); ?></option>
                                    <option value="amount"><?php p($l->t('Amount')); ?></option>
                                    <option value="date"><?php p($l->t('Date')); ?></option>
                                </select>
                            </div>
                            
                            <div class="transactions-filters">
                                <select id="transaction-account-filter">
                                    <option value=""><?php p($l->t('All Accounts')); ?></option>
                                    <!-- Dynamically populated by JavaScript -->
                                </select>
                                <select id="transaction-category-filter">
                                    <option value=""><?php p($l->t('All Categories')); ?></option>
                                    <option value="groceries"><?php p($l->t('Groceries')); ?></option>
                                    <option value="dining"><?php p($l->t('Dining Out')); ?></option>
                                    <option value="entertainment"><?php p($l->t('Entertainment')); ?></option>
                                    <option value="utilities"><?php p($l->t('Utilities')); ?></option>
                                    <option value="transportation"><?php p($l->t('Transportation')); ?></option>
                                    <option value="other"><?php p($l->t('Other')); ?></option>
                                </select>
                                <input type="date" id="transaction-start-date" placeholder="<?php p($l->t('Start Date')); ?>">
                                <input type="date" id="transaction-end-date" placeholder="<?php p($l->t('End Date')); ?>">
                            </div>
                        </div>

                        <div class="transactions-content">
                            <table id="transactions-table" class="transactions-table">
                                <thead>
                                    <tr>
                                        <th><?php p($l->t('Date')); ?></th>
                                        <th><?php p($l->t('Description')); ?></th>
                                        <th><?php p($l->t('Category')); ?></th>
                                        <th><?php p($l->t('Amount')); ?></th>
                                        <th><?php p($l->t('Type')); ?></th>
                                        <th><?php p($l->t('Actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-table-body">
                                    <!-- Transactions will be dynamically populated here -->
                                </tbody>
                            </table>
                            
                            <div id="no-transactions-found" class="no-results hidden">
                                <?php p($l->t('No transactions found matching your search.')); ?>
                            </div>
                        </div>
                        
                        <div class="transactions-summary">
                            <div class="summary-item">
                                <span><?php p($l->t('Total Income')); ?>:</span>
                                <span id="total-income">$0.00</span>
                            </div>
                            <div class="summary-item">
                                <span><?php p($l->t('Total Expenses')); ?>:</span>
                                <span id="total-expenses">$0.00</span>
                            </div>
                            <div class="summary-item">
                                <span><?php p($l->t('Net Balance')); ?>:</span>
                                <span id="net-balance">$0.00</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Investments Section -->
                <section id="investments-section" class="finance-section" style="display: none;">
                    <div class="finance-section-content">
                        <h2><?php p($l->t('Investments')); ?></h2>
                        
                        <div class="investments-header">
                            <div class="stock-search-container">
                                <input 
                                    type="text" 
                                    id="stock-search-input" 
                                    placeholder="<?php p($l->t('Search stocks by ticker or company name...')); ?>"
                                >
                                <button id="stock-search-btn" class="primary">
                                    <i class="fas fa-search"></i> <?php p($l->t('Search')); ?>
                                </button>
                            </div>
                            
                            <div class="investments-actions">
                                <button id="add-investment-btn" class="primary">
                                    <?php p($l->t('Add Investment')); ?>
                                </button>
                            </div>
                        </div>

                        <div id="stock-search-results" class="stock-search-results hidden">
                            <h3><?php p($l->t('Search Results')); ?></h3>
                            <table id="stock-search-results-table">
                                <thead>
                                    <tr>
                                        <th><?php p($l->t('Symbol')); ?></th>
                                        <th><?php p($l->t('Company Name')); ?></th>
                                        <th><?php p($l->t('Current Price')); ?></th>
                                        <th><?php p($l->t('Daily Change')); ?></th>
                                        <th><?php p($l->t('Performance')); ?></th>
                                        <th><?php p($l->t('Volume')); ?></th>
                                        <th><?php p($l->t('Actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="stock-search-results-body">
                                    <!-- Dynamically populated -->
                                </tbody>
                            </table>
                        </div>

                        <div id="stock-details-modal" class="modal hidden">
                            <div class="modal-content">
                                <span class="close-modal">&times;</span>
                                <h2 id="stock-details-title"><?php p($l->t('Stock Details')); ?></h2>
                                <div id="stock-details-content">
                                    <!-- Detailed stock information will be populated here -->
                                </div>
                                <div class="stock-details-actions">
                                    <button id="add-to-portfolio-btn" class="primary">
                                        <?php p($l->t('Add to Portfolio')); ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="investments-content">
                            <table id="investments-table" class="investments-table">
                                <thead>
                                    <tr>
                                        <th><?php p($l->t('Symbol')); ?></th>
                                        <th><?php p($l->t('Name')); ?></th>
                                        <th><?php p($l->t('Quantity')); ?></th>
                                        <th><?php p($l->t('Purchase Price')); ?></th>
                                        <th><?php p($l->t('Current Price')); ?></th>
                                        <th><?php p($l->t('Daily Change')); ?></th>
                                        <th><?php p($l->t('Total Value')); ?></th>
                                        <th><?php p($l->t('Total Return')); ?></th>
                                        <th><?php p($l->t('Performance Metrics')); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="investments-table-body">
                                    <!-- Dynamically populated -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Budget Section -->
                <section id="budget-section" class="finance-section" style="display: none;">
                    <div class="finance-section-content">
                        <h2><?php p($l->t('Budget')); ?></h2>
                        <div class="budgets-list"></div>
                        <button id="add-budget-btn" class="primary"><?php p($l->t('Add Budget')); ?></button>
                    </div>
                </section>

                <!-- Reports Section -->
                <section id="reports-section" class="finance-section" style="display: none;">
                    <div class="finance-section-content">
                        <h2><?php p($l->t('Reports')); ?></h2>
                        <div class="reports-content">
                            <p><?php p($l->t('Generate and view financial reports')); ?></p>
                            
                            <div class="report-buttons">
                                <button id="generate-financial-overview-report" class="primary">
                                    <i class="fas fa-chart-pie"></i> <?php p($l->t('Financial Overview')); ?>
                                </button>
                                
                                <button id="generate-trend-analysis-report" class="secondary">
                                    <i class="fas fa-chart-line"></i> <?php p($l->t('Trend Analysis')); ?>
                                </button>
                                
                                <button id="generate-investment-report" class="secondary">
                                    <i class="fas fa-money-bill-trend-up"></i> <?php p($l->t('Investment Performance')); ?>
                                </button>
                                
                                <button id="generate-tax-projection-report" class="secondary">
                                    <i class="fas fa-file-invoice-dollar"></i> <?php p($l->t('Tax Projection')); ?>
                                </button>
                            </div>

                            <div id="report-export-options" class="report-export-options hidden">
                                <h3><?php p($l->t('Export Report')); ?></h3>
                                <select id="report-export-format">
                                    <option value="csv"><?php p($l->t('CSV')); ?></option>
                                    <option value="json"><?php p($l->t('JSON')); ?></option>
                                    <option value="pdf"><?php p($l->t('PDF')); ?></option>
                                </select>
                                <button id="export-report-btn" class="primary">
                                    <i class="fas fa-download"></i> <?php p($l->t('Export')); ?>
                                </button>
                            </div>

                            <div id="generated-report-container" class="generated-report">
                                <!-- Generated report will be displayed here -->
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Adding Items -->
<div id="modal-overlay" class="modal-overlay hidden">
    <!-- Account Modal -->
    <div id="account-modal" class="modal hidden">
        <div class="modal-content">
            <h2><?php p($l->t('Add Account')); ?></h2>
            <form id="account-form" class="form-group">
                <div class="form-group">
                    <label for="account-name"><?php p($l->t('Account Name')); ?></label>
                    <input type="text" 
                           id="account-name" 
                           class="input-field" 
                           placeholder="<?php p($l->t('Account Name')); ?>" 
                           required>
                </div>
                <select id="account-type">
                    <option value="checking"><?php p($l->t('Checking')); ?></option>
                    <option value="savings"><?php p($l->t('Savings')); ?></option>
                    <option value="credit"><?php p($l->t('Credit Card')); ?></option>
                </select>
                <input type="number" id="account-balance" placeholder="<?php p($l->t('Initial Balance')); ?>" step="0.01" required>
                <div class="modal-actions">
                    <button type="submit" class="primary"><?php p($l->t('Save')); ?></button>
                    <button type="button" class="cancel"><?php p($l->t('Cancel')); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Modal -->
    <div id="transaction-modal" class="modal hidden">
        <div class="modal-content">
            <h2><?php p($l->t('Add Transaction')); ?></h2>
            <form id="transaction-form">
                <input type="text" id="transaction-description" placeholder="<?php p($l->t('Description')); ?>" required>
                <input type="number" id="transaction-amount" placeholder="<?php p($l->t('Amount')); ?>" step="0.01" required>
                <select id="transaction-type">
                    <option value="expense"><?php p($l->t('Expense')); ?></option>
                    <option value="income"><?php p($l->t('Income')); ?></option>
                </select>
                <select id="transaction-account">
                    <option value=""><?php p($l->t('Select Account')); ?></option>
                    <!-- This will be populated dynamically -->
                </select>
                <input type="date" id="transaction-date" required>
                <div class="modal-actions">
                    <button type="button" class="button cancel"><?php p($l->t('Cancel')); ?></button>
                    <button type="submit" class="primary"><?php p($l->t('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Budget Modal -->
    <div id="budget-modal" class="modal hidden">
        <div class="modal-content">
            <h2><?php p($l->t('Add Budget')); ?></h2>
            <form id="budget-form" class="form-group">
                <div class="form-group">
                    <label for="budget-name"><?php p($l->t('Budget Name')); ?></label>
                    <input type="text" 
                           id="budget-name" 
                           class="input-field" 
                           placeholder="<?php p($l->t('Budget Name')); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label for="budget-amount"><?php p($l->t('Budget Amount')); ?></label>
                    <input type="number" 
                           id="budget-amount" 
                           class="input-field" 
                           placeholder="<?php p($l->t('Budget Amount')); ?>" 
                           step="0.01" 
                           required>
                </div>
                <div class="form-group">
                    <label for="budget-category"><?php p($l->t('Category')); ?></label>
                    <select id="budget-category" class="select-field">
                        <option value=""><?php p($l->t('Select Category')); ?></option>
                        <option value="groceries"><?php p($l->t('Groceries')); ?></option>
                        <option value="dining"><?php p($l->t('Dining Out')); ?></option>
                        <option value="entertainment"><?php p($l->t('Entertainment')); ?></option>
                        <option value="utilities"><?php p($l->t('Utilities')); ?></option>
                        <option value="transportation"><?php p($l->t('Transportation')); ?></option>
                        <option value="other"><?php p($l->t('Other')); ?></option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="button cancel"><?php p($l->t('Cancel')); ?></button>
                    <button type="submit" class="primary"><?php p($l->t('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Investment Modal -->
    <div id="investment-modal" class="modal hidden">
        <div class="modal-content">
            <h2><?php p($l->t('Add Investment')); ?></h2>
            <form id="investment-form">
                <input type="text" id="investment-name" placeholder="<?php p($l->t('Investment Name')); ?>" required>
                <input type="text" id="investment-ticker" placeholder="<?php p($l->t('Ticker Symbol')); ?>">
                <input type="number" id="investment-shares" placeholder="<?php p($l->t('Number of Shares')); ?>" step="0.001" required>
                <input type="number" id="investment-price" placeholder="<?php p($l->t('Price per Share')); ?>" step="0.01" required>
                <div class="modal-actions">
                    <button type="submit" class="primary"><?php p($l->t('Save')); ?></button>
                    <button type="button" class="cancel"><?php p($l->t('Cancel')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
