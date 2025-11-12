@extends('pdf.layout')

@section('title', 'Account Statement')

@section('content')
<div class="header">
    <h1>🏦 Account Statement</h1>
    <div class="subtitle">{{ $account->name }}</div>
</div>

<div class="info-box">
    <div class="info-row">
        <span class="label">Account Type:</span>
        <span class="value">{{ ucwords(str_replace('_', ' ', $account->type)) }}</span>
    </div>
    <div class="info-row">
        <span class="label">Currency:</span>
        <span class="value">{{ $account->currency }}</span>
    </div>
    <div class="info-row">
        <span class="label">Period:</span>
        <span class="value">{{ $start_date->format('d/m/Y') }} - {{ $end_date->format('d/m/Y') }}</span>
    </div>
    <div class="info-row">
        <span class="label">Generated:</span>
        <span class="value">{{ $generated_at->format('d/m/Y H:i') }}</span>
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-label">Starting Balance</div>
        <div class="stat-value">{{ number_format($starting_balance, 2) }} {{ $account->currency }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Income</div>
        <div class="stat-value income">+{{ number_format($total_income, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Expenses</div>
        <div class="stat-value expense">-{{ number_format($total_expense, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Current Balance</div>
        <div class="stat-value">{{ number_format($ending_balance, 2) }} {{ $account->currency }}</div>
    </div>
</div>

<h2>Transaction History</h2>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Description</th>
            <th>Type</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
        <tr>
            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
            <td>{{ $transaction->category->name }}</td>
            <td>
                {{ $transaction->description ?? '—' }}
                @if($transaction->subscription)
                    <br><small style="color: #8b5cf6;">({{ $transaction->subscription->name }})</small>
                @endif
            </td>
            <td>
                <span class="badge {{ $transaction->type }}">
                    {{ ucfirst($transaction->type) }}
                </span>
            </td>
            <td class="text-right amount {{ $transaction->type === 'income' ? 'positive' : 'negative' }}">
                {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($transactions->count() === 0)
<div style="text-align: center; padding: 40px; color: #999;">
    <p style="font-size: 16px;">No transactions found for this period.</p>
</div>
@endif
@endsection

