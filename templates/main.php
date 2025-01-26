<?php
script('finance_tracker', 'script');
style('finance_tracker', 'style');
?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('navigation')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
            <!-- Accounts Section -->
            <section id="accounts-section" class="finance-section" style="display: none;">
                <h2><?php p($l->t('Accounts')); ?></h2>
                <div class="accounts-list"></div>
                <button id="add-account-btn" class="primary"><?php p($l->t('Add Account')); ?></button>
            </section>

            <!-- Transactions Section -->
            <section id="transactions-section" class="finance-section" style="display: none;">
                <div class="transactions-header">
                    <h2><?php p($l->t('Transactions')); ?></h2>
                    <div class="transactions-actions">
                        <button id="add-transaction-btn" class="primary"><?php p($l->t('Add Transaction')); ?></button>
                        <div class="csv-upload-container">
                            <input type="file" id="csv-upload-input" accept=".csv" style="display: none;">
                            <button id="csv-upload-btn" class="secondary">
                                <i class="icon-upload"></i> <?php p($l->t('Upload Bank Statement')); ?>
                            </button>
                        </div>
                    </div>
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
                
                <div class="transactions-list"></div>
                
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
            </section>

            <!-- Budgets Section -->
            <section id="budgets-section" class="finance-section" style="display: none;">
                <h2><?php p($l->t('Budgets')); ?></h2>
                <div class="budgets-list"></div>
                <button id="add-budget-btn" class="primary"><?php p($l->t('Add Budget')); ?></button>
            </section>

            <!-- Investments Section -->
            <section id="investments-section" class="finance-section" style="display: none;">
                <h2><?php p($l->t('Investments')); ?></h2>
                <div class="investments-list"></div>
                <button id="add-investment-btn" class="primary"><?php p($l->t('Add Investment')); ?></button>
            </section>

            <!-- Reports Section -->
            <section id="reports-section" class="finance-section" style="display: none;">
                <h2><?php p($l->t('Reports')); ?></h2>
                <div class="reports-content">
                    <p><?php p($l->t('Generate and view financial reports')); ?></p>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Modals for Adding Items -->
<div id="modal-overlay" class="modal-overlay hidden">
    <!-- Account Modal -->
    <div id="account-modal" class="modal hidden">
        <div class="modal-content">
            <h2><?php p($l->t('Add Account')); ?></h2>
            <form id="account-form">
                <input type="text" id="account-name" placeholder="<?php p($l->t('Account Name')); ?>" required>
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
                    <button type="submit" class="primary"><?php p($l->t('Save')); ?></button>
                    <button type="button" class="cancel"><?php p($l->t('Cancel')); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Budget Modal -->
    <div id="budget-modal" class="modal hidden">
        <div class="modal-content">
            <h2><?php p($l->t('Add Budget')); ?></h2>
            <form id="budget-form">
                <input type="text" id="budget-name" placeholder="<?php p($l->t('Budget Name')); ?>" required>
                <input type="number" id="budget-amount" placeholder="<?php p($l->t('Budget Amount')); ?>" step="0.01" required>
                <select id="budget-category">
                    <option value=""><?php p($l->t('Select Category')); ?></option>
                    <option value="groceries"><?php p($l->t('Groceries')); ?></option>
                    <option value="dining"><?php p($l->t('Dining Out')); ?></option>
                    <option value="entertainment"><?php p($l->t('Entertainment')); ?></option>
                    <option value="utilities"><?php p($l->t('Utilities')); ?></option>
                    <option value="transportation"><?php p($l->t('Transportation')); ?></option>
                    <option value="other"><?php p($l->t('Other')); ?></option>
                </select>
                <label><?php p($l->t('Start Date')); ?></label>
                <input type="date" id="budget-start-date" required>
                <label><?php p($l->t('End Date')); ?></label>
                <input type="date" id="budget-end-date" required>
                <div class="modal-actions">
                    <button type="submit" class="primary"><?php p($l->t('Save')); ?></button>
                    <button type="button" class="cancel"><?php p($l->t('Cancel')); ?></button>
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
