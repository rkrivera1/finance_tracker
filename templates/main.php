<?php
script('finance_tracker', 'script');
style('finance_tracker', 'style');
?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('navigation')); ?>
    </div>

    <div id="app-content">
        <div class="finance-tracker-container">
            <h1>Finance Tracker</h1>

            <div class="actions">
                <button id="add-account-btn" class="primary">Add Account</button>
                <button id="add-budget-btn" class="primary">Add Budget</button>
                <button id="add-transaction-btn" class="primary">Add Transaction</button>
                <button id="add-investment-btn" class="primary">Add Investment</button>
            </div>

            <div class="finance-sections">
                <section class="accounts-section">
                    <h2>Accounts</h2>
                    <div id="accounts-list" class="item-list"></div>
                </section>

                <section class="budgets-section">
                    <h2>Budgets</h2>
                    <div id="budgets-list" class="item-list"></div>
                </section>
            </div>

            <!-- Modals -->
            <div id="modal-overlay" class="modal-overlay hidden">
                <!-- Account Modal -->
                <div id="account-modal" class="modal hidden">
                    <div class="modal-content">
                        <h2>Add Account</h2>
                        <form id="account-form">
                            <input type="text" id="account-name" placeholder="Account Name" required>
                            <select id="account-type" required>
                                <option value="">Select Account Type</option>
                                <option value="checking">Checking</option>
                                <option value="savings">Savings</option>
                                <option value="credit">Credit Card</option>
                                <option value="investment">Investment</option>
                            </select>
                            <input type="number" id="account-balance" placeholder="Initial Balance" step="0.01" required>
                            <div class="modal-actions">
                                <button type="submit" class="primary">Add Account</button>
                                <button type="button" class="cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Budget Modal -->
                <div id="budget-modal" class="modal hidden">
                    <div class="modal-content">
                        <h2>Add Budget</h2>
                        <form id="budget-form">
                            <input type="text" id="budget-name" placeholder="Budget Name" required>
                            <input type="number" id="budget-amount" placeholder="Budget Amount" step="0.01" required>
                            <select id="budget-category" required>
                                <option value="">Select Category</option>
                                <option value="groceries">Groceries</option>
                                <option value="dining">Dining Out</option>
                                <option value="entertainment">Entertainment</option>
                                <option value="utilities">Utilities</option>
                                <option value="transportation">Transportation</option>
                                <option value="other">Other</option>
                            </select>
                            <label>Start Date</label>
                            <input type="date" id="budget-start-date" required>
                            <label>End Date</label>
                            <input type="date" id="budget-end-date" required>
                            <div class="modal-actions">
                                <button type="submit" class="primary">Add Budget</button>
                                <button type="button" class="cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transaction Modal -->
                <div id="transaction-modal" class="modal hidden">
                    <div class="modal-content">
                        <h2>Add Transaction</h2>
                        <form id="transaction-form">
                            <input type="text" id="transaction-description" placeholder="Description" required>
                            <input type="number" id="transaction-amount" placeholder="Amount" step="0.01" required>
                            <select id="transaction-type" required>
                                <option value="">Select Transaction Type</option>
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                            <select id="transaction-account" required>
                                <option value="">Select Account</option>
                                <!-- This will be populated dynamically -->
                            </select>
                            <input type="date" id="transaction-date" required>
                            <div class="modal-actions">
                                <button type="submit" class="primary">Add Transaction</button>
                                <button type="button" class="cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Investment Modal -->
                <div id="investment-modal" class="modal hidden">
                    <div class="modal-content">
                        <h2>Add Investment</h2>
                        <form id="investment-form">
                            <input type="text" id="investment-name" placeholder="Investment Name" required>
                            <input type="text" id="investment-ticker" placeholder="Ticker Symbol">
                            <input type="number" id="investment-shares" placeholder="Number of Shares" step="0.001" required>
                            <input type="number" id="investment-price" placeholder="Price per Share" step="0.01" required>
                            <div class="modal-actions">
                                <button type="submit" class="primary">Add Investment</button>
                                <button type="button" class="cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
