@extends('pdf.layout')

@section('title', 'Category Report')

@section('content')
<div class="header">
    <h1>📊 Category Report</h1>
    <div class="subtitle">{{ $category->name }} ({{ ucfirst($category->type) }})</div>
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
    <div class="info-row">
        <span class="label">Category Type:</span>
        <span class="value">{{ ucfirst($category->type) }}</span>
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-label">Total {{ ucfirst($category->type) }}</div>
        <div class="stat-value {{ $category->type === 'income' ? 'income' : 'expense' }}">
            ₺{{ number_format($total_spent, 2) }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Transactions</div>
        <div class="stat-value">{{ $transaction_count }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Average Transaction</div>
        <div class="stat-value">
            ₺{{ $transaction_count > 0 ? number_format($total_spent / $transaction_count, 2) : '0.00' }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Budgets</div>
        <div class="stat-value">{{ $budgets->count() }}</div>
    </div>
</div>

@if($budgets->count() > 0)
<h2>📈 Active Budgets</h2>
<table style="margin-bottom: 30px;">
    <thead>
        <tr>
            <th>Budget Name</th>
            <th>Period</th>
            <th class="text-right">Budget Amount</th>
            <th class="text-right">Spent</th>
            <th class="text-right">Remaining</th>
        </tr>
    </thead>
    <tbody>
        @foreach($budgets as $budget)
        <tr>
            <td>{{ $budget->name }}</td>
            <td><span class="badge info">{{ ucfirst($budget->period) }}</span></td>
            <td class="text-right">₺{{ number_format($budget->amount, 2) }}</td>
            <td class="text-right amount negative">₺{{ number_format($total_spent, 2) }}</td>
            <td class="text-right amount {{ ($budget->amount - $total_spent) >= 0 ? 'positive' : 'negative' }}">
                ₺{{ number_format($budget->amount - $total_spent, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h2>Transaction History</h2>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Account</th>
            <th>Description</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
        <tr>
            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
            <td>{{ $transaction->account->name }}</td>
            <td>
                {{ $transaction->description ?? '—' }}
                @if($transaction->subscription)
                    <br><small style="color: #8b5cf6;">({{ $transaction->subscription->name }})</small>
                @endif
            </td>
            <td class="text-right amount {{ $category->type === 'income' ? 'positive' : 'negative' }}">
                {{ $category->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($transactions->count() === 0)
<div style="text-align: center; padding: 40px; color: #999;">
    <p style="font-size: 16px;">No transactions found for this category in the selected period.</p>
</div>
@endif
@endsection

