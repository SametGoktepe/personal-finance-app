# 💱 Dynamic Currency Management Guide

## Overview

The application now features a **fully database-driven currency system**. You can add, edit, and manage currencies directly from the admin panel without touching any code!

## Features

### ✨ Database-Driven System
- **No Hard-Coded Lists** - All currencies stored in database
- **Admin Panel Management** - Add/edit currencies via UI
- **Dynamic Forms** - All dropdowns populate from database
- **API Integration** - Mark currencies for automatic rate updates
- **Base Currency** - Set any currency as base
- **Flexible** - Add unlimited currencies

## Currency Table Structure

### Fields

```
currencies:
- id (auto)
- code (3 chars, unique) - e.g., USD, EUR, CHF
- name (string) - e.g., US Dollar, Euro
- symbol (10 chars) - e.g., $, €, £, ₺
- is_active (boolean) - Active/Inactive status
- is_base (boolean) - Is this the base currency?
- api_enabled (boolean) - Fetch from API?
- decimal_places (int) - Number of decimals (0-8)
- description (text) - Additional info
- created_at, updated_at
```

### Example Data

| Code | Name | Symbol | Base | API | Active | Decimals |
|------|------|--------|------|-----|--------|----------|
| TRY | Turkish Lira | ₺ | ✅ | ❌ | ✅ | 2 |
| USD | US Dollar | $ | ❌ | ✅ | ✅ | 2 |
| EUR | Euro | € | ❌ | ✅ | ✅ | 2 |
| GBP | British Pound | £ | ❌ | ✅ | ✅ | 2 |
| JPY | Japanese Yen | ¥ | ❌ | ✅ | ✅ | 0 |

## Managing Currencies

### Access the Currency Manager

Navigate to: **Settings > Currencies**

### Adding a New Currency

1. Click **"New Currency"**
2. Fill in the form:
   - **Currency Code**: 3-letter ISO code (e.g., CHF)
   - **Currency Name**: Full name (e.g., Swiss Franc)
   - **Symbol**: Currency symbol (e.g., Fr)
   - **Decimal Places**: Usually 2, JPY uses 0
   - **Base Currency**: Only ONE can be base
   - **API Enabled**: Check if available in finans.truncgil.com API
   - **Active**: Enable/disable currency
   - **Description**: Optional notes

3. Click **"Create"**

### Example: Adding Swiss Franc (CHF)

```
Code: CHF
Name: Swiss Franc
Symbol: Fr
Decimal Places: 2
Base Currency: No
API Enabled: Yes (available in API)
Active: Yes
Description: Swiss Franc - Available from API
```

### Example: Adding Bitcoin (BTC)

```
Code: BTC
Name: Bitcoin
Symbol: ₿
Decimal Places: 8
Base Currency: No
API Enabled: No (not in standard currency API)
Active: Yes
Description: Cryptocurrency - Manual rates only
```

## How It Works

### Dynamic Loading

All currency dropdowns automatically load from database:

**Accounts Form:**
```php
Currency::getOptions()
// Returns: ['TRY' => 'TRY - Turkish Lira (₺)', ...]
```

**Transactions Form:**
```php
Currency::getOptions()
// Base currency shown first
```

**Exchange Rates:**
```php
Currency::getActive()
// All active currencies
```

### Base Currency Logic

Only ONE currency can be `is_base = true`:
- Used for all reporting
- All transactions convert to base
- Dashboard shows base currency
- Currently: TRY (Turkish Lira)

### API Integration

When `api_enabled = true`:
- Currency included in API fetch
- `php artisan rates:update` fetches this currency
- Automatic daily updates
- Shown in Exchange Rates page

## API-Supported Currencies

The finans.truncgil.com API supports 60+ currencies. Some examples you can add:

### Major Currencies
- **CHF** - Swiss Franc
- **CAD** - Canadian Dollar
- **AUD** - Australian Dollar
- **NZD** - New Zealand Dollar
- **SEK** - Swedish Krona
- **NOK** - Norwegian Krone
- **DKK** - Danish Krone

### Middle East
- **SAR** - Saudi Riyal
- **AED** - UAE Dirham
- **KWD** - Kuwaiti Dinar
- **QAR** - Qatari Riyal

### Asia
- **CNY** - Chinese Yuan
- **INR** - Indian Rupee
- **KRW** - South Korean Won
- **MYR** - Malaysian Ringgit
- **PHP** - Philippine Peso

### Others
- **RUB** - Russian Ruble
- **ZAR** - South African Rand
- **BRL** - Brazilian Real
- **MXN** - Mexican Peso

## Workflow

### 1. Add Currency
```
Settings > Currencies > New Currency
```

### 2. Enable API (if available)
- Check "API Enabled" checkbox
- Currency will be fetched in next update

### 3. Update Rates
```bash
php artisan rates:update
```

Or via admin panel:
```
Settings > Exchange Rates > Update from API
```

### 4. Use in Transactions
- Currency immediately available in dropdowns
- Can be selected for accounts
- Can be used in transactions
- Exchange rates auto-applied

## Model Methods

### Currency Model

```php
// Get base currency
$base = Currency::getBase();

// Get all active currencies
$currencies = Currency::getActive();
// Returns: ['TRY' => 'Turkish Lira', 'USD' => 'US Dollar', ...]

// Get API-enabled currencies
$apiCurrencies = Currency::getApiEnabled();
// Returns: ['USD', 'EUR', 'GBP', 'JPY']

// Get formatted options for forms
$options = Currency::getOptions();
// Returns: ['TRY' => 'TRY - Turkish Lira (₺)', ...]
```

### Usage in Code

```php
// In forms
Select::make('currency')
    ->options(fn () => Currency::getOptions())
    ->default(fn () => Currency::getBase()?->code);

// In filters
SelectFilter::make('currency')
    ->options(fn () => Currency::getActive());

// In CurrencyService
$apiCurrencies = Currency::getApiEnabled();
foreach ($apiCurrencies as $code) {
    // Fetch and update rates
}
```

## Changing Base Currency

### Steps

1. Go to **Settings > Currencies**
2. Edit current base currency (TRY)
3. Uncheck "Base Currency"
4. Save
5. Edit new base currency (e.g., USD)
6. Check "Base Currency"
7. Save

**Note:** Only one currency can be base at a time.

### What Changes?
- Dashboard calculations use new base
- All reports convert to new base
- Transaction conversions recalculated
- Exchange rate direction changes

## Deactivating a Currency

### Steps
1. **Settings > Currencies**
2. Edit currency
3. Uncheck "Active"
4. Save

### Effects
- Removed from all dropdowns
- Historical transactions still valid
- Can be reactivated anytime
- Exchange rates preserved

## Best Practices

### 1. Set Correct Decimal Places
- Most currencies: 2 decimals
- Japanese Yen (JPY): 0 decimals
- Cryptocurrencies: 8 decimals

### 2. Use ISO Currency Codes
- Always use official 3-letter codes
- Uppercase (e.g., USD not usd)
- Standard codes ensure compatibility

### 3. Enable API When Available
- Check if currency exists in API
- Test with: https://finans.truncgil.com/v4/today.json
- Enable "API Enabled" for automatic updates

### 4. One Base Currency
- Keep only one as base
- Usually your local currency
- Base affects all reporting

### 5. Add Description
- Note the currency's purpose
- Mention if it's manual or API
- Add usage context

## Adding Custom Currencies

### Example 1: Cryptocurrency (Manual)

```
Code: BTC
Name: Bitcoin
Symbol: ₿
Decimal Places: 8
Base: No
API Enabled: No
Active: Yes
Description: Bitcoin cryptocurrency - Manual exchange rates only
```

Then manually add exchange rates in **Settings > Exchange Rates**.

### Example 2: Regional Currency

```
Code: CHF
Name: Swiss Franc
Symbol: Fr
Decimal Places: 2
Base: No
API Enabled: Yes
Active: Yes
Description: Swiss Franc - Available from API
```

Run `php artisan rates:update` and CHF rates will be fetched automatically!

## Troubleshooting

### Currency Not Showing in Dropdown
**Check:**
- Is `is_active = true`?
- Clear browser cache
- Refresh the page

### API Not Fetching Currency
**Check:**
- Is `api_enabled = true`?
- Currency code matches API (uppercase)
- Currency exists in API response
- Run: `php artisan rates:update`

### Can't Set as Base Currency
**Reason:**
- Another currency is already base
- Uncheck other currency first
- Only one base allowed

### Exchange Rate Not Auto-Calculating
**Check:**
- Currency has exchange rate in database
- API fetch successful
- Date is correct
- Check logs: `storage/logs/laravel.log`

## Benefits of Database-Driven System

### For Users
✅ Add currencies via UI (no coding)
✅ Enable/disable currencies easily
✅ Flexible currency management
✅ No deployment needed for new currencies

### For Developers
✅ Clean, maintainable code
✅ No hard-coded arrays
✅ Easy to extend
✅ Single source of truth

### For System
✅ Scalable (unlimited currencies)
✅ Dynamic updates
✅ Centralized management
✅ Consistent data

## Migration Notes

### Before (Hard-Coded)
```php
'options' => [
    'TRY' => 'TRY - Turkish Lira',
    'USD' => 'USD - US Dollar',
    // Fixed list...
]
```

### After (Database-Driven)
```php
'options' => fn () => Currency::getOptions()
// Dynamic from database!
```

## API Integration

### CurrencyService Updates

Now automatically fetches API-enabled currencies:

```php
// Old
protected array $supportedCurrencies = ['USD', 'EUR', 'GBP', 'JPY'];

// New  
$apiEnabledCurrencies = Currency::getApiEnabled();
```

Add new currency to API updates:
1. Add currency to database
2. Check "API Enabled"
3. Run: `php artisan rates:update`
4. Done!

## Quick Commands

```bash
# View all currencies
php artisan tinker
>>> Currency::all()

# Get base currency
>>> Currency::getBase()

# Get API-enabled currencies
>>> Currency::getApiEnabled()

# Get form options
>>> Currency::getOptions()
```

## Screenshots

### Currency Management
- Settings > Currencies
- Table with Code, Name, Symbol
- Base Currency indicator (star icon)
- API Enabled indicator (wifi icon)
- Active status

### Adding Currency
- Simple form
- All fields explained
- Validation included
- Instant availability

---

**Pro Tip:** Start by adding currencies from the API list. They'll have automatic rate updates! For custom currencies (like cryptocurrencies), add them with `api_enabled = false` and manually manage rates.

Your currency system is now fully flexible and manageable! 🚀

