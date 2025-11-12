@extends('pdf.layout')

@section('title', 'Financial Summary Report')

@section('content')
<div class="header">
    <h1>💰 Financial Summary Report</h1>
    <div class="subtitle">Personal Finance Manager</div>
</div>

<div class="info-box">
    <div class="info-row">
        <span class="label">Period:</span>
        <span class="value">{{ $start_date->format('d/m/Y') }} - {{ $end_date->format('d/m/Y') }}</span>
    </div>
    <div class="info-row">
        <span class="label">Generated:</span>
        <span class="value">{{ $generated_at->format('d/m/Y H:i') }}</span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Balance</div>
        <div class="stat-value">₺{{ number_format($total_balance, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Income</div>
        <div class="stat-value income">₺{{ number_format($income, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Expenses</div>
        <div class="stat-value expense">₺{{ number_format($expense, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Net Balance</div>
        <div class="stat-value {{ ($income - $expense) >= 0 ? 'income' : 'expense' }}">
            ₺{{ number_format($income - $expense, 2) }}
        </div>
    </div>
</div>

<h2>📊 Accounts Overview</h2>
<table>
    <thead>
        <tr>
            <th>Account Name</th>
            <th>Type</th>
            <th>Currency</th>
            <th class="text-right">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($accounts as $account)
        <tr>
            <td>{{ $account->name }}</td>
            <td><span class="badge info">{{ ucwords(str_replace('_', ' ', $account->type)) }}</span></td>
            <td>{{ $account->currency }}</td>
            <td class="text-right amount">{{ number_format($account->current_balance, 2) }} {{ $account->currency }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h2>💸 Expense Breakdown by Category</h2>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th class="text-center">Transactions</th>
            <th class="text-right">Total Amount</th>
            <th class="text-right">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories_expense as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td class="text-center">{{ $category->transactions_count }}</td>
            <td class="text-right amount negative">₺{{ number_format($category->transactions_sum_amount, 2) }}</td>
            <td class="text-right">{{ $expense > 0 ? number_format(($category->transactions_sum_amount / $expense) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
        <tr style="border-top: 2px solid #d1d5db; font-weight: bold;">
            <td>TOTAL</td>
            <td class="text-center">{{ $transactions_count }}</td>
            <td class="text-right">₺{{ number_format($expense, 2) }}</td>
            <td class="text-right">100%</td>
        </tr>
    </tbody>
</table>

@if($categories_income->count() > 0)
<h2>💵 Income Sources</h2>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th class="text-right">Total Amount</th>
            <th class="text-right">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories_income as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td class="text-right amount positive">₺{{ number_format($category->transactions_sum_amount, 2) }}</td>
            <td class="text-right">{{ $income > 0 ? number_format(($category->transactions_sum_amount / $income) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection

