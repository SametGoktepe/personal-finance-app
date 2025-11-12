# 📄 PDF Reports Documentation

## Overview

The Personal Finance Manager includes a comprehensive PDF reporting system. Generate professional financial reports with one click directly from the admin panel!

## Features

### 📊 Available Reports

1. **Financial Summary** - Complete financial overview
2. **Transactions Report** - Detailed transaction history
3. **Subscriptions Report** - Recurring payments analysis
4. **Account Statement** - Individual account history (coming soon)
5. **Category Report** - Category-based spending (coming soon)

### ✨ Report Features

- **Professional Design** - Clean, readable PDF layout
- **Color-Coded** - Income (green), Expense (red)
- **Multi-Currency** - Shows both original and TRY amounts
- **Charts & Stats** - Visual data representation
- **Date Range** - Custom period selection
- **Auto-Download** - PDFs download automatically

## Accessing Reports

### Main Reports Page

Navigate to: **Finance > Reports**

This central hub provides:
- Financial Summary export
- Quick links to other report types
- Report descriptions and features
- Easy-to-use interface

## Report Types

### 1. 💰 Financial Summary Report

**Location:** Finance > Reports > "Financial Summary" button

**What It Includes:**
- **Period Overview**
  - Selected date range
  - Generation timestamp
  
- **Key Statistics**
  - Total balance (all accounts)
  - Total income
  - Total expenses
  - Net balance

- **Accounts Overview**
  - All active accounts
  - Account types
  - Current balances
  - Multi-currency support

- **Expense Breakdown**
  - Top 10 expense categories
  - Amount per category
  - Percentage distribution
  - Transaction counts

- **Income Sources**
  - All income categories
  - Amount per source
  - Percentage breakdown

**How to Generate:**
1. Go to Finance > Reports
2. Click "Financial Summary" button
3. Select start and end dates
4. Click "Export"
5. PDF downloads automatically

**File Name Format:** `financial-summary-2025-11-12.pdf`

---

### 2. 📝 Transactions Report

**Location:** Finance > Transactions > "Export PDF" button

**What It Includes:**
- **Summary Statistics**
  - Total income
  - Total expenses
  - Net balance
  - Transaction count

- **Transaction List**
  - Date
  - Account
  - Category
  - Description
  - Type (Income/Expense badge)
  - Amount (with currency)

- **Filter Options**
  - Date range selection
  - Type filter (All/Income/Expense)

**How to Generate:**
1. Go to Finance > Transactions
2. Click "Export PDF" button (green, top right)
3. Select:
   - Start date
   - End date
   - Transaction type
4. Click "Export"
5. PDF downloads

**File Name Format:** `transactions-2025-11-01-to-2025-11-30.pdf`

---

### 3. 🔄 Subscriptions Report

**Location:** Finance > Subscriptions > "Export PDF" button

**What It Includes:**
- **Cost Analysis**
  - Total monthly cost
  - Projected yearly cost
  - Number of active subscriptions
  - Upcoming payments count
  - Overdue count

- **Overdue Alerts** (if any)
  - Subscription name
  - Category
  - Amount
  - Due date
  - Days overdue

- **All Active Subscriptions**
  - Subscription details
  - Billing frequency
  - Next billing date
  - Auto-payment status
  - Category

**How to Generate:**
1. Go to Finance > Subscriptions
2. Click "Export PDF" button (green, top right)
3. Confirm export
4. PDF downloads

**File Name Format:** `subscriptions-report-2025-11-12.pdf`

## PDF Design

### Layout & Style

- **Header:** Branded with app name and report title
- **Color Scheme:** 
  - Primary: Amber (#f59e0b)
  - Income: Green (#10b981)
  - Expense: Red (#ef4444)
  - Info: Blue (#3b82f6)

- **Typography:** 
  - Font: DejaVu Sans (supports Unicode, including ₺)
  - Clear hierarchy
  - Readable sizes

- **Tables:**
  - Striped rows
  - Clear headers
  - Right-aligned numbers
  - Responsive columns

- **Footer:**
  - Generation timestamp
  - Page numbers
  - Application branding

## Technical Details

### PDF Service

Located at: `app/Services/PdfReportService.php`

**Available Methods:**
```php
// Financial summary
generateFinancialSummary(?Carbon $startDate, ?Carbon $endDate)

// Transactions report
generateTransactionsReport(?Carbon $startDate, ?Carbon $endDate, ?string $type)

// Subscriptions report
generateSubscriptionsReport()

// Account statement
generateAccountStatement(Account $account, ?Carbon $startDate, ?Carbon $endDate)

// Category report
generateCategoryReport(Category $category, ?Carbon $startDate, ?Carbon $endDate)
```

### Template Files

Located at: `resources/views/pdf/`

- `layout.blade.php` - Base layout with styles
- `financial-summary.blade.php` - Financial summary template
- `transactions-report.blade.php` - Transactions template
- `subscriptions-report.blade.php` - Subscriptions template
- `account-statement.blade.php` - Account statement template
- `category-report.blade.php` - Category report template

### PDF Library

**Package:** barryvdh/laravel-dompdf
**Version:** ^3.1
**Engine:** Dompdf
**Paper Size:** A4
**Orientation:** Portrait (most), Landscape (transactions)

## Usage Examples

### From Code

```php
use App\Services\PdfReportService;

// Generate financial summary
$pdfService = app(PdfReportService::class);
$pdf = $pdfService->generateFinancialSummary(
    now()->startOfMonth(),
    now()->endOfMonth()
);

// Download
return $pdf->download('report.pdf');

// Stream to browser
return $pdf->stream();

// Save to file
$pdf->save(storage_path('app/reports/financial-summary.pdf'));
```

### From Artisan

You can create custom commands:

```bash
php artisan make:command GenerateMonthlyReport
```

Then use PdfReportService in the command.

## Customization

### Changing Colors

Edit `resources/views/pdf/layout.blade.php`:

```css
.header {
    background: #your-color; /* Change header color */
}

.stat-value.income {
    color: #your-green; /* Change income color */
}
```

### Adding Your Logo

Add to header in `layout.blade.php`:

```html
<div class="header">
    <img src="data:image/png;base64,..." alt="Logo" style="height: 40px;">
    <h1>Financial Summary Report</h1>
</div>
```

### Custom Reports

Create new templates in `resources/views/pdf/`:

```php
// In PdfReportService.php
public function generateCustomReport(): \Barryvdh\DomPDF\PDF
{
    $data = [
        // Your data
    ];
    
    return Pdf::loadView('pdf.custom-report', $data)
        ->setPaper('a4', 'portrait');
}
```

## Best Practices

### 1. Date Ranges
- Default to current month for most reports
- Allow custom range for flexibility
- Validate end date >= start date

### 2. Performance
- Limit large datasets (e.g., paginate for 1000+ transactions)
- Use eager loading (`with()`) for relationships
- Cache complex calculations

### 3. Formatting
- Always format numbers with 2 decimals
- Include currency symbols
- Show both original and converted amounts
- Use consistent date format (d/m/Y)

### 4. User Experience
- Show generation timestamp
- Include report parameters
- Add summary statistics
- Use color coding effectively

## Troubleshooting

### Problem: PDF Not Downloading

**Check:**
- Browser pop-up blocker
- File download permissions
- Console errors (F12)

**Solution:**
- Allow downloads from site
- Try different browser
- Check Laravel logs

### Problem: PDF Shows Broken Layout

**Cause:** CSS not supported by dompdf

**Solution:**
- Use table-based layout
- Avoid flexbox/grid
- Use inline styles
- Keep CSS simple

### Problem: Turkish Characters Not Showing

**Solution:** Already handled!
- Using DejaVu Sans font
- Supports Unicode (₺, €, etc.)
- Pre-configured in package

### Problem: PDF Generation Slow

**Optimization:**
- Limit transactions shown
- Remove images if any
- Simplify complex calculations
- Use database indexing

## Security

### Access Control
- Only authenticated users
- Filament auth middleware
- No public PDF access
- Secure file downloads

### Data Privacy
- PDFs generated on-demand
- Not stored on server
- Downloaded directly
- No caching of sensitive data

## Future Enhancements

### Planned Features
- [ ] Account-specific statements with export button
- [ ] Category reports with export button
- [ ] Budget vs Actual comparison report
- [ ] Year-over-year comparison
- [ ] Custom logo upload
- [ ] Email report delivery
- [ ] Scheduled report generation
- [ ] Report templates management
- [ ] Charts in PDF (via Chart.js to image)

### Additional Export Formats
- [ ] CSV export
- [ ] Excel (XLSX) export
- [ ] JSON export
- [ ] HTML preview before export

## Examples

### Financial Summary Output

```
┌─────────────────────────────────┐
│ 💰 Financial Summary Report     │
│ Personal Finance Manager         │
└─────────────────────────────────┘

Period: 01/11/2025 - 30/11/2025
Generated: 12/11/2025 10:32

┌──────────┬──────────┬──────────┬──────────┐
│  Balance │  Income  │ Expenses │   Net    │
│  ₺6,500  │  ₺3,000  │  ₺280    │  ₺2,720  │
└──────────┴──────────┴──────────┴──────────┘

📊 Accounts Overview
- Main Bank: ₺5,000
- Debit Card: ₺1,000
...

💸 Expense Breakdown
- Food & Dining: ₺150 (53.6%)
- Entertainment: ₺80 (28.6%)
...
```

### Transactions Report Output

```
┌─────────────────────────────────┐
│ 📝 Transactions Report           │
│ All Transactions                 │
└─────────────────────────────────┘

Period: 01/11/2025 - 30/11/2025
Total: 4 transactions

┌──────────┬──────────┬──────────┐
│  Income  │ Expenses │   Net    │
│  ₺3,000  │  ₺280    │  ₺2,720  │
└──────────┴──────────┴──────────┘

Date       Account      Category     Type      Amount
───────────────────────────────────────────────────────
07/11/25   Bank         Salary      INCOME    +₺3,000
09/11/25   Cash         Food        EXPENSE   -₺150
...
```

## Quick Reference

### Keyboard Shortcuts
- None currently (all via UI)

### URL Patterns
- Reports Hub: `/admin/reports`
- Transactions: `/admin/transactions`
- Subscriptions: `/admin/subscriptions`

### File Locations
- Service: `app/Services/PdfReportService.php`
- Templates: `resources/views/pdf/*.blade.php`
- Layout: `resources/views/pdf/layout.blade.php`

### Package Documentation
- [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)
- [Dompdf Library](https://github.com/dompdf/dompdf)

---

**Pro Tip:** Generate reports at the end of each month for your financial records! 📊

Enjoy your professional PDF reports! 🎉

