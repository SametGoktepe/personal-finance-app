# 🌐 API Integration Documentation

## Currency Exchange Rate API

### Overview

The Personal Finance Manager integrates with the Turkish Financial API to provide real-time exchange rates.

**API Endpoint:** [https://finans.truncgil.com/v4/today.json](https://finans.truncgil.com/v4/today.json)

### Features

✅ **Real-Time Rates** - Current exchange rates updated multiple times per day
✅ **Automatic Updates** - Scheduled daily updates
✅ **Manual Refresh** - One-click update from admin panel
✅ **Smart Fallback** - Uses cached database rates when API is unavailable
✅ **Transaction Integration** - Auto-applies current rates when creating transactions

## API Response Format

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
  "EUR": {
    "Type": "Currency",
    "Change": -0.03,
    "Name": "Euro",
    "Buying": 48.9197,
    "Selling": 48.9309
  },
  "GBP": {
    "Type": "Currency",
    "Change": -0.27,
    "Name": "İngiliz Sterlini",
    "Buying": 55.4556,
    "Selling": 55.4661
  },
  "JPY": {
    "Type": "Currency",
    "Name": "Japon Yeni",
    "Buying": 0.002727,
    "Selling": 0.002734,
    "Change": 0
  }
}
```

### Data Fields

- **Update_Date**: Timestamp of last API update
- **Type**: "Currency" or "CryptoCurrency"
- **Name**: Full name in Turkish
- **Buying**: Rate for buying the currency (we use this)
- **Selling**: Rate for selling the currency
- **Change**: Percentage change from previous day

## Implementation

### CurrencyService Class

Location: `app/Services/CurrencyService.php`

```php
// Update all exchange rates
$service = app(CurrencyService::class);
$results = $service->updateExchangeRates();

// Get live rate for specific currency
$rate = $service->getRateToTRY('USD'); // Returns current USD to TRY rate

// Convert between currencies
$converted = $service->convert(100, 'USD', 'TRY'); // Converts $100 to TRY
```

### Artisan Command

Location: `app/Console/Commands/UpdateExchangeRates.php`

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

### Admin Panel Integration

**Manual Update Button:**
- Navigate to: Settings > Exchange Rates
- Click: "Update from API" (green button, top right)
- Confirm the update
- Success notification appears
- Table refreshes with new rates

### Transaction Form Integration

When creating a transaction:
1. Select foreign currency (USD, EUR, GBP, JPY)
2. System automatically fetches live rate from API
3. Exchange rate field auto-populates
4. Falls back to database if API unavailable
5. You can manually override if needed

## Scheduled Updates

### Configuration

Location: `routes/console.php`

```php
Schedule::command('rates:update')
    ->dailyAt('09:00')
    ->name('Update Exchange Rates')
    ->onOneServer();
```

### Running the Scheduler

**Development:**
```bash
php artisan schedule:work
```

**Production:**

Add to crontab:
```bash
crontab -e
```

Add this line:
```bash
* * * * * cd /path/to/personal-finance && php artisan schedule:run >> /dev/null 2>&1
```

### Verify Schedule

```bash
php artisan schedule:list
```

Output:
```
  0 9 * * * php artisan rates:update .............. Next Due: 18 hours from now
```

## Error Handling

### API Failures

**Scenarios:**
- Network timeout
- SSL certificate issues
- API endpoint down
- Invalid response format

**Handling:**
1. Error logged to `storage/logs/laravel.log`
2. User notification shown
3. System falls back to database rates
4. Transaction creation continues normally

### Fallback Strategy

```
1. Try live API ─┐
                 │
2. Use database ─┼─→ Success
   cached rates  │
                 │
3. Manual entry ─┘
```

## Rate Storage

### Database Table Structure

```sql
CREATE TABLE exchange_rates (
    id BIGINT PRIMARY KEY,
    from_currency VARCHAR(3),
    to_currency VARCHAR(3),
    rate DECIMAL(10,6),
    date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(from_currency, to_currency, date)
);
```

### Bidirectional Storage

For each API currency, we store 2 records:

**Example for USD:**
1. USD → TRY: rate = 42.24
2. TRY → USD: rate = 0.0237 (calculated as 1/42.24)

This allows efficient conversion in both directions.

## Supported Currencies

Currently fetching 4 currencies from API:
- **USD** - US Dollar
- **EUR** - Euro  
- **GBP** - British Pound
- **JPY** - Japanese Yen

### Adding More Currencies

Edit `app/Services/CurrencyService.php`:

```php
protected array $supportedCurrencies = [
    'USD', 
    'EUR', 
    'GBP', 
    'JPY',
    'CHF', // Swiss Franc (available in API)
    'CAD', // Canadian Dollar (available in API)
    'AUD', // Australian Dollar (available in API)
];
```

The API provides 60+ currencies and cryptocurrencies!

## Performance Considerations

### Caching Strategy
- API calls are only made when explicitly requested
- Database stores all historical rates
- No unnecessary API calls during transaction creation

### Rate Limiting
- Scheduled update: Once per day
- Manual updates: Require confirmation
- API timeout: 10 seconds
- No strict rate limits on API

### Network Requirements
- API calls require internet connection
- SSL/TLS support required
- Handled gracefully if offline

## Monitoring & Logs

### Check Last Update

Via command:
```bash
php artisan rates:update
```

Shows API update time at the start.

### Database Check

```bash
php artisan tinker
```

```php
// Get latest rates
ExchangeRate::where('date', today())->get();

// Get specific rate
ExchangeRate::where('from_currency', 'USD')
    ->where('to_currency', 'TRY')
    ->latest('date')
    ->first();
```

### Log Files

Check `storage/logs/laravel.log` for:
- API connection errors
- Failed rate updates
- Data parsing issues

## Testing

### Manual Test

```bash
# Update rates
php artisan rates:update

# Check database
php artisan tinker
>>> ExchangeRate::latest()->take(5)->get()

# Test conversion
>>> App\Services\CurrencyService::class
>>> $service = app($service)
>>> $service->convert(100, 'USD', 'TRY')
```

### Test in Admin Panel

1. Go to: Settings > Exchange Rates
2. Click: "Update from API"
3. Check for success notification
4. Verify rates in table
5. Try creating a transaction with USD

## Troubleshooting

### Problem: API Update Fails

**Check:**
```bash
# Test API directly
curl https://finans.truncgil.com/v4/today.json

# Check application logs
tail -f storage/logs/laravel.log

# Run update with verbose output
php artisan rates:update
```

### Problem: SSL Certificate Error

Already handled in code with:
```php
Http::withOptions(['verify' => false])->get($url);
```

If still problematic, check PHP curl configuration.

### Problem: Rates Not Applied in Transactions

1. Check if rates exist in database
2. Verify currency is in supported list
3. Check transaction date (uses rates ≤ date)
4. Try manual rate entry

## API Credits

**Data Provider:** [finans.truncgil.com](https://finans.truncgil.com)

Free API providing Turkish Lira exchange rates for 60+ currencies and cryptocurrencies.

**Update Frequency:** Multiple times per day
**Reliability:** High uptime, no authentication required
**Rate Limit:** None observed

## Future Enhancements

- [ ] Support for more currencies from API
- [ ] Cryptocurrency exchange rates
- [ ] Historical rate charts
- [ ] Rate change alerts/notifications
- [ ] Multiple API sources with automatic failover
- [ ] Cache optimization
- [ ] Rate comparison across different sources
- [ ] API usage statistics

## Security Notes

- API calls use HTTPS
- SSL verification can be toggled
- No API key required (public endpoint)
- No sensitive data sent to API
- Rate validation before storage

---

**API Documentation:** [finans.truncgil.com](https://finans.truncgil.com/v4/today.json)

For questions about the API, visit the provider's website.

