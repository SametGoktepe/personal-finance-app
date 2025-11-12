# Currency API Integration Guide

## Overview

The application integrates with [finans.truncgil.com API](https://finans.truncgil.com/v4/today.json) to fetch real-time exchange rates for TRY (Turkish Lira).

## Features

### 🔄 Automatic Rate Updates
- **Live API Integration** - Fetches current exchange rates
- **Daily Updates** - Scheduled to run every day at 09:00
- **Manual Updates** - Update button in Exchange Rates page
- **Smart Caching** - Uses database rates when API is unavailable

### 💱 Supported Currencies
- 🇺🇸 **USD** - US Dollar
- 🇪🇺 **EUR** - Euro
- 🇬🇧 **GBP** - British Pound
- 🇯🇵 **JPY** - Japanese Yen

All rates are relative to TRY (Turkish Lira) as the base currency.

## API Details

### Endpoint
```
https://finans.truncgil.com/v4/today.json
```

### Data Structure
```json
{
  "Update_Date": "2025-11-12 13:09:01",
  "USD": {
    "Type": "Currency",
    "Change": 0.05,
    "Name": "Amerikan Doları",
    "Buying": 42.2402,
    "Selling": 42.2467
  },
  "EUR": { ... },
  "GBP": { ... },
  "JPY": { ... }
}
```

### Rate Selection
The application uses the **Buying** rate for currency conversions as it's more accurate for expense tracking.

## Usage

### Manual Update via Command Line

Update exchange rates manually:

```bash
php artisan rates:update
```

Output example:
```
Fetching exchange rates from API...
API Last Update: 2025-11-12 13:09:01

Updating exchange rates...

✓ Successfully updated:
  • USD - Updated (1 USD = 42.2379 TRY)
  • EUR - Updated (1 EUR = 48.9271 TRY)
  • GBP - Updated (1 GBP = 55.4591 TRY)
  • JPY - Updated (1 JPY = 0.002728 TRY)

Total rates updated: 8

✓ Exchange rates updated successfully!
```

### Manual Update via Admin Panel

1. Go to **Settings > Exchange Rates**
2. Click **"Update from API"** button in the top right
3. Confirm the update
4. Success notification will appear

### Automatic Updates

The application is configured to update rates automatically every day at 09:00.

To enable scheduled tasks, run Laravel scheduler:

```bash
php artisan schedule:work
```

Or add to cron (production):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## How It Works

### 1. CurrencyService

Located at `app/Services/CurrencyService.php`

**Key Methods:**
- `updateExchangeRates()` - Fetch and update all rates
- `getRateToTRY($currency)` - Get live rate for specific currency
- `getCurrentRate($from, $to)` - Get rate between any two currencies
- `convert($amount, $from, $to)` - Convert amount between currencies

### 2. Artisan Command

Located at `app/Console/Commands/UpdateExchangeRates.php`

**Command:** `rates:update`

**Options:**
- `--force` - Force update even if recently updated (future use)

### 3. Transaction Integration

When creating a transaction with foreign currency:
1. System attempts to fetch live rate from API
2. If successful, uses current rate
3. If API fails, falls back to database rates
4. Rate is saved with the transaction for historical accuracy

### 4. Scheduled Task

Configured in `routes/console.php`:
```php
Schedule::command('rates:update')
    ->dailyAt('09:00')
    ->name('Update Exchange Rates')
    ->onOneServer();
```

## Rate Storage

### Database Structure
```
exchange_rates table:
- from_currency: Currency code (e.g., 'USD')
- to_currency: Currency code (e.g., 'TRY')
- rate: Exchange rate (decimal, 6 places)
- date: Rate date
- is_active: Boolean flag
```

### Bidirectional Rates
For each currency pair, two records are created:
- USD → TRY (e.g., 42.24)
- TRY → USD (e.g., 0.0237) [calculated as 1/42.24]

This allows efficient conversion in both directions.

## Error Handling

### API Failures
- Logged to Laravel log file
- Graceful fallback to database rates
- User-friendly error messages
- No interruption to transaction creation

### Missing Rates
- Defaults to 1.0 if no rate found
- Warning logged for investigation
- Manual rate entry still available

## Benefits

### For Users
- ✅ Always up-to-date exchange rates
- ✅ No manual rate entry needed
- ✅ Accurate transaction calculations
- ✅ Historical rate preservation

### For Developers
- ✅ Clean service architecture
- ✅ Easy to extend for more currencies
- ✅ Testable code
- ✅ Comprehensive error handling

## Extending the System

### Adding More Currencies

Edit `app/Services/CurrencyService.php`:

```php
protected array $supportedCurrencies = [
    'USD', 
    'EUR', 
    'GBP', 
    'JPY',
    'CHF', // Add Swiss Franc
    'CAD', // Add Canadian Dollar
];
```

Then add to forms:
- `app/Filament/Resources/Accounts/Schemas/AccountForm.php`
- `app/Filament/Resources/Transactions/Schemas/TransactionForm.php`
- `app/Filament/Resources/ExchangeRates/Schemas/ExchangeRateForm.php`

### Alternative API Sources

To use a different API, modify `CurrencyService.php`:

```php
protected string $apiUrl = 'https://your-api-url.com/rates';
```

And adjust the data parsing logic in `updateExchangeRates()` method.

## Testing

Test the currency service:

```bash
# Update rates
php artisan rates:update

# Check the exchange_rates table
php artisan tinker
>>> ExchangeRate::where('date', today())->get()
```

## Monitoring

### Check Last Update
```bash
php artisan rates:update
```

Shows API last update time at the beginning.

### Database Verification
Check `exchange_rates` table in admin panel:
- Settings > Exchange Rates
- Sort by date to see latest rates
- Check "Update_Date" in description

## Production Considerations

### Cron Setup
```bash
# Add to crontab
crontab -e

# Add this line:
* * * * * cd /path/to/personal-finance && php artisan schedule:run >> /dev/null 2>&1
```

### API Rate Limiting
The API appears to have no strict rate limits, but:
- Updates run once daily (09:00)
- Manual updates require confirmation
- Timeout set to 10 seconds

### Failover Strategy
1. Live API fetch (first priority)
2. Database cached rates (fallback)
3. Manual entry (always available)

## Troubleshooting

### Problem: SSL Certificate Error
**Solution:** Already handled with `verify => false` option

### Problem: API Not Responding
**Solution:** System automatically falls back to database rates

### Problem: Rates Not Updating
**Check:**
1. Internet connection
2. API availability: https://finans.truncgil.com/v4/today.json
3. Laravel logs: `storage/logs/laravel.log`
4. Run manually: `php artisan rates:update`

### Problem: Wrong Rates
**Check:**
1. API data format hasn't changed
2. Correct currency codes used
3. Database rates are up to date

## API Response Example

```json
{
  "Update_Date": "2025-11-12 13:09:01",
  "USD": {
    "Type": "Currency",
    "Change": 0.05,
    "Name": "Amerikan Doları",
    "Buying": 42.2402,
    "Selling": 42.2467
  }
}
```

We use the **Buying** rate as it's more representative of the actual cost when purchasing foreign currency.

## Future Improvements

- [ ] Support for more currencies from the API
- [ ] Rate change notifications
- [ ] Historical rate charts
- [ ] Custom update frequency
- [ ] Multiple API sources with fallback
- [ ] Rate comparison alerts

---

**API Source:** [finans.truncgil.com](https://finans.truncgil.com/v4/today.json)

