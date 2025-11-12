# Multi-Currency Support Guide

## Overview

Your Personal Finance application now has full multi-currency support with **TRY (Turkish Lira)** as the base currency! You can track transactions in multiple currencies, manage exchange rates, and view unified reports in TRY.

## Features Added

### 1. **Exchange Rate Management**
- New `Exchange Rates` menu under Settings
- Track exchange rates between currencies
- Historical rate tracking
- Auto-calculation when creating transactions

### 2. **Multi-Currency Transactions**
- Each transaction can have its own currency
- Automatic exchange rate application
- Real-time conversion to base currency (TRY)
- Exchange rate stored with each transaction for historical accuracy

### 3. **Supported Currencies**
- 🇹🇷 TRY - Turkish Lira (Base Currency)
- 🇺🇸 USD - US Dollar
- 🇪🇺 EUR - Euro
- 🇬🇧 GBP - British Pound
- 🇯🇵 JPY - Japanese Yen

### 4. **Account Types**
- Bank Account
- Debit Card
- Cash
- Credit Card
- Investment
- Other

## How to Use

### Adding Exchange Rates

1. Go to **Settings > Exchange Rates**
2. Click **New Exchange Rate**
3. Select currencies and enter rate
4. The rate means: 1 FROM currency = X TO currency

**Example:**
- From: USD
- To: TRY
- Rate: 34.50
- Means: 1 USD = 34.50 TRY

### Creating Multi-Currency Transactions

1. Go to **Finance > Transactions**
2. Click **New Transaction**
3. Fill in details:
   - Select Account
   - Select Category
   - Choose Type (Income/Expense)
   - Enter Amount
   - **Select Currency** (defaults to TRY)
   - Exchange rate will auto-populate from your exchange rates table
   - Select Date

4. The system will:
   - Store the original amount and currency
   - Save the exchange rate at transaction date
   - Calculate TRY equivalent for reporting

### Viewing Transactions

In the Transactions table:
- **Amount column**: Shows original currency and amount
- **Amount (TRY) column**: Shows TRY equivalent (hidden by default, click columns to show)

### Currency Conversion

The system automatically converts all amounts to TRY for:
- Dashboard statistics
- Reports and charts
- Total calculations

## Exchange Rate Logic

### Automatic Rate Selection
When creating a transaction:
1. System finds the most recent exchange rate ≤ transaction date
2. If no rate found, defaults to 1.0
3. Supports reverse rates (e.g., if only USD→TRY exists, TRY→USD is calculated as 1/rate)

### Manual Override
You can manually edit the exchange rate field when creating/editing a transaction if needed.

## API Methods

### Transaction Model Methods

```php
// Get amount in base currency (TRY)
$transaction->getAmountInBaseCurrency(); // Returns float

// Get amount in specific currency
$transaction->getAmountInCurrency('USD'); // Returns float
```

### ExchangeRate Model Methods

```php
// Get exchange rate between currencies
ExchangeRate::getRate('USD', 'TRY', '2024-11-11'); // Returns float

// Convert amount
ExchangeRate::convert(100, 'USD', 'TRY', '2024-11-11'); // Returns float
```

## Sample Data

The system comes with sample exchange rates for today:
- USD → TRY: 34.50
- EUR → TRY: 37.26
- GBP → TRY: 43.83
- JPY → TRY: 0.23
- And reverse rates for all pairs

Sample accounts:
- Main Bank Account: 5,000 ₺
- Debit Card: 1,000 ₺
- Cash Wallet: 500 ₺
- Credit Card: 0 ₺

## Database Structure

### Transactions Table
- `currency` (string, 3): Currency code (default: TRY)
- `exchange_rate` (decimal): Rate to TRY at transaction time
- Original amount stored in original currency

### Exchange Rates Table
- `from_currency` (string, 3)
- `to_currency` (string, 3)
- `rate` (decimal, 6 places)
- `date` (date)
- `is_active` (boolean)

### Accounts Table
- `type`: bank, debit_card, cash, credit_card, investment, other
- `currency` (default: TRY)
- `current_balance`: In account's currency

## Best Practices

1. **Update Rates Regularly**: Add new exchange rates periodically for accuracy
2. **Historical Data**: Keep old rates active for historical transaction accuracy
3. **Base Currency**: TRY is the base currency for all reporting
4. **Manual Rates**: You can override automatic rates if needed
5. **Multiple Currencies**: Each account can have its own currency

## Currency Display

- Dashboard: All amounts shown in TRY (₺)
- Transaction List: Original currency + TRY equivalent (toggle)
- Account Balance: Account's own currency
- Reports: Unified in TRY

## Future Enhancements

Possible additions:
- API integration for automatic rate updates (TCMB, etc.)
- More currencies
- Multi-base currency support
- Currency conversion calculator widget
- Rate change alerts
- Exchange rate history charts

## Troubleshooting

**Q: Transaction shows wrong exchange rate**
A: Check if exchange rate exists for that date. System uses most recent rate before transaction date.

**Q: How to change base currency from TRY?**
A: Currently base currency is TRY. Changing requires modifying dashboard calculations and conversion logic in models.

**Q: Can I have different base currencies for different accounts?**
A: Each account has its own currency, but all reporting converts to TRY for unified view.

**Q: Debit card account type?**
A: Yes! We support 6 account types including debit card, perfect for Turkish banking system.

## Navigation

- **Exchange Rates**: Settings > Exchange Rates
- **Transactions**: Finance > Transactions  
- **Accounts**: Finance > Accounts
- **Dashboard**: Shows all amounts in TRY equivalent

## Login Info

- Email: `admin@admin.com`
- Password: `password`

---

Enjoy managing your finances in multiple currencies with TRY as your base! 🇹🇷💰
