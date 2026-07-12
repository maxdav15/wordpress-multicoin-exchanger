# Multi-Coin Exchanger for WordPress

A complete WordPress solution for cryptocurrency exchange with a modern, responsive interface. Users can exchange between multiple cryptocurrencies with real-time exchange rates.

## Features

✨ **Key Features**
- Real-time cryptocurrency exchange rates from CoinGecko API
- Support for 10+ popular cryptocurrencies
- Beautiful, responsive exchange interface
- Instant currency conversion calculations
- Transaction history tracking
- Admin dashboard with analytics
- Secure transaction processing with AJAX
- User-friendly wallet address validation

## Installation

### 1. **Add Files to WordPress Theme**

Copy all template files to your WordPress theme directory:

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

### 2. **Create Exchange Page**

1. Go to **WordPress Admin → Pages → Add New**
2. Set the title to "Exchange" or your preferred name
3. In the right sidebar, find **Template** and select **"Exchange Interface"**
4. Publish the page
5. Access your exchanger at the page URL

### 3. **Database Setup**

The plugin automatically creates a transaction table on first activation. If needed, manually create:

```sql
CREATE TABLE wp_exchanger_transactions (
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

## Usage

### For Users

1. **Select Currencies**: Click on currency buttons to select from/to cryptocurrencies
2. **Enter Amount**: Input the amount you want to exchange
3. **View Rate**: Real-time exchange rate displays automatically
4. **Swap**: Click the swap button to reverse the exchange direction
5. **Review Fees**: Platform fee (0.5% default) and network fees shown
6. **Exchange**: Click "Exchange Now" to proceed
7. **Confirm**: Review details and enter wallet address
8. **Receive**: Transaction ID generated, coins sent to wallet

### For Admin

Access the admin dashboard:
1. Go to **Dashboard → Exchanger**
2. View statistics and analytics
3. Manage transactions
4. Configure settings (fees, etc.)

## Configuration

### Adjust Fees

Edit the fee settings in WordPress admin:

```php
// In functions.php or settings page
$fee = 0.5;           // Platform fee: 0.5%
$network_fee = 2.50;  // Network fee: $2.50
```

### Add More Cryptocurrencies

Edit `assets/js/exchanger.js` in the `loadCoins()` method:

```javascript
const popularCoins = [
    { id: 'bitcoin', symbol: 'BTC', name: 'Bitcoin' },
    { id: 'ethereum', symbol: 'ETH', name: 'Ethereum' },
    // Add more coins here
];
```

### Customize Colors

Edit CSS variables in `assets/css/exchanger-style.css`:

```css
:root {
    --primary-color: #6366f1;
    --primary-dark: #4f46e5;
    --secondary-color: #10b981;
    --danger-color: #ef4444;
    /* ... more colors */
}
```

## API Integration

The exchanger uses the **CoinGecko API** (free, no authentication required):

- **Coins List**: `https://api.coingecko.com/api/v3/coins/list`
- **Exchange Rates**: `https://api.coingecko.com/api/v3/simple/price`

For production, consider these alternatives:
- **CoinMarketCap API** (more features, requires API key)
- **Binance API** (real-time prices, high reliability)

## Security Considerations

⚠️ **Important Security Notes**

1. **SSL Certificate**: Always use HTTPS in production
2. **Wallet Address Validation**: Implement blockchain validation
3. **Rate Limiting**: Add rate limiting for AJAX requests
4. **Nonce Verification**: All AJAX requests use WordPress nonces
5. **Input Sanitization**: All user inputs are sanitized
6. **Payment Gateway**: Integrate with Stripe, Coinbase, or similar for payments
7. **2FA**: Consider adding two-factor authentication for users
8. **Address Verification**: Validate wallet addresses before processing

## Customization

### Add Custom Exchange Rates

Replace CoinGecko API with your own rate calculation:

```javascript
// In exchanger.js
async updateExchangeRate() {
    // Call your custom API endpoint
    const response = await fetch('/wp-json/custom/v1/rates');
    // Process rates
}
```

### Add Payment Gateway

Modify the `confirmExchange()` function:

```javascript
// Process payment via Stripe, PayPal, etc.
const paymentResult = await processPaymentWithStripe(amount);
if (paymentResult.success) {
    // Complete exchange
}
```

### Email Notifications

Add to `handle_exchange_ajax()` in functions.php:

```php
// Send confirmation email
wp_mail(
    $user->user_email,
    'Exchange Confirmed',
    'Your transaction: ' . $transaction_id
);
```

## Troubleshooting

### Exchange rates not updating
- Check CoinGecko API is accessible
- Check browser console for errors
- Verify internet connection

### AJAX requests failing
- Verify WordPress nonce is correct
- Check admin-ajax.php is accessible
- Look at WordPress error logs

### Modal not opening
- Check that jQuery is loaded
- Verify JavaScript console for errors
- Ensure modal HTML is in the page

### Transactions not saving
- Verify database table exists
- Check database connection
- Review WordPress error logs

## File Structure

```
├── templates/
│   └── exchange-main.php          # Main template file
├── assets/
│   ├── css/
│   │   └── exchanger-style.css   # All styles
│   └── js/
│       └── exchanger.js           # Main JavaScript
├── functions.php                  # PHP backend
└── README.md                      # This file
```

## Supported Cryptocurrencies

- Bitcoin (BTC)
- Ethereum (ETH)
- Litecoin (LTC)
- Ripple (XRP)
- Cardano (ADA)
- Solana (SOL)
- Polkadot (DOT)
- Dogecoin (DOGE)
- Polygon (MATIC)
- Chainlink (LINK)

## Performance

- **Load Time**: ~2-3 seconds (including API calls)
- **API Calls**: ~2 per page load + updates every 30 seconds
- **Database Queries**: ~1 per transaction
- **Browser Support**: All modern browsers (Chrome, Firefox, Safari, Edge)

## License

This template is provided as-is for use in WordPress projects.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review browser console for errors
3. Check WordPress error logs at `/wp-content/debug.log`
4. Verify all files are in correct locations

## Version

**Version**: 1.0.0  
**Last Updated**: 2024

---

Made with ❤️ for WordPress developers
