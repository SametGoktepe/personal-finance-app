# 🚀 Quick Start Guide

Get your Personal Finance Manager up and running in 5 minutes!

## ⚡ Fast Track Setup

### Prerequisites
- PHP 8.2+
- Composer
- SQLite (or MySQL/PostgreSQL)

### One-Command Setup

```bash
# Clone the repository
git clone https://github.com/SametGoktepe/personal-finance-app.git
cd personal-finance-app

# Run automated setup
composer setup
```

That's it! The `composer setup` command will:
- ✅ Install all PHP dependencies
- ✅ Create and configure .env file
- ✅ Generate application key
- ✅ Run database migrations
- ✅ Seed sample data
- ✅ **Fetch live exchange rates from API**
- ✅ Install npm packages
- ✅ Build frontend assets

### Start the Application

```bash
php artisan serve
```

Visit: `http://localhost:8000/admin`

## 🔐 Login

Use these credentials:
- **Email:** admin@admin.com
- **Password:** password

⚠️ Change these after first login!

## 🎯 What You Get

### Sample Data Included

✅ **6 Categories:**
- Salary (Income)
- Freelance (Income)
- Food & Dining (Expense)
- Transportation (Expense)
- Entertainment (Expense)
- Utilities (Expense)

✅ **4 Accounts:**
- Main Bank Account: 5,000 ₺
- Debit Card: 1,000 ₺
- Cash Wallet: 500 ₺
- Credit Card: 0 ₺

✅ **4 Sample Transactions:**
- Monthly salary
- Grocery shopping
- Gas station
- Movie tickets

✅ **4 Subscriptions:**
- Netflix: 149.99 ₺/month (Due in 5 days)
- Spotify: 54.99 ₺/month (Due in 12 days)
- Internet: 350 ₺/month (Due in 8 days)
- YouTube Premium: 29.99 ₺/month (Due in 20 days)

✅ **Live Exchange Rates:**
- USD, EUR, GBP, JPY ↔ TRY
- Updated from API automatically

## 📱 Quick Tour

### Dashboard (`/admin`)
- Total balance across all accounts
- Income vs Expense statistics
- 12-month trend chart
- Upcoming subscriptions (next 14 days)
- Latest transactions

### Main Features

1. **Categories** - Organize your finances
2. **Accounts** - Track multiple accounts
3. **Transactions** - Record income/expenses
4. **Budgets** - Set spending limits
5. **Subscriptions** - Manage recurring payments
6. **Exchange Rates** - Multi-currency support

## 🔄 Daily Workflow

### Adding a Transaction
1. Go to **Finance > Transactions**
2. Click **New Transaction**
3. Select account, category, enter amount
4. Choose currency (auto-converts to TRY)
5. Save!

### Checking Subscriptions
1. Dashboard shows **Upcoming Subscriptions**
2. Color-coded: Red (overdue), Yellow (due soon), Green (upcoming)
3. Click to manage in **Finance > Subscriptions**

### Updating Exchange Rates
**Auto:** Runs daily at 09:00 automatically

**Manual:**
- Settings > Exchange Rates > "Update from API" button
- Or run: `php artisan rates:update`

## ⚙️ Configuration

### Change Base Currency

Currently TRY, to change edit:
- Migrations default values
- `CurrencyService.php`
- Dashboard calculations

### Enable Scheduled Tasks

**Development:**
```bash
php artisan schedule:work
```

**Production (Add to crontab):**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Custom Colors

Edit theme in:
```php
app/Providers/Filament/AdminPanelProvider.php

->colors([
    'primary' => Color::Amber,  // Change here
])
```

## 🛠️ Common Commands

```bash
# Update exchange rates
php artisan rates:update

# View scheduled tasks
php artisan schedule:list

# Clear cache
php artisan optimize:clear

# Run migrations
php artisan migrate

# Seed data
php artisan db:seed

# Fresh install
php artisan migrate:fresh --seed
```

## 📖 Full Documentation

For detailed documentation, see:
- [README.md](README.md) - Complete guide
- [MULTI_CURRENCY_GUIDE.md](MULTI_CURRENCY_GUIDE.md) - Currency features
- [CURRENCY_API_GUIDE.md](CURRENCY_API_GUIDE.md) - API integration

## 🆘 Troubleshooting

### Can't access /admin?
- Make sure migrations ran: `php artisan migrate:status`
- Check if user exists: `php artisan tinker` → `User::count()`
- Clear cache: `php artisan optimize:clear`

### Exchange rates not updating?
- Check internet connection
- Run manually: `php artisan rates:update`
- Check logs: `storage/logs/laravel.log`

### Assets not loading?
- Run: `npm run build`
- Check if files exist in `public/build/`

### Database error?
- Check .env database configuration
- Create SQLite file: `touch database/database.sqlite`
- Run migrations: `php artisan migrate`

## 🎉 You're Ready!

Your personal finance manager is ready to use. Start by:
1. ✅ Logging in
2. ✅ Exploring the dashboard
3. ✅ Adding your first transaction
4. ✅ Setting up your budgets
5. ✅ Adding your subscriptions

Need help? Check the full [README.md](README.md) or create an issue on GitHub!

---

**Happy Finance Tracking! 💰**

