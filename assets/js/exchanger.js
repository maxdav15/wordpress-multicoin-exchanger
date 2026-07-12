/**
 * Multi-Coin Exchanger JavaScript
 * Handles exchange functionality, API calls, and UI interactions
 */

class MultiCoinExchanger {
    constructor() {
        this.coins = [];
        this.exchangeRates = {};
        this.fromCoin = 'bitcoin';
        this.toCoin = 'ethereum';
        this.fee = 0.5; // 0.5% fee
        this.networkFee = 2.50;
        this.init();
    }

    // Initialize the exchanger
    async init() {
        await this.loadCoins();
        this.setupEventListeners();
        this.updateExchangeRate();
        
        // Update rates every 30 seconds
        setInterval(() => this.updateExchangeRate(), 30000);
    }

    // Load available coins from CoinGecko API
    async loadCoins() {
        try {
            const response = await fetch(
                'https://api.coingecko.com/api/v3/coins/list'
            );
            const data = await response.json();
            
            // Select popular coins
            const popularCoins = [
                { id: 'bitcoin', symbol: 'BTC', name: 'Bitcoin' },
                { id: 'ethereum', symbol: 'ETH', name: 'Ethereum' },
                { id: 'litecoin', symbol: 'LTC', name: 'Litecoin' },
                { id: 'ripple', symbol: 'XRP', name: 'Ripple' },
                { id: 'cardano', symbol: 'ADA', name: 'Cardano' },
                { id: 'solana', symbol: 'SOL', name: 'Solana' },
                { id: 'polkadot', symbol: 'DOT', name: 'Polkadot' },
                { id: 'dogecoin', symbol: 'DOGE', name: 'Dogecoin' },
                { id: 'polygon', symbol: 'MATIC', name: 'Polygon' },
                { id: 'chainlink', symbol: 'LINK', name: 'Chainlink' },
            ];
            
            this.coins = popularCoins;
            this.populateCurrencyLists();
        } catch (error) {
            console.error('Error loading coins:', error);
        }
    }

    // Populate currency dropdown lists
    populateCurrencyLists() {
        const fromList = document.getElementById('from-list');
        const toList = document.getElementById('to-list');
        
        fromList.innerHTML = '';
        toList.innerHTML = '';
        
        this.coins.forEach(coin => {
            const fromItem = this.createCurrencyItem(coin, 'from');
            const toItem = this.createCurrencyItem(coin, 'to');
            
            fromList.appendChild(fromItem);
            toList.appendChild(toItem);
        });
    }

    // Create currency item element
    createCurrencyItem(coin, target) {
        const div = document.createElement('div');
        div.className = 'currency-item';
        
        const icon = document.createElement('img');
        icon.className = 'currency-item-icon';
        icon.src = `https://api.coingecko.com/api/v3/coins/${coin.id}`;
        icon.onerror = () => {
            icon.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ccc"%3E%3Ccircle cx="12" cy="12" r="10"/%3E%3C/svg%3E';
        };
        
        const info = document.createElement('div');
        info.className = 'currency-item-info';
        
        const name = document.createElement('div');
        name.className = 'currency-item-name';
        name.textContent = coin.name;
        
        const symbol = document.createElement('div');
        symbol.className = 'currency-item-symbol';
        symbol.textContent = coin.symbol;
        
        info.appendChild(name);
        info.appendChild(symbol);
        
        div.appendChild(icon);
        div.appendChild(info);
        
        div.addEventListener('click', () => this.selectCurrency(coin, target));
        
        return div;
    }

    // Setup event listeners
    setupEventListeners() {
        // Currency button clicks
        document.getElementById('from-currency-btn').addEventListener('click', () => {
            this.toggleDropdown('from');
        });
        
        document.getElementById('to-currency-btn').addEventListener('click', () => {
            this.toggleDropdown('to');
        });
        
        // Amount input
        document.getElementById('from-amount').addEventListener('input', (e) => {
            this.calculateExchange();
        });
        
        // Swap button
        document.getElementById('swap-btn').addEventListener('click', () => {
            this.swapCurrencies();
        });
        
        // Exchange button
        document.getElementById('exchange-btn').addEventListener('click', () => {
            this.openTransactionModal();
        });
        
        // Modal buttons
        document.getElementById('cancel-btn').addEventListener('click', () => {
            this.closeModal('transaction-modal');
        });
        
        document.getElementById('confirm-btn').addEventListener('click', () => {
            this.confirmExchange();
        });
        
        document.getElementById('close-success').addEventListener('click', () => {
            this.closeModal('success-modal');
        });
        
        // Modal close button
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                this.closeModal(modal.id);
            });
        });
        
        // Copy transaction ID
        document.getElementById('copy-tx-id').addEventListener('click', () => {
            this.copyToClipboard('transaction-id');
        });
        
        // Currency search
        document.querySelectorAll('.currency-search').forEach(input => {
            input.addEventListener('input', (e) => {
                this.filterCurrencies(e.target.value, e.target.dataset.target);
            });
        });
        
        // Close modals on background click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });
    }

    // Toggle currency dropdown
    toggleDropdown(type) {
        const dropdown = document.getElementById(`${type}-dropdown`);
        const btn = document.getElementById(`${type}-currency-btn`);
        
        // Close other dropdown
        const otherType = type === 'from' ? 'to' : 'from';
        document.getElementById(`${otherType}-dropdown`).style.display = 'none';
        document.getElementById(`${otherType}-currency-btn`).classList.remove('active');
        
        if (dropdown.style.display === 'none') {
            dropdown.style.display = 'block';
            btn.classList.add('active');
        } else {
            dropdown.style.display = 'none';
            btn.classList.remove('active');
        }
    }

    // Select currency
    selectCurrency(coin, target) {
        if (target === 'from') {
            this.fromCoin = coin.id;
            document.getElementById('from-code').textContent = coin.symbol;
            document.getElementById('from-symbol').textContent = coin.symbol;
        } else {
            this.toCoin = coin.id;
            document.getElementById('to-code').textContent = coin.symbol;
            document.getElementById('to-symbol').textContent = coin.symbol;
        }
        
        this.toggleDropdown(target);
        this.calculateExchange();
        this.updateExchangeRate();
    }

    // Swap currencies
    swapCurrencies() {
        const temp = this.fromCoin;
        this.fromCoin = this.toCoin;
        this.toCoin = temp;
        
        const fromCode = document.getElementById('from-code').textContent;
        const toCode = document.getElementById('to-code').textContent;
        
        document.getElementById('from-code').textContent = toCode;
        document.getElementById('to-code').textContent = fromCode;
        
        document.getElementById('from-symbol').textContent = toCode;
        document.getElementById('to-symbol').textContent = fromCode;
        
        const fromAmount = document.getElementById('from-amount');
        const toAmount = document.getElementById('to-amount');
        
        const temp2 = fromAmount.value;
        fromAmount.value = toAmount.value;
        toAmount.value = temp2;
        
        this.calculateExchange();
        this.updateExchangeRate();
    }

    // Filter currencies in dropdown
    filterCurrencies(query, target) {
        const items = document.querySelectorAll(`#${target}-list .currency-item`);
        const lowerQuery = query.toLowerCase();
        
        items.forEach(item => {
            const name = item.querySelector('.currency-item-name').textContent.toLowerCase();
            const symbol = item.querySelector('.currency-item-symbol').textContent.toLowerCase();
            
            if (name.includes(lowerQuery) || symbol.includes(lowerQuery)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Update exchange rate
    async updateExchangeRate() {
        try {
            const response = await fetch(
                `https://api.coingecko.com/api/v3/simple/price?ids=${this.fromCoin},${this.toCoin}&vs_currencies=usd`
            );
            const data = await response.json();
            
            const fromPrice = data[this.fromCoin]?.usd || 0;
            const toPrice = data[this.toCoin]?.usd || 0;
            
            this.exchangeRates[this.fromCoin] = fromPrice;
            this.exchangeRates[this.toCoin] = toPrice;
            
            document.getElementById('from-price').textContent = fromPrice.toFixed(2);
            document.getElementById('to-price').textContent = toPrice.toFixed(2);
            
            this.calculateExchange();
        } catch (error) {
            console.error('Error updating exchange rate:', error);
        }
    }

    // Calculate exchange amount
    calculateExchange() {
        const fromAmount = parseFloat(document.getElementById('from-amount').value) || 0;
        const fromPrice = this.exchangeRates[this.fromCoin] || 0;
        const toPrice = this.exchangeRates[this.toCoin] || 0;
        
        if (fromPrice === 0 || toPrice === 0) {
            document.getElementById('to-amount').value = '0.00';
            return;
        }
        
        // Calculate the amount in USD first, then convert to target coin
        const usdAmount = fromAmount * fromPrice;
        const toAmount = usdAmount / toPrice;
        
        document.getElementById('to-amount').value = toAmount.toFixed(8);
        
        // Update exchange rate display
        const rateFromToAmount = toAmount / (fromAmount || 1);
        const fromCode = document.getElementById('from-code').textContent;
        const toCode = document.getElementById('to-code').textContent;
        
        document.getElementById('exchange-rate').textContent = 
            `1 ${fromCode} = ${rateFromToAmount.toFixed(4)} ${toCode}`;
        
        // Calculate total cost with fees
        const feeAmount = usdAmount * (this.fee / 100);
        const totalCost = usdAmount + feeAmount + this.networkFee;
        
        document.getElementById('exchange-fee').textContent = this.fee + '%';
        document.getElementById('total-with-fee').textContent = '$' + totalCost.toFixed(2);
    }

    // Open transaction confirmation modal
    openTransactionModal() {
        const fromAmount = parseFloat(document.getElementById('from-amount').value) || 0;
        const toAmount = parseFloat(document.getElementById('to-amount').value) || 0;
        const fromCode = document.getElementById('from-code').textContent;
        const toCode = document.getElementById('to-code').textContent;
        
        if (fromAmount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        // Update modal with transaction details
        document.getElementById('modal-from-amount').textContent = fromAmount.toFixed(8);
        document.getElementById('modal-from-code').textContent = fromCode;
        document.getElementById('modal-to-amount').textContent = toAmount.toFixed(8);
        document.getElementById('modal-to-code').textContent = toCode;
        
        const fromPrice = this.exchangeRates[this.fromCoin] || 0;
        const usdAmount = fromAmount * fromPrice;
        const feeAmount = usdAmount * (this.fee / 100);
        const totalCost = usdAmount + feeAmount + this.networkFee;
        
        const rate = (toAmount / fromAmount).toFixed(4);
        document.getElementById('modal-rate').textContent = 
            `1 ${fromCode} = ${rate} ${toCode}`;
        
        document.getElementById('modal-network-fee').textContent = 
            '$' + this.networkFee.toFixed(2);
        
        document.getElementById('modal-platform-fee').textContent = 
            '$' + feeAmount.toFixed(2);
        
        document.getElementById('modal-total').textContent = 
            '$' + totalCost.toFixed(2);
        
        this.openModal('transaction-modal');
    }

    // Confirm exchange
    async confirmExchange() {
        const walletAddress = document.getElementById('wallet-address').value.trim();
        const fromAmount = parseFloat(document.getElementById('from-amount').value) || 0;
        const toAmount = parseFloat(document.getElementById('to-amount').value) || 0;
        const fromCode = document.getElementById('from-code').textContent;
        const toCode = document.getElementById('to-code').textContent;
        
        if (!walletAddress) {
            alert('Please enter your wallet address');
            return;
        }
        
        // Show loading state
        const confirmBtn = document.getElementById('confirm-btn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner"></span> Processing...';
        
        try {
            // Simulate API call to backend
            const response = await fetch(wordpress_exchanger_vars.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'process_exchange',
                    nonce: wordpress_exchanger_vars.nonce,
                    from_coin: this.fromCoin,
                    to_coin: this.toCoin,
                    from_amount: fromAmount,
                    to_amount: toAmount,
                    wallet_address: walletAddress,
                    fee: this.fee,
                    network_fee: this.networkFee,
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.closeModal('transaction-modal');
                
                // Update success modal
                document.getElementById('transaction-id').textContent = data.transaction_id;
                this.openModal('success-modal');
                
                // Reset form
                document.getElementById('from-amount').value = '';
                document.getElementById('to-amount').value = '';
                document.getElementById('wallet-address').value = '';
            } else {
                alert('Exchange failed: ' + data.message);
            }
        } catch (error) {
            console.error('Error processing exchange:', error);
            alert('An error occurred. Please try again.');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Confirm Exchange';
        }
    }

    // Open modal
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('show');
    }

    // Close modal
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
    }

    // Copy to clipboard
    copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent;
        
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copy-tx-id');
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('copied');
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('copied');
            }, 2000);
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new MultiCoinExchanger();
});
