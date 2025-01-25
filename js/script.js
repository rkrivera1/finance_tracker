document.addEventListener('DOMContentLoaded', function() {
    // Utility function for showing toast notifications
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.classList.add('notification', `notification-${type}`);
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    }

    // Modal functionality
    const modalOverlay = document.getElementById('modal-overlay');
    const modals = {
        account: document.getElementById('account-modal'),
        budget: document.getElementById('budget-modal'),
        transaction: document.getElementById('transaction-modal'),
        investment: document.getElementById('investment-modal')
    };

    const buttons = {
        addAccount: document.getElementById('add-account-btn'),
        addBudget: document.getElementById('add-budget-btn'),
        addTransaction: document.getElementById('add-transaction-btn'),
        addInvestment: document.getElementById('add-investment-btn')
    };

    const forms = {
        account: document.getElementById('account-form'),
        budget: document.getElementById('budget-form'),
        transaction: document.getElementById('transaction-form'),
        investment: document.getElementById('investment-form')
    };

    const lists = {
        accounts: document.getElementById('accounts-list'),
        budgets: document.getElementById('budgets-list'),
        transactions: document.getElementById('transactions-list'),
        investments: document.getElementById('investments-list')
    };

    // Open modal functions
    function openModal(modalType) {
        modalOverlay.classList.remove('hidden');
        modals[modalType].classList.remove('hidden');
    }

    // Close modal functions
    function closeModal() {
        modalOverlay.classList.add('hidden');
        Object.values(modals).forEach(modal => modal.classList.add('hidden'));
    }

    // Fetch and display accounts
    function fetchAccounts() {
        fetch(OC.generateUrl('apps/finance_tracker/accounts'))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch accounts');
                }
                return response.json();
            })
            .then(accounts => {
                lists.accounts.innerHTML = ''; // Clear existing accounts
                if (accounts.length === 0) {
                    const noAccountsMessage = document.createElement('p');
                    noAccountsMessage.textContent = 'No accounts found. Add your first account!';
                    lists.accounts.appendChild(noAccountsMessage);
                    return;
                }
                accounts.forEach(account => {
                    const accountElement = document.createElement('div');
                    accountElement.classList.add('account-item');
                    accountElement.innerHTML = `
                        <strong>${account.name}</strong>
                        <span>${account.type}</span>
                        <span>$${account.balance.toFixed(2)}</span>
                    `;
                    lists.accounts.appendChild(accountElement);
                });
            })
            .catch(error => {
                console.error('Error fetching accounts:', error);
                showNotification('Failed to load accounts', 'error');
            });
    }

    // Fetch and display budgets
    function fetchBudgets() {
        fetch(OC.generateUrl('apps/finance_tracker/budgets'))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch budgets');
                }
                return response.json();
            })
            .then(budgets => {
                lists.budgets.innerHTML = ''; // Clear existing budgets
                if (budgets.length === 0) {
                    const noBudgetsMessage = document.createElement('p');
                    noBudgetsMessage.textContent = 'No budgets found. Create your first budget!';
                    lists.budgets.appendChild(noBudgetsMessage);
                    return;
                }
                budgets.forEach(budget => {
                    const budgetElement = document.createElement('div');
                    budgetElement.classList.add('budget-item');
                    budgetElement.innerHTML = `
                        <strong>${budget.name}</strong>
                        <span>${budget.category}</span>
                        <span>$${budget.amount.toFixed(2)}</span>
                        <span>${new Date(budget.startDate).toLocaleDateString()} - ${new Date(budget.endDate).toLocaleDateString()}</span>
                    `;
                    lists.budgets.appendChild(budgetElement);
                });
            })
            .catch(error => {
                console.error('Error fetching budgets:', error);
                showNotification('Failed to load budgets', 'error');
            });
    }

    // Fetch and display transactions
    function fetchTransactions() {
        fetch(OC.generateUrl('apps/finance_tracker/transactions'))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch transactions');
                }
                return response.json();
            })
            .then(transactions => {
                lists.transactions.innerHTML = ''; // Clear existing transactions
                if (transactions.length === 0) {
                    const noTransactionsMessage = document.createElement('p');
                    noTransactionsMessage.textContent = 'No transactions found.';
                    lists.transactions.appendChild(noTransactionsMessage);
                    return;
                }
                transactions.forEach(transaction => {
                    const transactionElement = document.createElement('div');
                    transactionElement.classList.add('transaction-item');
                    transactionElement.innerHTML = `
                        <strong>${transaction.description}</strong>
                        <span>${transaction.type}</span>
                        <span>$${transaction.amount.toFixed(2)}</span>
                    `;
                    lists.transactions.appendChild(transactionElement);
                });
            })
            .catch(error => {
                console.error('Error fetching transactions:', error);
                showNotification('Failed to load transactions', 'error');
            });
    }

    // Fetch and display investments
    function fetchInvestments() {
        fetch(OC.generateUrl('apps/finance_tracker/investments'))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch investments');
                }
                return response.json();
            })
            .then(investments => {
                lists.investments.innerHTML = ''; // Clear existing investments
                if (investments.length === 0) {
                    const noInvestmentsMessage = document.createElement('p');
                    noInvestmentsMessage.textContent = 'No investments found.';
                    lists.investments.appendChild(noInvestmentsMessage);
                    return;
                }
                investments.forEach(investment => {
                    const investmentElement = document.createElement('div');
                    investmentElement.classList.add('investment-item');
                    investmentElement.innerHTML = `
                        <strong>${investment.name}</strong>
                        <span>Ticker: ${investment.ticker}</span>
                        <span>Shares: ${investment.shares}</span>
                        <span>Purchase Price: $${investment.purchasePrice.toFixed(2)}</span>
                    `;
                    lists.investments.appendChild(investmentElement);
                });
            })
            .catch(error => {
                console.error('Error fetching investments:', error);
                showNotification('Failed to load investments', 'error');
            });
    }

    // Event listeners for opening modals
    buttons.addAccount.addEventListener('click', () => openModal('account'));
    buttons.addBudget.addEventListener('click', () => openModal('budget'));
    buttons.addTransaction.addEventListener('click', () => openModal('transaction'));
    buttons.addInvestment.addEventListener('click', () => openModal('investment'));

    // Event listeners for cancel buttons
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    // Close modal when clicking outside
    modalOverlay.addEventListener('click', function(event) {
        if (event.target === modalOverlay) {
            closeModal();
        }
    });

    // Account form submission
    forms.account.addEventListener('submit', function(event) {
        event.preventDefault();
        const accountName = document.getElementById('account-name').value;
        const accountType = document.getElementById('account-type').value;
        const accountBalance = document.getElementById('account-balance').value;

        fetch(OC.generateUrl('apps/finance_tracker/accounts'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            },
            body: JSON.stringify({
                name: accountName,
                type: accountType,
                balance: accountBalance
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to create account');
            }
            return response.json();
        })
        .then(account => {
            showNotification('Account created successfully', 'success');
            fetchAccounts(); // Refresh accounts list
            closeModal();
        })
        .catch(error => {
            console.error('Error creating account:', error);
            showNotification('Failed to create account', 'error');
        });
    });

    // Budget form submission
    forms.budget.addEventListener('submit', function(event) {
        event.preventDefault();
        const budgetName = document.getElementById('budget-name').value;
        const budgetAmount = document.getElementById('budget-amount').value;
        const budgetCategory = document.getElementById('budget-category').value;
        const budgetStartDate = document.getElementById('budget-start-date').value;
        const budgetEndDate = document.getElementById('budget-end-date').value;

        fetch(OC.generateUrl('apps/finance_tracker/budgets'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            },
            body: JSON.stringify({
                name: budgetName,
                amount: budgetAmount,
                category: budgetCategory,
                startDate: budgetStartDate,
                endDate: budgetEndDate
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to create budget');
            }
            return response.json();
        })
        .then(budget => {
            showNotification('Budget created successfully', 'success');
            fetchBudgets(); // Refresh budgets list
            closeModal();
        })
        .catch(error => {
            console.error('Error creating budget:', error);
            showNotification('Failed to create budget', 'error');
        });
    });

    // Transaction form submission
    forms.transaction.addEventListener('submit', function(event) {
        event.preventDefault();
        const description = document.getElementById('transaction-description').value;
        const amount = document.getElementById('transaction-amount').value;
        const type = document.getElementById('transaction-type').value;
        const accountId = document.getElementById('transaction-account').value;

        fetch(OC.generateUrl('apps/finance_tracker/transactions'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                description: description,
                amount: amount,
                type: type,
                accountId: accountId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to add transaction');
            }
            return response.json();
        })
        .then(transaction => {
            showNotification('Transaction added successfully', 'success');
            fetchTransactions();
            closeModal();
        })
        .catch(error => {
            console.error('Error adding transaction:', error);
            showNotification('Failed to add transaction', 'error');
        });
    });

    // Investment form submission
    forms.investment.addEventListener('submit', function(event) {
        event.preventDefault();
        const name = document.getElementById('investment-name').value;
        const ticker = document.getElementById('investment-ticker').value;
        const shares = document.getElementById('investment-shares').value;
        const purchasePrice = document.getElementById('investment-purchase-price').value;

        fetch(OC.generateUrl('apps/finance_tracker/investments'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                ticker: ticker,
                shares: shares,
                purchasePrice: purchasePrice
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to add investment');
            }
            return response.json();
        })
        .then(investment => {
            showNotification('Investment added successfully', 'success');
            fetchInvestments();
            closeModal();
        })
        .catch(error => {
            console.error('Error adding investment:', error);
            showNotification('Failed to add investment', 'error');
        });
    });

    // Initial fetches
    fetchAccounts();
    fetchBudgets();
    fetchTransactions();
    fetchInvestments();
});
