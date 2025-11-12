# 📚 Documentation Index

Welcome to the Personal Finance Manager documentation!

## 🚀 Getting Started

### For New Users
1. **[Quick Start Guide](../QUICKSTART.md)** - Set up in 5 minutes
2. **[Installation Guide](../README.md#installation)** - Detailed setup instructions
3. **[Usage Guide](../README.md#usage-guide)** - How to use each feature

### For Developers
1. **[Project Structure](../README.md#project-structure)** - Code organization
2. **[Development Guide](../README.md#development)** - Local development setup
3. **[API Integration](API_INTEGRATION.md)** - External API usage

## 📖 Feature Documentation

### Financial Features
- **[Complete Feature List](FEATURES.md)** - All 100+ features explained
- **[Multi-Currency Guide](../MULTI_CURRENCY_GUIDE.md)** - Currency management
- **[Currency API Guide](../CURRENCY_API_GUIDE.md)** - Live exchange rates

### Core Modules
- **Accounts** - Manage bank accounts, cards, cash
- **Categories** - Organize income/expenses
- **Transactions** - Track financial activity
- **Subscriptions** - Recurring payments
- **Budgets** - Spending limits
- **Exchange Rates** - Currency conversion

## 🔧 Technical Documentation

### API Integration
- **[API Integration Guide](API_INTEGRATION.md)** - finans.truncgil.com integration
- **Endpoint:** https://finans.truncgil.com/v4/today.json
- **Features:** Live exchange rates, auto-updates, fallback handling

### Database
- **Schema:** See [README.md](../README.md#database-schema)
- **Migrations:** `database/migrations/`
- **Seeders:** `database/seeders/`
- **Relationships:** Fully documented in models

### Commands
```bash
# Update exchange rates
php artisan rates:update

# Setup application
composer setup

# Run development server
composer dev

# Update rates via composer
composer update-rates
```

## 📊 Dashboard Widgets

### Stats Overview
- Total Balance (all accounts in TRY)
- Total Income (all-time)
- Total Expenses (all-time)
- Net Balance (income - expenses)

### Charts & Analytics
- **Income vs Expense Chart** - 12-month trend
- **Upcoming Subscriptions** - Next 14 days
- **Latest Transactions** - Last 10 transactions

## 🔐 Security

### Authentication
- Filament built-in auth
- Password hashing (bcrypt)
- Session management
- CSRF protection

### Data Protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Input validation
- Encrypted cookies

## 🌍 Multi-Currency

### Base Currency
- **TRY** (Turkish Lira) - All reporting in TRY
- Easily changeable if needed

### Supported Currencies
- TRY, USD, EUR, GBP, JPY
- Add more from API (60+ available)

### Exchange Rates
- Live API updates
- Daily auto-updates (09:00)
- Manual refresh available
- Historical rate preservation

## 🔄 Subscription System

### Features
- Recurring billing tracking
- Due date alerts
- Auto-payment flags
- Category linking
- Transaction history

### Alerts
- Overdue (red)
- Due soon - 7 days (yellow)
- Upcoming (green)

## 🛠️ Development

### Running Locally
```bash
# All services
composer dev

# Individual services
php artisan serve          # Web server
php artisan queue:work     # Queue worker
npm run dev                # Vite dev server
php artisan schedule:work  # Scheduler
```

### Code Quality
```bash
# Format code
./vendor/bin/pint

# Run tests
php artisan test

# Clear cache
php artisan optimize:clear
```

## 📦 Dependencies

### PHP Packages
- **Laravel 12.0** - Framework
- **Filament 4.2** - Admin panel
- **Guzzle** - HTTP client

### Frontend
- **Tailwind CSS 4.0** - Styling
- **Vite 7.0** - Build tool
- **Alpine.js** - (via Filament) Interactivity

## 🗺️ Architecture

### Design Patterns
- **Repository Pattern** - Via Eloquent
- **Service Layer** - Business logic (CurrencyService)
- **Observer Pattern** - Model events
- **Factory Pattern** - Model factories

### Code Organization
```
app/
├── Console/Commands/      # Artisan commands
├── Filament/             # Admin panel
│   ├── Resources/        # CRUD pages
│   └── Widgets/          # Dashboard widgets
├── Models/               # Database models
├── Services/             # Business logic
└── Providers/            # Service providers
```

## 📞 Support & Help

### Resources
1. Check this documentation
2. Review [README.md](../README.md)
3. Check Laravel [official docs](https://laravel.com/docs)
4. Check Filament [official docs](https://filamentphp.com/docs)

### Common Issues
- **Can't login?** - Run migrations and seed
- **Rates not updating?** - Check internet connection
- **Assets not loading?** - Run `npm run build`

## 🎯 Quick Reference

### Default Credentials
- **Email:** admin@admin.com
- **Password:** password
- **Panel:** `/admin`

### Important Commands
```bash
composer setup           # Initial setup
php artisan rates:update # Update exchange rates
composer dev             # Start development
php artisan migrate      # Run migrations
php artisan db:seed      # Seed data
```

### File Locations
- **Models:** `app/Models/`
- **Resources:** `app/Filament/Resources/`
- **Widgets:** `app/Filament/Widgets/`
- **Services:** `app/Services/`
- **Migrations:** `database/migrations/`

## 🆕 What's New

### Latest Features
- ✅ Live exchange rate API integration
- ✅ Subscription management system
- ✅ Multi-currency transactions
- ✅ Upcoming subscriptions dashboard widget
- ✅ Automatic daily rate updates
- ✅ Transaction-subscription linking

## 🔮 Roadmap

### Planned Features
- PDF export
- CSV/Excel import/export
- Email notifications
- Multi-user support
- Advanced analytics
- Mobile app

---

**Documentation Version:** 1.0
**Last Updated:** November 12, 2025
**Application Version:** 1.0.0

For questions or issues, please check the main [README.md](../README.md) or create an issue on GitHub.

