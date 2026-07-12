# Multi-Coin Exchanger for WordPress

A complete WordPress solution for cryptocurrency exchange with a modern, responsive interface. Users can exchange between multiple cryptocurrencies with real-time exchange rates.

## ✨ Key Features

- **Real-time Exchange Rates** - Live crypto prices from CoinGecko API (free, no auth required)
- **10+ Cryptocurrencies** - BTC, ETH, LTC, XRP, ADA, SOL, DOT, DOGE, MATIC, LINK
- **Beautiful UI** - Modern gradient design with smooth animations
- **Fully Responsive** - Perfect on mobile, tablet, and desktop
- **Instant Calculations** - Real-time conversion as you type
- **Secure Transactions** - WordPress nonces + input sanitization
- **Transaction Tracking** - Database storage with status management
- **Admin Dashboard** - View stats, manage transactions, configure fees
- **User-friendly** - Intuitive interface for seamless exchanging

## 📦 Files Included

```
wordpress-multicoin-exchanger/
├── templates/
│   └── exchange-main.php              # Main exchange interface template
├���─ assets/
│   ├── css/
│   │   └── exchanger-style.css       # Professional styling (1000+ lines)
│   └── js/
│       └── exchanger.js               # Complete JavaScript functionality
├── functions.php                      # WordPress backend & AJAX handlers
└── README.md                          # This documentation
```

## 🚀 Quick Installation

### Step 1: Copy Files to WordPress Theme

Copy all files to your WordPress theme directory:

```
/wp-content/themes/your-theme/
├── templates/
│   └── exchange-main.php
├── assets/
│   ├── css/
│   │   └── exchanger-style.css
│   └── js/
│       └── exchanger.js
├── functions.php
└── README.md
```

### Step 2: Create Database Table

The table is automatically created, but here's the SQL if needed:

```sql
CREATE TABLE IF NOT EXISTS wp_exchanger_transactions (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    transaction_id varchar(50) NOT NULL UNIQUE,
    from_coin varchar(50) NOT NULL,
    to_coin varchar(50) NOT NULL,
    from_amount decimal(20,8) NOT NULL,
    to_amount decimal(20,8) NOT NULL,
    wallet_address varchar(255) NOT NULL,
    fee decimal(10,2) NOT NULL,
    network_fee decimal(10,2) NOT NULL,
    status varchar(20) DEFAULT 'pending',
    user_id bigint(20) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ip_address varchar(50),
    PRIMARY KEY (id),
    UNIQUE KEY transaction_id (transaction_id),
    KEY status (status),
    KEY user_id (user_id),
    KEY created_at (created_at)
);
```

### Step 3: Create Exchange Page in WordPress

1. Go to **WordPress Admin → Pages → Add New**
2. Set title to "Exchange" or your preferred name
3. In right sidebar, find **Template** dropdown
4. Select **"Exchange Interface"**
5. Publish the page
6. Visit the page URL to use your exchanger!

## 👥 User Guide

### Using the Exchanger

1. **Select From Currency** - Click the first currency button to choose what you're sending
2. **Enter Amount** - Type the amount in the input field
3. **View Rate** - Real-time exchange rate displays automatically
4. **Select To Currency** - Click the second button to choose what you're receiving
5. **Review Fees** - See platform fee and total cost
6. **Swap** - Click the circular swap button to reverse currencies
7. **Exchange** - Click "Exchange Now" to proceed
8. **Confirm** - Review details and enter your wallet address
9. **Complete** - Get your transaction ID and confirmation

### Features

- **Live Price Updates** - Rates update every 30 seconds automatically
- **Search Coins** - Type to search for currencies in the dropdown
- **Instant Calculation** - See conversion amount as you type
- **Mobile Friendly** - Touch-optimized interface
- **Copy Transaction ID** - One-click copying with confirmation

## 🔧 Admin Guide

### Access Admin Dashboard

1. Go to **WordPress Admin → Exchanger**
2. View dashboard stats:
   - Total Transactions
   - Pending Transactions
   - Completed Transactions
   - Trading Volume

### View All Transactions

1. Go to **WordPress Admin → Exchanger → Transactions**
2. See transaction details:
   - Transaction ID
   - From/To Coins
   - Amount
   - Status (Pending, Completed, Failed)
   - User
   - Date

### Configure Settings

1. Go to **WordPress Admin → Exchanger → Settings**
2. Adjust fees:
   - **Platform Fee** - % of transaction (default: 0.5%)
   - **Network Fee** - Fixed fee in USD (default: $2.50)
3. Save changes

## ⚙️ Configuration

### Modify Fees

Edit in **Exchanger Settings** page or in code:

```php
// In functions.php
$fee = 0.5;           // Platform fee: 0.5%
$network_fee = 2.50;  // Network fee: $2.50
```

### Add More Cryptocurrencies

Edit `assets/js/exchanger.js`:

```javascript
const popularCoins = [
    { id: 'bitcoin', symbol: 'BTC', name: 'Bitcoin' },
    { id: 'ethereum', symbol: 'ETH', name: 'Ethereum' },
    // Add more coins here:
    { id: 'your-coin-id', symbol: 'XXX', name: 'Your Coin Name' },
];
```

### Customize Colors

Edit CSS variables in `assets/css/exchanger-style.css`:

```css
:root {
    --primary-color: #6366f1;        /* Main color */
    --primary-dark: #4f46e5;          /* Darker shade */
    --secondary-color: #10b981;       /* Success color */
    --danger-color: #ef4444;          /* Error color */
    /* ... more colors */
}
```

### Custom Exchange Rate API

Replace CoinGecko with your API:

```javascript
// In exchanger.js
async updateExchangeRate() {
    // Call your custom API
    const response = await fetch('/wp-json/custom/v1/rates');
    // Process your rates
}
```

## 🔒 Security Features

✅ **WordPress Nonces** - AJAX requests verified with nonces  
✅ **Input Sanitization** - All user inputs sanitized  
✅ **SQL Injection Prevention** - Uses prepared statements  
✅ **HTTPS Ready** - Fully compatible with SSL/TLS  
✅ **User ID Tracking** - Transactions linked to users  
✅ **IP Logging** - Records transaction IP addresses  

### Security Recommendations

1. **Always use HTTPS** in production
2. **Validate wallet addresses** on backend
3. **Add rate limiting** for AJAX requests
4. **Implement payment gateway** (Stripe, Coinbase, etc.)
5. **Add 2FA** for user accounts
6. **Monitor large transactions** for fraud
7. **Use environment variables** for sensitive data
8. **Regular backups** of transaction data

## 🔌 API Integration

### CoinGecko API (Used by Default)

**Free tier, no authentication:**

```javascript
// Get price
https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum&vs_currencies=usd

// Get all coins
https://api.coingecko.com/api/v3/coins/list
```

### Alternative APIs

**CoinMarketCap API** - More features, requires API key
```
https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest
```

**Binance API** - Real-time prices
```
https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT
```

## 🐛 Troubleshooting

### Exchange rates not updating
- ✓ Check CoinGecko API is accessible
- ✓ Check browser console for errors (F12)
- ✓ Verify internet connection
- ✓ Check rate update interval

### AJAX requests failing
- ✓ Verify WordPress nonce is correct
- ✓ Ensure admin-ajax.php is accessible
- ✓ Check `/wp-content/debug.log` for errors
- ✓ Verify POST parameters

### Modal not opening
- ✓ Check jQuery is loaded
- ✓ Check browser console (F12) for JavaScript errors
- ✓ Ensure modal HTML exists in page
- ✓ Verify CSS display properties

### Transactions not saving
- ✓ Verify database table exists
- ✓ Check database permissions
- ✓ Review WordPress error logs
- ✓ Test database connection

### Styles not loading
- ✓ Check CSS file path is correct
- ✓ Clear browser cache (Ctrl+Shift+Delete)
- ✓ Verify file permissions (644)
- ✓ Check for CSS conflicts

## 📊 Supported Cryptocurrencies

| Coin | Symbol | ID |
|------|--------|-----|
| Bitcoin | BTC | bitcoin |
| Ethereum | ETH | ethereum |
| Litecoin | LTC | litecoin |
| Ripple | XRP | ripple |
| Cardano | ADA | cardano |
| Solana | SOL | solana |
| Polkadot | DOT | polkadot |
| Dogecoin | DOGE | dogecoin |
| Polygon | MATIC | polygon |
| Chainlink | LINK | chainlink |

## ⚡ Performance

- **Load Time**: ~2-3 seconds (including API calls)
- **API Calls**: 2 per page load + updates every 30 seconds
- **Database Queries**: 1 per transaction
- **Browser Support**: All modern browsers (Chrome, Firefox, Safari, Edge)

## 📱 Responsive Design

- ✅ Desktop (1024px+)
- ✅ Tablet (768px - 1023px)
- ✅ Mobile (320px - 767px)
- ✅ Touch-optimized buttons
- ✅ Full-width modals on mobile

## 🎨 UI/UX Features

- Smooth animations and transitions
- Real-time calculations
- Clear fee breakdown
- Transaction confirmation
- Success confirmation with ID
- Copy-to-clipboard functionality
- Loading states
- Error handling
- Mobile-friendly design
- Accessible form inputs

## 📝 Database Schema

**wp_exchanger_transactions** table:

| Column | Type | Purpose |
|--------|------|---------|
| id | INT | Primary key |
| transaction_id | VARCHAR(50) | Unique transaction ID |
| from_coin | VARCHAR(50) | Source cryptocurrency |
| to_coin | VARCHAR(50) | Target cryptocurrency |
| from_amount | DECIMAL(20,8) | Amount sent |
| to_amount | DECIMAL(20,8) | Amount received |
| wallet_address | VARCHAR(255) | Recipient wallet |
| fee | DECIMAL(10,2) | Platform fee |
| network_fee | DECIMAL(10,2) | Network fee |
| status | VARCHAR(20) | Transaction status |
| user_id | BIGINT | WordPress user ID |
| created_at | DATETIME | Creation timestamp |
| updated_at | DATETIME | Update timestamp |
| ip_address | VARCHAR(50) | Client IP address |

## 🚀 Advanced Customization

### Add Payment Gateway

```javascript
// In exchanger.js
async confirmExchange() {
    // Call payment processor
    const payment = await processPayment(amount, stripeToken);
    if (payment.success) {
        // Complete exchange
    }
}
```

### Send Email Notifications

```php
// In functions.php
wp_mail(
    $user->user_email,
    'Exchange Confirmed - Transaction #' . $transaction_id,
    'Your transaction has been submitted'
);
```

### Integrate Blockchain

```javascript
// Call blockchain API to execute transfer
const tx = await blockchainApi.transfer(wallet, amount);
```

### Add Affiliate System

```php
// Track referral commissions
$affiliate_commission = $fee_amount * 0.1; // 10% to affiliate
```

## 📄 License

This template is provided as-is for use in WordPress projects.

## 🤝 Support

**For issues or questions:**

1. Check the Troubleshooting section
2. Review browser console for errors (F12)
3. Check WordPress error logs at `/wp-content/debug.log`
4. Verify all files are in correct locations
5. Ensure database table exists and is accessible

## 📞 Getting Help

- Check file paths and permissions
- Verify WordPress version compatibility (5.0+)
- Test with WordPress debugging enabled
- Use browser developer tools (F12)

## 🔄 Version History

**v1.0.0** (July 2024)
- Initial release
- Real-time exchange rates
- 10 cryptocurrencies
- Admin dashboard
- Transaction tracking
- Mobile responsive

## 🎯 Roadmap

- [ ] Multi-currency support
- [ ] Advanced analytics
- [ ] Affiliate system
- [ ] Multiple payment gateways
- [ ] Mobile app
- [ ] Advanced reporting

## 🙏 Credits

Built with:
- WordPress REST API
- CoinGecko API
- Modern CSS Grid & Flexbox
- Vanilla JavaScript (ES6+)

---

**Made with ❤️ for WordPress developers**

For updates and more templates, visit your WordPress theme repository.

**Last Updated**: July 2024  
**Version**: 1.0.0  
**Compatibility**: WordPress 5.0+  
**PHP Version**: 7.2+
