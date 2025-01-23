<?php
script('finance_tracker', 'script');
style('finance_tracker', 'style');
?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('navigation')); ?>
    </div>

    <div id="app-content">
        <div class="container">
            <h1>Finance Tracker</h1>
            
            <div class="section accounts">
                <h2>Accounts</h2>
                <button id="add-account-btn">Add Account</button>
                <div id="accounts-list"></div>
            </div>

            <div class="section budgets">
                <h2>Budgets</h2>
                <button id="add-budget-btn">Create Budget</button>
                <div id="budgets-list"></div>
            </div>

            <div class="section transactions">
                <h2>Transactions</h2>
                <button id="add-transaction-btn">Add Transaction</button>
                <div id="transactions-list"></div>
            </div>

            <div class="section investments">
                <h2>Investments</h2>
                <button id="add-investment-btn">Add Investment</button>
                <div id="investments-list"></div>
            </div>
        </div>

        <!-- Modals -->
        <div id="modal-overlay" class="modal-overlay hidden">
            <div id="account-modal" class="modal hidden">
                <h3>Add Account</h3>
                <form id="account-form">
                    <input type="text" id="account-name" placeholder="Account Name" required>
                    <select id="account-type">
                        <option value="checking">Checking</option>
                        <option value="savings">Savings</option>
                        <option value="credit">Credit Card</option>
                    </select>
                    <input type="number" id="account-balance" placeholder="Initial Balance" step="0.01">
                    <button type="submit">Save Account</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>

            <div id="budget-modal" class="modal hidden">
                <h3>Create Budget</h3>
                <form id="budget-form">
                    <input type="text" id="budget-name" placeholder="Budget Name" required>
                    <input type="number" id="budget-amount" placeholder="Budget Amount" step="0.01" required>
                    <select id="budget-category">
                        <option value="groceries">Groceries</option>
                        <option value="dining">Dining Out</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="utilities">Utilities</option>
                    </select>
                    <button type="submit">Save Budget</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>

            <div id="transaction-modal" class="modal hidden">
                <h3>Add Transaction</h3>
                <form id="transaction-form">
                    <input type="text" id="transaction-description" placeholder="Description" required>
                    <input type="number" id="transaction-amount" placeholder="Amount" step="0.01" required>
                    <select id="transaction-type">
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                    <select id="transaction-account">
                        <!-- Dynamically populated from accounts -->
                    </select>
                    <input type="date" id="transaction-date" required>
                    <button type="submit">Save Transaction</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>

            <div id="investment-modal" class="modal hidden">
                <h3>Add Investment</h3>
                <form id="investment-form">
                    <input type="text" id="investment-name" placeholder="Investment Name" required>
                    <input type="text" id="investment-ticker" placeholder="Ticker Symbol">
                    <input type="number" id="investment-shares" placeholder="Number of Shares" step="0.001">
                    <input type="number" id="investment-price" placeholder="Price per Share" step="0.01">
                    <button type="submit">Save Investment</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
