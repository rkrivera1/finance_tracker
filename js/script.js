document.addEventListener('DOMContentLoaded', function() {
    // Initialize settings menu
    if (OCA.Settings) {
        OCA.Settings.Apps.setupApps();
    }

    const contentView = document.getElementById('content-view');
    const emptyContent = document.getElementById('emptycontent');

    // Navigation handling
    function handleNavigation(route) {
        // Show loading state
        contentView.innerHTML = '<div class="icon-loading"></div>';
        
        // Fetch content for the route
        fetch(`/index.php/apps/finance_tracker/api/v1/${route}`, {
            headers: {
                'requesttoken': OC.requestToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && Object.keys(data).length > 0) {
                emptyContent.classList.add('hidden');
                contentView.innerHTML = renderContent(route, data);
            } else {
                emptyContent.classList.remove('hidden');
                contentView.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            contentView.innerHTML = `<div class="error">${t('finance_tracker', 'Error loading content')}</div>`;
        });
    }

    // Handle navigation clicks
    document.querySelectorAll('#app-navigation li > a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const route = e.currentTarget.getAttribute('href').split('/').pop();
            
            // Update navigation state
            document.querySelectorAll('#app-navigation li').forEach(item => {
                item.classList.remove('active');
            });
            e.currentTarget.parentElement.classList.add('active');
            
            // Update URL and load content
            window.history.pushState({}, '', e.currentTarget.getAttribute('href'));
            handleNavigation(route);
        });
    });

    // Handle settings
    const saveSettingsBtn = document.getElementById('save-settings');
    if (saveSettingsBtn) {
        saveSettingsBtn.addEventListener('click', () => {
            const settings = {
                stockApiKey: document.getElementById('stock-api-key').value,
                stockApiProvider: document.getElementById('stock-api-provider').value
            };

            fetch('/index.php/apps/finance_tracker/api/v1/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(settings)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    OC.Notification.showTemporary(t('finance_tracker', 'Settings saved successfully'));
                }
            })
            .catch(error => {
                console.error('Error saving settings:', error);
                OC.Notification.showTemporary(t('finance_tracker', 'Error saving settings'));
            });
        });
    }

    // Load initial content
    const currentPath = window.location.pathname.split('/').pop() || 'dashboard';
    handleNavigation(currentPath);

    // Modal handlers
    const modalButtons = {
        'add-investment-btn': 'investment-modal',
        'add-budget-btn': 'budget-modal',
        'stock-api-settings-btn': 'stock-api-settings-modal',
        'add-transaction-btn': 'transaction-modal'
    };

    // Setup modal triggers
    Object.entries(modalButtons).forEach(([btnId, modalId]) => {
        const button = document.getElementById(btnId);
        const modal = document.getElementById(modalId);
        if (button && modal) {
            button.addEventListener('click', () => {
                document.getElementById('modal-overlay').classList.remove('hidden');
                modal.classList.remove('hidden');
            });
        }
    });

    // Close modal handlers
    document.querySelectorAll('.modal .cancel, .modal .close-modal').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('modal-overlay').classList.add('hidden');
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.add('hidden');
            });
        });
    });

    // Form submissions
    const forms = {
        'investment-form': '/apps/finance_tracker/api/v1/investments',
        'budget-form': '/apps/finance_tracker/api/v1/budgets',
        'transaction-form': '/apps/finance_tracker/api/v1/transactions',
        'stock-api-settings-form': '/apps/finance_tracker/api/v1/settings'
    };

    Object.entries(forms).forEach(([formId, endpoint]) => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                
                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'requesttoken': OC.requestToken
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                    if (data.status === 'success') {
                        // Hide modal
                        document.getElementById('modal-overlay').classList.add('hidden');
                        document.querySelectorAll('.modal').forEach(modal => {
                            modal.classList.add('hidden');
                        });
                        
                        // Show success message
                        OC.Notification.showTemporary(t('finance_tracker', 'Changes saved successfully'));
                        
                        // Refresh relevant section
                        refreshCurrentSection();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    OC.Notification.showTemporary(t('finance_tracker', 'Error saving changes'));
                }
            });
        }
    });

    // Handle initial load
    function handleInitialLoad() {
        const hash = window.location.hash.substring(1) || 'dashboard';
        showSection(hash);
    }

    // Refresh current section data
    function refreshCurrentSection() {
        const currentSection = document.querySelector('.section.active');
        if (currentSection) {
            const sectionId = currentSection.id.replace('-section', '');
            // Implement refresh logic based on section
            console.log('Refreshing section:', sectionId);
            // TODO: Add specific refresh logic for each section
        }
    }

    // Initialize
    handleInitialLoad();
    window.addEventListener('hashchange', handleInitialLoad);


    // Show the first section by default
    function showDefaultSection() {
        sections.forEach(section => section.style.display = 'none');
        document.getElementById('dashboard-section').style.display = 'block';
    }

    // Navigation click handler
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section') + '-section';
            
            // Hide all sections
            sections.forEach(section => section.style.display = 'none');
            
            // Show selected section
            const selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
        });
    });

    // Modal functionality
    const modals = {
        account: document.getElementById('account-modal'),
        transaction: document.getElementById('transaction-modal'),
        budget: document.getElementById('budget-modal'),
        investment: document.getElementById('investment-modal')
    };

    const modalButtons = {
        addAccount: document.getElementById('add-account-btn'),
        addTransaction: document.getElementById('add-transaction-btn'),
        addBudget: document.getElementById('add-budget-btn'),
        addInvestment: document.getElementById('add-investment-btn')
    };

    const modalForms = {
        account: document.getElementById('account-form'),
        transaction: document.getElementById('transaction-form'),
        budget: document.getElementById('budget-form'),
        investment: document.getElementById('investment-form')
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

    // Attach modal open events
    Object.keys(modalButtons).forEach(key => {
        modalButtons[key].addEventListener('click', () => {
            const modalType = key.replace('add', '').toLowerCase();
            openModal(modalType);
        });
    });

    // Attach modal close events
    document.querySelectorAll('.modal .cancel').forEach(cancelBtn => {
        cancelBtn.addEventListener('click', closeModal);
    });

    // Form submission handlers (placeholder)
    Object.keys(modalForms).forEach(key => {
        modalForms[key].addEventListener('submit', function(e) {
            e.preventDefault();
            // TODO: Implement actual form submission logic
            alert(`${key.charAt(0).toUpperCase() + key.slice(1)} form submitted`);
            closeModal();
        });
    });

    // Initialize with default section
    showDefaultSection();

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

    // Fetch and display accounts with detailed error handling
    function fetchAccounts() {
        const url = OC.generateUrl('/apps/finance_tracker/accounts');
        console.log('Fetching accounts from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Accounts response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(accounts => {
                console.log('Accounts fetched:', accounts);
                const accountsList = document.getElementById('accounts-list');
                accountsList.innerHTML = ''; // Clear existing accounts

                if (accounts.length === 0) {
                    const noAccountsMessage = document.createElement('p');
                    noAccountsMessage.textContent = 'No accounts found. Add your first account!';
                    accountsList.appendChild(noAccountsMessage);
                    return;
                }

                accounts.forEach(account => {
                    const accountElement = document.createElement('div');
                    accountElement.classList.add('account-item');
                    accountElement.innerHTML = `
                        <div class="account-details">
                            <span class="account-name">${account.name}</span>
                            <span class="account-type">${account.type}</span>
                            <span class="account-balance">$${account.balance.toFixed(2)}</span>
                        </div>
                    `;
                    accountsList.appendChild(accountElement);
                });
            })
            .catch(error => {
                console.error('Detailed Account Fetch Error:', error);
                showNotification(`Failed to load accounts: ${error.message}`, 'error');
            });
    }

    // Fetch and display budgets with detailed error handling
    function fetchBudgets() {
        const url = OC.generateUrl('/apps/finance_tracker/budgets');
        console.log('Fetching budgets from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Budgets response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(budgets => {
                console.log('Budgets fetched:', budgets);
                const budgetsList = document.getElementById('budgets-list');
                budgetsList.innerHTML = ''; // Clear existing budgets

                if (budgets.length === 0) {
                    const noBudgetsMessage = document.createElement('p');
                    noBudgetsMessage.textContent = 'No budgets found. Create your first budget!';
                    budgetsList.appendChild(noBudgetsMessage);
                    return;
                }

                budgets.forEach(budget => {
                    const budgetElement = document.createElement('div');
                    budgetElement.classList.add('budget-item');
                    budgetElement.innerHTML = `
                        <div class="budget-details">
                            <span class="budget-name">${budget.name}</span>
                            <span class="budget-category">${budget.category}</span>
                            <span class="budget-amount">$${budget.amount.toFixed(2)}</span>
                            <span class="budget-period">
                                ${new Date(budget.startDate).toLocaleDateString()} - 
                                ${new Date(budget.endDate).toLocaleDateString()}
                            </span>
                        </div>
                    `;
                    budgetsList.appendChild(budgetElement);
                });
            })
            .catch(error => {
                console.error('Detailed Budget Fetch Error:', error);
                showNotification(`Failed to load budgets: ${error.message}`, 'error');
            });
    }

    // Fetch and display transactions with detailed error handling
    function fetchTransactions() {
        const url = OC.generateUrl('/apps/finance_tracker/transactions');
        console.log('Fetching transactions from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Transactions response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(transactions => {
                console.log('Transactions fetched:', transactions);
                const transactionsList = document.querySelector('.transactions-list');
                transactionsList.innerHTML = ''; // Clear existing transactions

                if (transactions.length === 0) {
                    const noTransactionsMessage = document.createElement('p');
                    noTransactionsMessage.textContent = 'No transactions found.';
                    transactionsList.appendChild(noTransactionsMessage);
                    return;
                }

                transactions.forEach(transaction => {
                    const transactionElement = document.createElement('div');
                    transactionElement.classList.add('transaction-item');
                    transactionElement.innerHTML = `
                        <div class="transaction-details">
                            <span class="transaction-date">${new Date(transaction.date).toLocaleDateString()}</span>
                            <span class="transaction-description">${transaction.description}</span>
                            <span class="transaction-category">${transaction.category}</span>
                            <span class="transaction-amount ${transaction.type === 'income' ? 'income' : 'expense'}">
                                ${transaction.type === 'income' ? '+' : '-'}$${transaction.amount.toFixed(2)}
                            </span>
                        </div>
                    `;
                    transactionsList.appendChild(transactionElement);
                });
            })
            .catch(error => {
                console.error('Detailed Transaction Fetch Error:', error);
                showNotification(`Failed to load transactions: ${error.message}`, 'error');
            });
    }

    // Fetch and display investments with detailed error handling
    function fetchInvestments() {
        const url = OC.generateUrl('/apps/finance_tracker/investments');
        console.log('Fetching investments from:', url);
        
        fetch(url)
            .then(response => {
                console.log('Investments response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(investments => {
                console.log('Investments fetched:', investments);
                const investmentsList = document.querySelector('.investments-list');
                investmentsList.innerHTML = ''; // Clear existing investments

                if (investments.length === 0) {
                    const noInvestmentsMessage = document.createElement('p');
                    noInvestmentsMessage.textContent = 'No investments found.';
                    investmentsList.appendChild(noInvestmentsMessage);
                    return;
                }

                investments.forEach(investment => {
                    const investmentElement = document.createElement('div');
                    investmentElement.classList.add('investment-item');
                    investmentElement.innerHTML = `
                        <div class="investment-details">
                            <span class="investment-name">${investment.name}</span>
                            <span class="investment-ticker">${investment.ticker || 'N/A'}</span>
                            <span class="investment-shares">Shares: ${investment.shares}</span>
                            <span class="investment-price">Purchase Price: $${investment.purchasePrice.toFixed(2)}</span>
                        </div>
                    `;
                    investmentsList.appendChild(investmentElement);
                });
            })
            .catch(error => {
                console.error('Detailed Investment Fetch Error:', error);
                showNotification(`Failed to load investments: ${error.message}`, 'error');
            });
    }

    // Account form submission
    document.getElementById('account-form').addEventListener('submit', function(event) {
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
    document.getElementById('budget-form').addEventListener('submit', function(event) {
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
    document.getElementById('transaction-form').addEventListener('submit', function(event) {
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
    document.getElementById('investment-form').addEventListener('submit', function(event) {
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

    // CSV Upload Functionality
    function setupCSVUpload() {
        const csvUploadBtn = document.getElementById('csv-upload-btn');
        const csvUploadInput = document.getElementById('csv-upload-input');

        // Trigger file input when upload button is clicked
        csvUploadBtn.addEventListener('click', () => {
            csvUploadInput.click();
        });

        // Handle file selection
        csvUploadInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.type !== 'text/csv') {
                    showNotification('Please upload a valid CSV file', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('csvFile', file);

                // Send CSV to server for processing
                fetch(OC.generateUrl('apps/finance_tracker/transactions/upload-csv'), {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('CSV upload failed');
                    }
                    return response.json();
                })
                .then(result => {
                    showNotification(`Uploaded ${result.transactionsAdded} transactions`, 'success');
                    // Refresh transactions list
                    fetchTransactions();
                })
                .catch(error => {
                    console.error('CSV Upload Error:', error);
                    showNotification('Failed to upload CSV', 'error');
                });
            }
        });
    }

    // Transaction Filtering
    function setupTransactionFilters() {
        const accountFilter = document.getElementById('transaction-account-filter');
        const categoryFilter = document.getElementById('transaction-category-filter');
        const startDateFilter = document.getElementById('transaction-start-date');
        const endDateFilter = document.getElementById('transaction-end-date');

        // Populate account filter dynamically
        function populateAccountFilter() {
            fetch(OC.generateUrl('apps/finance_tracker/accounts'))
                .then(response => response.json())
                .then(accounts => {
                    accountFilter.innerHTML = '<option value="">All Accounts</option>';
                    accounts.forEach(account => {
                        const option = document.createElement('option');
                        option.value = account.id;
                        option.textContent = account.name;
                        accountFilter.appendChild(option);
                    });
                });
        }

        // Apply filters to transactions
        function applyTransactionFilters() {
            const filters = {
                accountId: accountFilter.value,
                category: categoryFilter.value,
                startDate: startDateFilter.value,
                endDate: endDateFilter.value
            };

            fetch(OC.generateUrl('apps/finance_tracker/transactions'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(filters)
            })
            .then(response => response.json())
            .then(transactions => {
                renderTransactions(transactions);
                updateTransactionSummary(transactions);
            })
            .catch(error => {
                console.error('Transaction Filter Error:', error);
                showNotification('Failed to filter transactions', 'error');
            });
        }

        // Render transactions with additional details
        function renderTransactions(transactions) {
            const transactionsList = document.querySelector('.transactions-list');
            transactionsList.innerHTML = '';

            if (transactions.length === 0) {
                const noTransactionsMessage = document.createElement('p');
                noTransactionsMessage.textContent = 'No transactions found.';
                transactionsList.appendChild(noTransactionsMessage);
                return;
            }

            transactions.forEach(transaction => {
                const transactionElement = document.createElement('div');
                transactionElement.classList.add('transaction-item');
                transactionElement.innerHTML = `
                    <div class="transaction-details">
                        <span class="transaction-date">${new Date(transaction.date).toLocaleDateString()}</span>
                        <span class="transaction-description">${transaction.description}</span>
                        <span class="transaction-category">${transaction.category}</span>
                        <span class="transaction-amount ${transaction.type === 'income' ? 'income' : 'expense'}">
                            ${transaction.type === 'income' ? '+' : '-'}$${transaction.amount.toFixed(2)}
                        </span>
                    </div>
                `;
                transactionsList.appendChild(transactionElement);
            });
        }

        // Update transaction summary
        function updateTransactionSummary(transactions) {
            const totalIncomeEl = document.getElementById('total-income');
            const totalExpensesEl = document.getElementById('total-expenses');
            const netBalanceEl = document.getElementById('net-balance');

            const totalIncome = transactions
                .filter(t => t.type === 'income')
                .reduce((sum, t) => sum + t.amount, 0);

            const totalExpenses = transactions
                .filter(t => t.type === 'expense')
                .reduce((sum, t) => sum + t.amount, 0);

            const netBalance = totalIncome - totalExpenses;

            totalIncomeEl.textContent = `$${totalIncome.toFixed(2)}`;
            totalExpensesEl.textContent = `$${totalExpenses.toFixed(2)}`;
            netBalanceEl.textContent = `$${netBalance.toFixed(2)}`;
        }

        // Event listeners for filters
        [accountFilter, categoryFilter, startDateFilter, endDateFilter].forEach(el => {
            el.addEventListener('change', applyTransactionFilters);
        });

        // Initial setup
        populateAccountFilter();
    }

    // Initial fetches
    fetchAccounts();
    fetchBudgets();
    fetchTransactions();
    fetchInvestments();

    setupCSVUpload();
    setupTransactionFilters();

    // Real-time Stock Tracking
    function setupRealTimeStockTracking() {
        const investmentTable = document.getElementById('investments-table');
        if (!investmentTable) return;

        // Fetch and update stock prices periodically
        function updateStockPrices() {
            const stockSymbols = Array.from(
                investmentTable.querySelectorAll('.stock-symbol')
            ).map(el => el.textContent.trim());

            if (stockSymbols.length === 0) return;

            fetch('/apps/finance_tracker/stocks/prices', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify({ symbols: stockSymbols })
            })
            .then(response => response.json())
            .then(stockPrices => {
                stockSymbols.forEach(symbol => {
                    const priceData = stockPrices[symbol];
                    const symbolRows = investmentTable.querySelectorAll(
                        `.stock-symbol:contains("${symbol}")`
                    ).closest('tr');

                    symbolRows.forEach(row => {
                        // Update current price
                        const currentPriceCell = row.querySelector('.current-price');
                        if (currentPriceCell && priceData && !priceData.error) {
                            currentPriceCell.textContent = `$${priceData.price.toFixed(2)}`;
                            
                            // Update gain/loss
                            const purchasePrice = parseFloat(
                                row.querySelector('.purchase-price').textContent.replace('$', '')
                            );
                            const quantity = parseFloat(
                                row.querySelector('.quantity').textContent
                            );

                            const totalPurchaseCost = purchasePrice * quantity;
                            const currentValue = priceData.price * quantity;
                            const gainLoss = currentValue - totalPurchaseCost;
                            const gainLossPercentage = (gainLoss / totalPurchaseCost) * 100;

                            const gainLossCell = row.querySelector('.gain-loss');
                            gainLossCell.textContent = `$${gainLoss.toFixed(2)} (${gainLossPercentage.toFixed(2)}%)`;
                            
                            // Color code gain/loss
                            gainLossCell.classList.remove('positive', 'negative');
                            gainLossCell.classList.add(
                                gainLoss >= 0 ? 'positive' : 'negative'
                            );
                        } else if (priceData && priceData.error) {
                            // Handle price fetch error
                            currentPriceCell.textContent = 'Error';
                            currentPriceCell.classList.add('error');
                        }
                    });
                });
            })
            .catch(error => {
                console.error('Error updating stock prices:', error);
            });
        }

        // Initial update
        updateStockPrices();

        // Update every 5 minutes
        setInterval(updateStockPrices, 5 * 60 * 1000);

        // Add real-time price refresh button
        const refreshButton = document.createElement('button');
        refreshButton.textContent = 'Refresh Prices';
        refreshButton.classList.add('stock-refresh-btn');
        refreshButton.addEventListener('click', updateStockPrices);

        const investmentSection = document.getElementById('investments-section');
        if (investmentSection) {
            investmentSection.insertBefore(
                refreshButton, 
                investmentSection.querySelector('.investments-content')
            );
        }
    }

    setupRealTimeStockTracking();

    // Report Generation and Export Functionality
    const reportButtons = {
        'generate-financial-overview-report': generateFinancialOverviewReport,
        'generate-trend-analysis-report': generateTrendAnalysisReport,
        'generate-investment-report': generateInvestmentPerformanceReport,
        'generate-tax-projection-report': generateTaxProjectionReport
    };

    // Attach event listeners to report generation buttons
    Object.keys(reportButtons).forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', reportButtons[buttonId]);
        }
    });

    // Export report button
    const exportReportBtn = document.getElementById('export-report-btn');
    if (exportReportBtn) {
        exportReportBtn.addEventListener('click', exportCurrentReport);
    }

    // Current generated report data (to be used for export)
    let currentReportData = null;
    let currentReportType = null;

    function generateFinancialOverviewReport() {
        fetch('/apps/finance_tracker/reports/financial-overview', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            }
        })
        .then(response => response.json())
        .then(data => {
            currentReportData = data;
            currentReportType = 'financial_overview';
            displayReport(data, 'Financial Overview Report');
            showExportOptions();
        })
        .catch(error => {
            console.error('Error generating financial overview report:', error);
            showErrorMessage('Failed to generate financial overview report');
        });
    }

    function generateTrendAnalysisReport() {
        fetch('/apps/finance_tracker/reports/trend-analysis', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            }
        })
        .then(response => response.json())
        .then(data => {
            currentReportData = data;
            currentReportType = 'trend_analysis';
            displayReport(data, 'Trend Analysis Report');
            showExportOptions();
        })
        .catch(error => {
            console.error('Error generating trend analysis report:', error);
            showErrorMessage('Failed to generate trend analysis report');
        });
    }

    function generateInvestmentPerformanceReport() {
        fetch('/apps/finance_tracker/reports/investment-performance', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            }
        })
        .then(response => response.json())
        .then(data => {
            currentReportData = data;
            currentReportType = 'investment_performance';
            displayReport(data, 'Investment Performance Report');
            showExportOptions();
        })
        .catch(error => {
            console.error('Error generating investment performance report:', error);
            showErrorMessage('Failed to generate investment performance report');
        });
    }

    function generateTaxProjectionReport() {
        fetch('/apps/finance_tracker/reports/tax-projection', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            }
        })
        .then(response => response.json())
        .then(data => {
            currentReportData = data;
            currentReportType = 'tax_projection';
            displayReport(data, 'Tax Projection Report');
            showExportOptions();
        })
        .catch(error => {
            console.error('Error generating tax projection report:', error);
            showErrorMessage('Failed to generate tax projection report');
        });
    }

    function displayReport(reportData, title) {
        const reportContainer = document.getElementById('generated-report-container');
        reportContainer.innerHTML = ''; // Clear previous report

        const reportTitle = document.createElement('h3');
        reportTitle.textContent = title;
        reportContainer.appendChild(reportTitle);

        // Create a formatted display of the report data
        const reportTable = document.createElement('table');
        reportTable.classList.add('report-table');

        // Dynamically generate table based on report type
        Object.entries(reportData).forEach(([key, value]) => {
            if (typeof value === 'object') {
                const sectionTitle = document.createElement('h4');
                sectionTitle.textContent = key.charAt(0).toUpperCase() + key.slice(1);
                reportContainer.appendChild(sectionTitle);

                const sectionTable = document.createElement('table');
                sectionTable.classList.add('report-section-table');

                Object.entries(value).forEach(([subKey, subValue]) => {
                    const row = sectionTable.insertRow();
                    const keyCell = row.insertCell(0);
                    const valueCell = row.insertCell(1);
                    keyCell.textContent = subKey;
                    valueCell.textContent = JSON.stringify(subValue);
                });

                reportContainer.appendChild(sectionTable);
            } else {
                const row = reportTable.insertRow();
                const keyCell = row.insertCell(0);
                const valueCell = row.insertCell(1);
                keyCell.textContent = key;
                valueCell.textContent = value;
            }
        });

        reportContainer.appendChild(reportTable);
    }

    function showExportOptions() {
        const exportOptionsContainer = document.getElementById('report-export-options');
        exportOptionsContainer.classList.remove('hidden');
    }

    function exportCurrentReport() {
        if (!currentReportData || !currentReportType) {
            showErrorMessage('No report to export');
            return;
        }

        const exportFormat = document.getElementById('report-export-format').value;

        fetch('/apps/finance_tracker/reports/export', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'requesttoken': OC.requestToken
            },
            body: JSON.stringify({
                reportType: currentReportType,
                reportData: currentReportData,
                format: exportFormat
            })
        })
        .then(response => response.blob())
        .then(blob => {
            // Create a download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `finance_report_${currentReportType}_${new Date().toISOString().split('T')[0]}.${exportFormat}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error exporting report:', error);
            showErrorMessage('Failed to export report');
        });
    }

    function showErrorMessage(message) {
        const errorContainer = document.createElement('div');
        errorContainer.classList.add('error-message');
        errorContainer.textContent = message;
        
        const reportContainer = document.getElementById('generated-report-container');
        reportContainer.innerHTML = ''; // Clear previous content
        reportContainer.appendChild(errorContainer);
    }

    // Transaction Search Functionality
    function setupTransactionSearch() {
        const searchInput = document.getElementById('transactions-search-input');
        const searchFilter = document.getElementById('transactions-search-filter');
        const transactionsTable = document.getElementById('transactions-table');
        const transactionsTableBody = document.getElementById('transactions-table-body');
        const noTransactionsFound = document.getElementById('no-transactions-found');

        if (!searchInput || !searchFilter || !transactionsTable) return;

        // Debounce search to improve performance
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        searchFilter.addEventListener('change', performSearch);

        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const searchField = searchFilter.value;
            let visibleRowCount = 0;

            // Get all transaction rows
            const rows = transactionsTableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const rowVisible = matchesSearch(row, searchTerm, searchField);
                
                row.style.display = rowVisible ? '' : 'none';
                
                if (rowVisible) {
                    visibleRowCount++;
                }
            });

            // Show/hide no results message
            noTransactionsFound.classList.toggle('hidden', visibleRowCount > 0);
        }

        function matchesSearch(row, searchTerm, searchField) {
            // If no search term, show all rows
            if (!searchTerm) return true;

            // Get cell values based on search field
            const cells = {
                'description': row.querySelector('td:nth-child(2)'),
                'category': row.querySelector('td:nth-child(3)'),
                'amount': row.querySelector('td:nth-child(4)'),
                'date': row.querySelector('td:nth-child(1)')
            };

            // Search logic
            if (searchField === 'all') {
                // Search across all fields
                return Array.from(row.querySelectorAll('td'))
                    .some(cell => 
                        cell.textContent.toLowerCase().includes(searchTerm)
                    );
            } else if (cells[searchField]) {
                // Search specific field
                return cells[searchField].textContent.toLowerCase().includes(searchTerm);
            }

            return false;
        }
    }

    setupTransactionSearch();

    // Stock Search and Details Functionality
    function setupStockTracking() {
        const stockSearchInput = document.getElementById('stock-search-input');
        const stockSearchBtn = document.getElementById('stock-search-btn');
        const stockSearchResults = document.getElementById('stock-search-results');
        
        // Add click handler for search button
        stockSearchBtn.addEventListener('click', () => {
            performStockSearch(stockSearchInput.value);
        });
        
        // Keep the existing input handler for real-time search
        stockSearchInput.addEventListener('input', debounce(async (e) => {
            if (e.target.value.length >= 2) {
                performStockSearch(e.target.value);
            }
        }, 300));

        async function performStockSearch(query) {
            if (!query.trim()) {
                showNotification(t('finance_tracker', 'Please enter a search term'), 'info');
                return;
            }
            
            try {
                const response = await fetch(OC.generateUrl('/apps/finance_tracker/api/stocks/search'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'requesttoken': OC.requestToken
                    },
                    body: JSON.stringify({ query: query.trim() })
                });
                
                const data = await response.json();
                displayStockSearchResults(data);
            } catch (error) {
                console.error('Stock search error:', error);
                showNotification(t('finance_tracker', 'Failed to search stocks'), 'error');
            }
        }

        function displayStockSearchResults(stocks) {
            const resultsTable = document.getElementById('stock-search-results-table');
            resultsTable.innerHTML = '';
            
            stocks.forEach(stock => {
                const row = resultsTable.insertRow();
                row.innerHTML = `
                    <td>${stock.symbol}</td>
                    <td>${stock.name}</td>
                    <td class="price">$${formatNumber(stock.price)}</td>
                    <td class="change ${stock.change >= 0 ? 'positive' : 'negative'}">
                        <span class="change-indicator">
                            ${stock.change >= 0 ? '▲' : '▼'}
                        </span>
                        ${formatNumber(stock.change)}%
                    </td>
                    <td class="performance">
                        <div class="performance-metrics">
                            <div class="metric">
                                <span class="label">1d:</span>
                                <span class="${stock.performance.day > 0 ? 'positive' : 'negative'}">
                                    ${formatNumber(stock.performance.day)}%
                                </span>
                            </div>
                            <div class="metric">
                                <span class="label">1m:</span>
                                <span class="${stock.performance.month > 0 ? 'positive' : 'negative'}">
                                    ${formatNumber(stock.performance.month)}%
                                </span>
                            </div>
                            <div class="metric">
                                <span class="label">YTD:</span>
                                <span class="${stock.performance.ytd > 0 ? 'positive' : 'negative'}">
                                    ${formatNumber(stock.performance.ytd)}%
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>${formatVolume(stock.volume)}</td>
                    <td>
                        <button class="primary small add-stock-btn" 
                                data-symbol="${stock.symbol}" 
                                data-price="${stock.price}">
                            ${t('finance_tracker', 'Add')}
                        </button>
                    </td>
                `;
            });
            
            stockSearchResults.classList.remove('hidden');
        }
    }

    setupStockTracking();

    // Dashboard Functionality
    function setupDashboard() {
        const dashboardSection = document.getElementById('dashboard-section');
        const addTransactionQuickBtn = document.getElementById('add-transaction-quick-btn');
        const addInvestmentQuickBtn = document.getElementById('add-investment-quick-btn');

        // Quick action buttons
        addTransactionQuickBtn.addEventListener('click', () => {
            const transactionModal = document.getElementById('transaction-modal');
            transactionModal.classList.remove('hidden');
        });

        addInvestmentQuickBtn.addEventListener('click', () => {
            const investmentModal = document.getElementById('investment-modal');
            investmentModal.classList.remove('hidden');
        });

        // Fetch dashboard data
        function fetchDashboardData() {
            fetch('/apps/finance_tracker/dashboard/data', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update overview cards
                updateOverviewCards(data.overview);
                
                // Update charts
                updateIncomeExpensesChart(data.incomeExpensesData);
                updateSpendingCategoriesChart(data.spendingCategoriesData);
                
                // Update recent activities
                updateRecentTransactions(data.recentTransactions);
                updateRecentInvestments(data.recentInvestments);
            })
            .catch(error => {
                console.error('Dashboard data fetch error:', error);
            });
        }

        function updateOverviewCards(overview) {
            // Total Balance
            const totalBalanceAmount = document.getElementById('total-balance-amount');
            const balanceTrendIcon = document.getElementById('balance-trend-icon');
            const balanceTrendPercentage = document.getElementById('balance-trend-percentage');
            
            totalBalanceAmount.textContent = `$${overview.totalBalance.toFixed(2)}`;
            balanceTrendIcon.classList.toggle('positive', overview.balanceTrend >= 0);
            balanceTrendIcon.classList.toggle('negative', overview.balanceTrend < 0);
            balanceTrendPercentage.textContent = `${Math.abs(overview.balanceTrend).toFixed(2)}%`;

            // Total Income
            const totalIncomeAmount = document.getElementById('total-income-amount');
            const incomeTrendIcon = document.getElementById('income-trend-icon');
            const incomeTrendPercentage = document.getElementById('income-trend-percentage');
            
            totalIncomeAmount.textContent = `$${overview.totalIncome.toFixed(2)}`;
            incomeTrendIcon.classList.toggle('positive', overview.incomeTrend >= 0);
            incomeTrendIcon.classList.toggle('negative', overview.incomeTrend < 0);
            incomeTrendPercentage.textContent = `${Math.abs(overview.incomeTrend).toFixed(2)}%`;

            // Total Expenses
            const totalExpensesAmount = document.getElementById('total-expenses-amount');
            const expensesTrendIcon = document.getElementById('expenses-trend-icon');
            const expensesTrendPercentage = document.getElementById('expenses-trend-percentage');
            
            totalExpensesAmount.textContent = `$${overview.totalExpenses.toFixed(2)}`;
            expensesTrendIcon.classList.toggle('positive', overview.expensesTrend >= 0);
            expensesTrendIcon.classList.toggle('negative', overview.expensesTrend < 0);
            expensesTrendPercentage.textContent = `${Math.abs(overview.expensesTrend).toFixed(2)}%`;

            // Total Investments
            const totalInvestmentsValue = document.getElementById('total-investments-value');
            const investmentsTrendIcon = document.getElementById('investments-trend-icon');
            const investmentsTrendPercentage = document.getElementById('investments-trend-percentage');
            
            totalInvestmentsValue.textContent = `$${overview.totalInvestments.toFixed(2)}`;
            investmentsTrendIcon.classList.toggle('positive', overview.investmentsTrend >= 0);
            investmentsTrendIcon.classList.toggle('negative', overview.investmentsTrend < 0);
            investmentsTrendPercentage.textContent = `${Math.abs(overview.investmentsTrend).toFixed(2)}%`;
        }

        function updateIncomeExpensesChart(chartData) {
            const ctx = document.getElementById('income-expenses-chart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Income',
                            data: chartData.incomeData,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: chartData.expensesData,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount ($)'
                            }
                        }
                    }
                }
            });
        }

        function updateSpendingCategoriesChart(chartData) {
            const ctx = document.getElementById('spending-categories-chart').getContext('2d');
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData.categories,
                    datasets: [{
                        data: chartData.amounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }

        function updateRecentTransactions(transactions) {
            const transactionsBody = document.getElementById('dashboard-recent-transactions-body');
            transactionsBody.innerHTML = '';

            transactions.forEach(transaction => {
                const row = transactionsBody.insertRow();
                
                const dateCell = row.insertCell(0);
                dateCell.textContent = transaction.date;
                
                const descriptionCell = row.insertCell(1);
                descriptionCell.textContent = transaction.description;
                
                const categoryCell = row.insertCell(2);
                categoryCell.textContent = transaction.category;
                
                const amountCell = row.insertCell(3);
                amountCell.textContent = `$${transaction.amount.toFixed(2)}`;
                amountCell.classList.add(transaction.type === 'income' ? 'positive' : 'negative');
            });
        }

        function updateRecentInvestments(investments) {
            const investmentsBody = document.getElementById('dashboard-recent-investments-body');
            investmentsBody.innerHTML = '';

            investments.forEach(investment => {
                const row = investmentsBody.insertRow();
                
                const symbolCell = row.insertCell(0);
                symbolCell.textContent = investment.symbol;
                
                const nameCell = row.insertCell(1);
                nameCell.textContent = investment.name;
                
                const quantityCell = row.insertCell(2);
                quantityCell.textContent = investment.quantity;
                
                const valueCell = row.insertCell(3);
                valueCell.textContent = `$${investment.currentValue.toFixed(2)}`;
            });
        }

        // Initial dashboard data fetch
        fetchDashboardData();

        // Periodic refresh (every 5 minutes)
        setInterval(fetchDashboardData, 5 * 60 * 1000);
    }

    setupDashboard();

    // Add real-time updates for existing investments
    function setupRealTimeUpdates() {
        const investmentsTable = document.getElementById('investments-table-body');
        if (!investmentsTable) return;

        // Update prices every minute
        setInterval(async () => {
            const investments = Array.from(investmentsTable.querySelectorAll('tr')).map(row => ({
                symbol: row.dataset.symbol,
                shares: parseFloat(row.dataset.shares)
            }));

            if (investments.length === 0) return;

            try {
                const response = await fetch(OC.generateUrl('/apps/finance_tracker/api/stocks/prices'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'requesttoken': OC.requestToken
                    },
                    body: JSON.stringify({ symbols: investments.map(inv => inv.symbol) })
                });
                
                const prices = await response.json();
                updateInvestmentPrices(investments, prices);
            } catch (error) {
                console.error('Price update error:', error);
            }
        }, 60000); // Update every minute
    }

    function updateInvestmentPrices(investments, prices) {
        investments.forEach(investment => {
            const row = document.querySelector(`tr[data-symbol="${investment.symbol}"]`);
            if (!row || !prices[investment.symbol]) return;

            const currentPrice = prices[investment.symbol].price;
            const purchasePrice = parseFloat(row.dataset.purchasePrice);
            const shares = investment.shares;

            // Calculate values
            const totalValue = currentPrice * shares;
            const totalReturn = totalValue - (purchasePrice * shares);
            const returnPercent = ((currentPrice - purchasePrice) / purchasePrice) * 100;
            
            // Update row with new values and indicators
            row.innerHTML = `
                <td>${investment.symbol}</td>
                <td>${investment.name}</td>
                <td>${formatNumber(shares)}</td>
                <td>$${formatNumber(purchasePrice)}</td>
                <td class="current-price">$${formatNumber(currentPrice)}</td>
                <td class="daily-change ${prices[investment.symbol].change >= 0 ? 'positive' : 'negative'}">
                    <span class="change-indicator">
                        ${prices[investment.symbol].change >= 0 ? '▲' : '▼'}
                    </span>
                    ${formatNumber(prices[investment.symbol].change)}%
                </td>
                <td class="total-value">$${formatNumber(totalValue)}</td>
                <td class="total-return ${totalReturn >= 0 ? 'positive' : 'negative'}">
                    $${formatNumber(totalReturn)}
                    <span class="return-percent">(${formatNumber(returnPercent)}%)</span>
                </td>
                <td class="performance-metrics">
                    <div class="metric">
                        <span class="label">ROI:</span>
                        <span class="${returnPercent >= 0 ? 'positive' : 'negative'}">
                            ${formatNumber(returnPercent)}%
                        </span>
                    </div>
                    <div class="metric">
                        <span class="label">Beta:</span>
                        <span>${formatNumber(prices[investment.symbol].beta)}</span>
                    </div>
                    <div class="metric">
                        <span class="label">52w:</span>
                        <span class="${prices[investment.symbol].yearChange >= 0 ? 'positive' : 'negative'}">
                            ${formatNumber(prices[investment.symbol].yearChange)}%
                        </span>
                    </div>
                </td>
            `;
        });
    }

    // Helper functions
    function formatNumber(number) {
        return Number(number).toFixed(2);
    }

    function formatVolume(volume) {
        if (volume >= 1000000) {
            return (volume / 1000000).toFixed(2) + 'M';
        } else if (volume >= 1000) {
            return (volume / 1000).toFixed(2) + 'K';
        }
        return volume;
    }

    // Helper function to debounce API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize stock functionality
    setupStockTracking();
    setupRealTimeUpdates();

    // Budget Section Functionality
    setupBudgetSection();

    // Initialize sample data
    loadSampleData();

    setupDeleteHandlers();
});

function initNavigation() {
    // Get all navigation items with data-section attribute
    const navItems = document.querySelectorAll('.app-navigation-entry-link[data-section]');
    
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active class from all navigation items
            navItems.forEach(navItem => {
                navItem.parentElement.classList.remove('active');
            });
            
            // Add active class to clicked item
            item.parentElement.classList.add('active');
            
            // Get the section to show from data-section attribute
            const sectionId = item.getAttribute('data-section');
            showSection(sectionId);
        });
    });
}

function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.finance-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    // Show the selected section
    const selectedSection = document.getElementById(`${sectionId}-section`);
    if (selectedSection) {
        selectedSection.style.display = 'block';
        
        // Load section specific data if needed
        switch(sectionId) {
            case 'dashboard':
                loadDashboardData();
                break;
            case 'accounts':
                loadAccountsData();
                break;
            case 'transactions':
                loadTransactionsData();
                break;
            case 'investments':
                loadInvestmentsData();
                break;
            case 'budget':
                loadBudgetData();
                break;
            case 'reports':
                loadReportsData();
                break;
        }
    }
}

// Data loading functions (to be implemented based on your backend API)
function loadDashboardData() {
    // Example implementation
    const accountsSummary = document.getElementById('dashboard-accounts-summary');
    const recentTransactions = document.getElementById('dashboard-recent-transactions');
    const budgetOverview = document.getElementById('dashboard-budget-overview');
    
    // Show loading state
    accountsSummary.innerHTML = '<div class="loading-indicator"><span class="icon-loading"></span></div>';
    
    // Make API call to fetch dashboard data
    // Replace with your actual API endpoint
    fetch(OC.generateUrl('/apps/finance_tracker/api/dashboard'))
        .then(response => response.json())
        .then(data => {
            // Update dashboard sections with received data
            updateDashboardUI(data);
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
            // Show error state
            accountsSummary.innerHTML = '<div class="empty-content"><div class="icon-error"></div><h2>Error loading dashboard data</h2></div>';
        });
}

function loadAccountsData() {
    const accountsList = document.querySelector('.accounts-list');
    accountsList.innerHTML = '<div class="loading-indicator"><span class="icon-loading"></span></div>';
    
    // Implement accounts data loading
}

function loadTransactionsData() {
    const transactionsTable = document.getElementById('transactions-table-body');
    transactionsTable.innerHTML = '<tr><td colspan="6"><div class="loading-indicator"><span class="icon-loading"></span></div></td></tr>';
    
    // Implement transactions data loading
}

function loadInvestmentsData() {
    const investmentsTable = document.getElementById('investments-table-body');
    investmentsTable.innerHTML = '<tr><td colspan="8"><div class="loading-indicator"><span class="icon-loading"></span></div></td></tr>';
    
    // Implement investments data loading
}

function loadBudgetData() {
    const budgetsList = document.querySelector('.budgets-list');
    budgetsList.innerHTML = '<div class="loading-indicator"><span class="icon-loading"></span></div>';
    
    // Implement budget data loading
}

function loadReportsData() {
    const reportsContainer = document.getElementById('generated-report-container');
    reportsContainer.innerHTML = '<div class="loading-indicator"><span class="icon-loading"></span></div>';
    
    // Implement reports data loading
}

// Helper function to update dashboard UI
function updateDashboardUI(data) {
    // Example implementation
    const accountsSummary = document.getElementById('dashboard-accounts-summary');
    const recentTransactions = document.getElementById('dashboard-recent-transactions');
    const budgetOverview = document.getElementById('dashboard-budget-overview');
    
    // Update accounts summary
    accountsSummary.innerHTML = `
        <div class="summary-card">
            <h4>${t('finance_tracker', 'Total Balance')}</h4>
            <div class="amount">${formatCurrency(data.totalBalance)}</div>
        </div>
    `;
    
    // Update other dashboard sections...
}

// Helper function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function setupBudgetSection() {
    const budgetPeriodSelect = document.getElementById('budget-period');
    const budgetMonthInput = document.getElementById('budget-month');
    const budgetYearInput = document.getElementById('budget-year');
    const addBudgetBtn = document.getElementById('add-budget-btn');
    const budgetModal = document.getElementById('budget-modal');
    
    // Period selector handling
    budgetPeriodSelect.addEventListener('change', (e) => {
        const isPeriodMonthly = e.target.value === 'monthly';
        budgetMonthInput.style.display = isPeriodMonthly ? 'block' : 'none';
        budgetYearInput.style.display = isPeriodMonthly ? 'none' : 'block';
        loadBudgetData();
    });

    // Date change handlers
    budgetMonthInput.addEventListener('change', loadBudgetData);
    budgetYearInput.addEventListener('change', loadBudgetData);

    // Add budget button
    addBudgetBtn.addEventListener('click', () => {
        budgetModal.classList.remove('hidden');
        document.getElementById('modal-overlay').classList.remove('hidden');
    });

    // Budget form handling
    const budgetForm = document.getElementById('budget-form');
    budgetForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            category: document.getElementById('budget-category').value,
            amount: parseFloat(document.getElementById('budget-amount').value),
            alertThreshold: parseInt(document.getElementById('budget-alert-threshold').value),
            notes: document.getElementById('budget-notes').value,
            period: budgetPeriodSelect.value,
            date: budgetPeriodSelect.value === 'monthly' ? 
                  budgetMonthInput.value : 
                  budgetYearInput.value
        };

        try {
            const response = await fetch(OC.generateUrl('/apps/finance_tracker/api/budgets'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                budgetModal.classList.add('hidden');
                document.getElementById('modal-overlay').classList.add('hidden');
                budgetForm.reset();
                loadBudgetData();
                showNotification(t('finance_tracker', 'Budget created successfully'), 'success');
            } else {
                throw new Error('Failed to create budget');
            }
        } catch (error) {
            console.error('Budget creation error:', error);
            showNotification(t('finance_tracker', 'Failed to create budget'), 'error');
        }
    });

    async function loadBudgetData() {
        const period = budgetPeriodSelect.value;
        const date = period === 'monthly' ? budgetMonthInput.value : budgetYearInput.value;
        
        try {
            const response = await fetch(
                OC.generateUrl(`/apps/finance_tracker/api/budgets/${period}/${date}`),
                {
                    headers: {
                        'requesttoken': OC.requestToken
                    }
                }
            );
            
            const data = await response.json();
            updateBudgetUI(data);
        } catch (error) {
            console.error('Budget data loading error:', error);
            showNotification(t('finance_tracker', 'Failed to load budget data'), 'error');
        }
    }

    function updateBudgetUI(data) {
        // Update summary stats
        document.getElementById('total-budget').textContent = formatCurrency(data.totalBudget);
        document.getElementById('total-spent').textContent = formatCurrency(data.totalSpent);
        document.getElementById('total-remaining').textContent = formatCurrency(data.totalRemaining);

        // Update progress bar
        const progressPercent = (data.totalSpent / data.totalBudget) * 100;
        const progressBar = document.querySelector('.progress');
        const progressText = document.querySelector('.progress-text');
        
        progressBar.style.width = `${Math.min(progressPercent, 100)}%`;
        progressBar.className = `progress ${progressPercent > 80 ? 'warning' : ''}`;
        progressText.textContent = `${progressPercent.toFixed(1)}% used`;

        // Update category grid
        const categoriesGrid = document.getElementById('budget-categories-grid');
        categoriesGrid.innerHTML = '';

        data.categories.forEach(category => {
            const categoryCard = createCategoryCard(category);
            categoriesGrid.appendChild(categoryCard);
        });

        // Update alerts
        updateBudgetAlerts(data.alerts);

        // Update goals section
        updateGoalsProgress(data.goals);
    }

    function createCategoryCard(category) {
        const card = document.createElement('div');
        card.className = 'category-card';
        
        const spentPercent = (category.spent / category.budget) * 100;
        const isOverBudget = spentPercent > 100;
        const isNearLimit = spentPercent > 80;

        card.innerHTML = `
            <div class="category-header ${isOverBudget ? 'over-budget' : isNearLimit ? 'near-limit' : ''}">
                <h4>${category.name}</h4>
                <div class="category-actions">
                    <button class="edit-category" data-id="${category.id}">
                        <span class="icon-rename"></span>
                    </button>
                    <button class="delete-category" data-id="${category.id}">
                        <span class="icon-delete"></span>
                    </button>
                </div>
            </div>
            <div class="category-stats">
                <div class="stat">
                    <span class="label">${t('finance_tracker', 'Budget')}</span>
                    <span class="value">${formatCurrency(category.budget)}</span>
                </div>
                <div class="stat">
                    <span class="label">${t('finance_tracker', 'Spent')}</span>
                    <span class="value">${formatCurrency(category.spent)}</span>
                </div>
                <div class="stat">
                    <span class="label">${t('finance_tracker', 'Remaining')}</span>
                    <span class="value ${isOverBudget ? 'negative' : 'positive'}">
                        ${formatCurrency(category.budget - category.spent)}
                    </span>
                </div>
            </div>
            <div class="category-progress">
                <div class="progress-bar">
                    <div class="progress ${isOverBudget ? 'over-budget' : isNearLimit ? 'warning' : ''}" 
                         style="width: ${Math.min(spentPercent, 100)}%">
                    </div>
                </div>
                <span class="progress-text">${spentPercent.toFixed(1)}%</span>
            </div>
        `;

        return card;
    }

    function updateBudgetAlerts(alerts) {
        const alertsList = document.getElementById('budget-alerts-list');
        alertsList.innerHTML = '';

        alerts.forEach(alert => {
            const alertElement = document.createElement('div');
            alertElement.className = `budget-alert ${alert.severity}`;
            alertElement.innerHTML = `
                <span class="alert-icon"></span>
                <span class="alert-message">${alert.message}</span>
                <span class="alert-date">${formatDate(alert.date)}</span>
            `;
            alertsList.appendChild(alertElement);
        });
    }

    function updateGoalsProgress(goals) {
        const goalsContainer = document.getElementById('budget-goals');
        goalsContainer.innerHTML = '';

        goals.forEach(goal => {
            const goalCard = document.createElement('div');
            goalCard.className = 'goal-card';
            
            const progress = (goal.currentAmount / goal.targetAmount) * 100;
            const remainingDays = Math.ceil((new Date(goal.targetDate) - new Date()) / (1000 * 60 * 60 * 24));
            
            goalCard.innerHTML = `
                <div class="goal-header">
                    <h4>${goal.type === 'saving' ? t('finance_tracker', 'Saving Goal') : t('finance_tracker', 'Spending Limit')}</h4>
                    <span class="goal-date">${t('finance_tracker', '{days} days remaining', {days: remainingDays})}</span>
                </div>
                <div class="goal-amount">
                    <span class="current">${formatCurrency(goal.currentAmount)}</span>
                    <span class="separator">/</span>
                    <span class="target">${formatCurrency(goal.targetAmount)}</span>
                </div>
                <div class="goal-progress">
                    <div class="progress-bar">
                        <div class="progress" style="width: ${progress}%"></div>
                    </div>
                    <span class="progress-text">${progress.toFixed(1)}%</span>
                </div>
                <div class="goal-stats">
                    <div class="stat">
                        <span class="label">${t('finance_tracker', 'Remaining')}</span>
                        <span class="value">${formatCurrency(goal.targetAmount - goal.currentAmount)}</span>
                    </div>
                    <div class="stat">
                        <span class="label">${t('finance_tracker', 'Daily Target')}</span>
                        <span class="value">${formatCurrency((goal.targetAmount - goal.currentAmount) / remainingDays)}</span>
                    </div>
                </div>
            `;
            
            goalsContainer.appendChild(goalCard);
        });

        // Update dashboard if we're on the dashboard
        const dashboardGoals = document.getElementById('dashboard-goals');
        if (dashboardGoals) {
            dashboardGoals.innerHTML = goalsContainer.innerHTML;
        }
    }

    // Initial load
    loadBudgetData();
}

function loadSampleData() {
    // Simulate API responses with sample data
    window.mockApi = {
        getAccounts: () => Promise.resolve(sampleData.accounts),
        getTransactions: () => Promise.resolve(sampleData.transactions),
        getInvestments: () => Promise.resolve(sampleData.investments),
        getBudgets: () => Promise.resolve(sampleData.budgets)
    };
}

async function loadAccountsData() {
    const accountsList = document.querySelector('.accounts-list');
    try {
        const accounts = await window.mockApi.getAccounts();
        displayAccounts(accounts);
    } catch (error) {
        console.error('Error loading accounts:', error);
        showNotification(t('finance_tracker', 'Failed to load accounts'), 'error');
    }
}

async function loadTransactionsData() {
    const transactionsTable = document.getElementById('transactions-table-body');
    try {
        const transactions = await window.mockApi.getTransactions();
        displayTransactions(transactions);
    } catch (error) {
        console.error('Error loading transactions:', error);
        showNotification(t('finance_tracker', 'Failed to load transactions'), 'error');
    }
}

async function loadBudgetData() {
    try {
        const budgetData = await window.mockApi.getBudgets();
        updateBudgetUI(budgetData);
    } catch (error) {
        console.error('Error loading budget data:', error);
        showNotification(t('finance_tracker', 'Failed to load budget data'), 'error');
    }
}

function setupDeleteHandlers() {
    const deleteConfirmationModal = document.getElementById('delete-confirmation-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    let currentDeleteCallback = null;

    function showDeleteConfirmation(message, onConfirm) {
        document.getElementById('delete-confirmation-message').textContent = message;
        deleteConfirmationModal.classList.remove('hidden');
        document.getElementById('modal-overlay').classList.remove('hidden');
        currentDeleteCallback = onConfirm;
    }

    confirmDeleteBtn.addEventListener('click', () => {
        if (currentDeleteCallback) {
            currentDeleteCallback();
        }
        deleteConfirmationModal.classList.add('hidden');
        document.getElementById('modal-overlay').classList.add('hidden');
    });

    cancelDeleteBtn.addEventListener('click', () => {
        deleteConfirmationModal.classList.add('hidden');
        document.getElementById('modal-overlay').classList.add('hidden');
    });

    // Account deletion
    document.querySelector('.accounts-list').addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.delete-account-btn');
        if (deleteBtn) {
            const accountId = deleteBtn.dataset.id;
            const accountName = deleteBtn.dataset.name;
            
            showDeleteConfirmation(
                t('finance_tracker', 'Are you sure you want to delete the account "{name}"?', {name: accountName}),
                async () => {
                    try {
                        await deleteAccount(accountId);
                        loadAccountsData();
                        showNotification(t('finance_tracker', 'Account deleted successfully'), 'success');
                    } catch (error) {
                        showNotification(t('finance_tracker', 'Failed to delete account'), 'error');
                    }
                }
            );
        }
    });

    // Transaction deletion
    document.getElementById('transactions-table-body').addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.delete-transaction-btn');
        if (deleteBtn) {
            const transactionId = deleteBtn.dataset.id;
            
            showDeleteConfirmation(
                t('finance_tracker', 'Are you sure you want to delete this transaction?'),
                async () => {
                    try {
                        await deleteTransaction(transactionId);
                        loadTransactionsData();
                        showNotification(t('finance_tracker', 'Transaction deleted successfully'), 'success');
                    } catch (error) {
                        showNotification(t('finance_tracker', 'Failed to delete transaction'), 'error');
                    }
                }
            );
        }
    });

    // Investment deletion
    document.getElementById('investments-table-body').addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.delete-investment-btn');
        if (deleteBtn) {
            const symbol = deleteBtn.dataset.symbol;
            
            showDeleteConfirmation(
                t('finance_tracker', 'Are you sure you want to stop tracking {symbol}?', {symbol}),
                async () => {
                    try {
                        await deleteInvestment(symbol);
                        loadInvestmentsData();
                        showNotification(t('finance_tracker', 'Investment removed successfully'), 'success');
                    } catch (error) {
                        showNotification(t('finance_tracker', 'Failed to remove investment'), 'error');
                    }
                }
            );
        }
    });

    // Budget deletion
    document.getElementById('budget-categories-grid').addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.delete-category');
        if (deleteBtn) {
            const categoryId = deleteBtn.dataset.id;
            const categoryName = deleteBtn.closest('.category-card').querySelector('h4').textContent;
            
            showDeleteConfirmation(
                t('finance_tracker', 'Are you sure you want to delete the budget for "{name}"?', {name: categoryName}),
                async () => {
                    try {
                        await deleteBudget(categoryId);
                        loadBudgetData();
                        showNotification(t('finance_tracker', 'Budget deleted successfully'), 'success');
                    } catch (error) {
                        showNotification(t('finance_tracker', 'Failed to delete budget'), 'error');
                    }
                }
            );
        }
    });

    // Delete API functions
    async function deleteAccount(accountId) {
        // For sample data, just remove from the array
        sampleData.accounts = sampleData.accounts.filter(account => account.id !== parseInt(accountId));
        return Promise.resolve();
    }

    async function deleteTransaction(transactionId) {
        sampleData.transactions = sampleData.transactions.filter(transaction => transaction.id !== parseInt(transactionId));
        return Promise.resolve();
    }

    async function deleteInvestment(symbol) {
        sampleData.investments = sampleData.investments.filter(investment => investment.symbol !== symbol);
        return Promise.resolve();
    }

    async function deleteBudget(categoryId) {
        sampleData.budgets.categories = sampleData.budgets.categories.filter(category => category.id !== parseInt(categoryId));
        return Promise.resolve();
    }
}

function displayAccounts(accounts) {
    const accountsList = document.querySelector('.accounts-list');
    accountsList.innerHTML = accounts.map(account => `
        <div class="account-item" data-id="${account.id}">
            <div class="account-info">
                <h3>${account.name}</h3>
                <span class="account-type">${account.type}</span>
                <span class="account-balance">${formatCurrency(account.balance)}</span>
            </div>
            <div class="account-actions">
                <button class="edit-account-btn" data-id="${account.id}">
                    <span class="icon-rename"></span>
                </button>
                <button class="delete-account-btn" data-id="${account.id}" data-name="${account.name}">
                    <span class="icon-delete"></span>
                </button>
            </div>
        </div>
    `).join('');
}

function displayTransactions(transactions) {
    const tbody = document.getElementById('transactions-table-body');
    tbody.innerHTML = transactions.map(transaction => `
        <tr>
            <td>${transaction.date}</td>
            <td>${transaction.description}</td>
            <td>${transaction.category}</td>
            <td class="${transaction.type === 'expense' ? 'negative' : 'positive'}">
                ${formatCurrency(transaction.amount)}
            </td>
            <td>${transaction.type}</td>
            <td>
                <button class="edit-transaction-btn" data-id="${transaction.id}">
                    <span class="icon-rename"></span>
                </button>
                <button class="delete-transaction-btn" data-id="${transaction.id}">
                    <span class="icon-delete"></span>
                </button>
            </td>
        </tr>
    `).join('');
}
