<?php
/**
 * Template: Main Exchange Interface
 * Description: Primary exchange page template
 */

get_header();
?>

<div class="exchanger-container">
    <div class="exchanger-wrapper">
        
        <!-- Header Section -->
        <div class="exchanger-header">
            <h1 class="exchanger-title">Multi-Coin Exchanger</h1>
            <p class="exchanger-subtitle">Fast, secure, and easy cryptocurrency exchange</p>
        </div>

        <!-- Main Exchange Box -->
        <div class="exchange-box">
            
            <!-- FROM SECTION -->
            <div class="exchange-section exchange-from">
                <label class="exchange-label">
                    <span class="label-text">You Send</span>
                    <span class="exchange-balance">Balance: <span id="from-balance">0.00</span></span>
                </label>

                <div class="exchange-input-group">
                    <input 
                        type="number" 
                        id="from-amount" 
                        class="exchange-input" 
                        placeholder="0.00" 
                        step="0.00000001"
                        min="0"
                    />
                    
                    <div class="currency-selector from-currency">
                        <button class="currency-btn" id="from-currency-btn">
                            <span class="currency-icon">
                                <img id="from-icon" src="" alt="Currency" class="coin-icon" />
                            </span>
                            <span class="currency-code" id="from-code">BTC</span>
                            <span class="dropdown-icon">▼</span>
                        </button>

                        <div class="currency-dropdown" id="from-dropdown" style="display: none;">
                            <input 
                                type="text" 
                                class="currency-search" 
                                placeholder="Search coin..." 
                                data-target="from"
                            />
                            <div class="currency-list" id="from-list">
                                <!-- Populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="exchange-info">
                    <small class="exchange-price">
                        1 <span id="from-symbol">BTC</span> = $<span id="from-price">0.00</span>
                    </small>
                </div>
            </div>

            <!-- SWAP BUTTON -->
            <div class="swap-container">
                <button class="swap-btn" id="swap-btn" title="Swap currencies">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </button>
            </div>

            <!-- TO SECTION -->
            <div class="exchange-section exchange-to">
                <label class="exchange-label">
                    <span class="label-text">You Receive</span>
                    <span class="exchange-balance">Balance: <span id="to-balance">0.00</span></span>
                </label>

                <div class="exchange-input-group">
                    <input 
                        type="number" 
                        id="to-amount" 
                        class="exchange-input" 
                        placeholder="0.00" 
                        step="0.00000001"
                        min="0"
                        readonly
                    />
                    
                    <div class="currency-selector to-currency">
                        <button class="currency-btn" id="to-currency-btn">
                            <span class="currency-icon">
                                <img id="to-icon" src="" alt="Currency" class="coin-icon" />
                            </span>
                            <span class="currency-code" id="to-code">ETH</span>
                            <span class="dropdown-icon">▼</span>
                        </button>

                        <div class="currency-dropdown" id="to-dropdown" style="display: none;">
                            <input 
                                type="text" 
                                class="currency-search" 
                                placeholder="Search coin..." 
                                data-target="to"
                            />
                            <div class="currency-list" id="to-list">
                                <!-- Populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="exchange-info">
                    <small class="exchange-price">
                        1 <span id="to-symbol">ETH</span> = $<span id="to-price">0.00</span>
                    </small>
                </div>
            </div>

        </div>

        <!-- Exchange Details -->
        <div class="exchange-details">
            <div class="detail-row">
                <span class="detail-label">Exchange Rate:</span>
                <span class="detail-value" id="exchange-rate">1 BTC = 15.5 ETH</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fee:</span>
                <span class="detail-value" id="exchange-fee">0.5%</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">You'll Pay:</span>
                <span class="detail-value" id="total-with-fee">$0.00</span>
            </div>
        </div>

        <!-- Exchange Button -->
        <button class="exchange-btn" id="exchange-btn">
            <span class="btn-text">Exchange Now</span>
            <span class="btn-loader" style="display: none;">
                <span class="spinner"></span>
            </span>
        </button>

        <!-- Additional Info -->
        <div class="exchanger-info">
            <div class="info-item">
                <div class="info-icon">⚡</div>
                <div class="info-content">
                    <h3>Fast Transactions</h3>
                    <p>Exchange completed in seconds</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">🔒</div>
                <div class="info-content">
                    <h3>Secure</h3>
                    <p>Bank-level security for your funds</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">💰</div>
                <div class="info-content">
                    <h3>Best Rates</h3>
                    <p>Competitive exchange rates</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Transaction Modal -->
<div class="modal" id="transaction-modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        
        <div class="modal-header">
            <h2>Confirm Exchange</h2>
        </div>

        <div class="modal-body">
            <div class="transaction-summary">
                <div class="transaction-item">
                    <div class="transaction-from">
                        <span class="transaction-label">Sending</span>
                        <div class="transaction-amount">
                            <span id="modal-from-amount">0.00</span>
                            <span id="modal-from-code">BTC</span>
                        </div>
                    </div>
                </div>

                <div class="transaction-arrow">→</div>

                <div class="transaction-item">
                    <div class="transaction-to">
                        <span class="transaction-label">Receiving</span>
                        <div class="transaction-amount">
                            <span id="modal-to-amount">0.00</span>
                            <span id="modal-to-code">ETH</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="transaction-details">
                <div class="detail-row">
                    <span>Exchange Rate:</span>
                    <span id="modal-rate">1 BTC = 15.5 ETH</span>
                </div>
                <div class="detail-row">
                    <span>Network Fee:</span>
                    <span id="modal-network-fee">$2.50</span>
                </div>
                <div class="detail-row">
                    <span>Platform Fee (0.5%):</span>
                    <span id="modal-platform-fee">$0.00</span>
                </div>
                <div class="detail-row total">
                    <span>Total Cost:</span>
                    <span id="modal-total">$0.00</span>
                </div>
            </div>

            <div class="transaction-address">
                <label>Your Wallet Address:</label>
                <input 
                    type="text" 
                    id="wallet-address" 
                    class="address-input" 
                    placeholder="Enter your receiving address"
                />
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" id="cancel-btn">Cancel</button>
            <button class="btn-confirm" id="confirm-btn">Confirm Exchange</button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal" id="success-modal">
    <div class="modal-content modal-success">
        <div class="success-icon">✓</div>
        <h2>Exchange Successful!</h2>
        <p>Your transaction has been submitted.</p>
        
        <div class="transaction-id">
            <label>Transaction ID:</label>
            <div class="transaction-id-box">
                <span id="transaction-id">TX123456789</span>
                <button class="copy-btn" id="copy-tx-id">Copy</button>
            </div>
        </div>

        <p class="success-message">You will receive your coins shortly.</p>
        
        <button class="btn-close" id="close-success">Done</button>
    </div>
</div>

<?php
get_footer();
?>
