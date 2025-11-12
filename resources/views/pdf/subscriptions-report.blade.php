@extends('pdf.layout')

@section('title', 'Subscriptions Report')

@section('content')
<div class="header">
    <h1>🔄 Subscriptions Report</h1>
    <div class="subtitle">Active Recurring Payments</div>
</div>

<div class="info-box">
    <div class="info-row">
        <span class="label">Generated:</span>
        <span class="value">{{ $generated_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-row">
        <span class="label">Active Subscriptions:</span>
        <span class="value">{{ $subscriptions->count() }}</span>
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-label">Monthly Cost</div>
        <div class="stat-value">₺{{ number_format($total_monthly_cost, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Yearly Cost</div>
        <div class="stat-value">₺{{ number_format($total_yearly_cost, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Upcoming (14d)</div>
        <div class="stat-value warning">{{ $upcoming->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Overdue</div>
        <div class="stat-value expense">{{ $overdue->count() }}</div>
    </div>
</div>

@if($overdue->count() > 0)
<h2 style="color: #ef4444;">⚠️ Overdue Subscriptions</h2>
<table>
    <thead>
        <tr>
            <th>Subscription</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Days Overdue</th>
        </tr>
    </thead>
    <tbody>
        @foreach($overdue as $subscription)
        <tr>
            <td>{{ $subscription->name }}</td>
            <td>{{ $subscription->category->name }}</td>
            <td class="text-right">{{ number_format($subscription->price, 2) }} {{ $subscription->currency }}</td>
            <td>{{ $subscription->next_billing_date->format('d/m/Y') }}</td>
            <td class="text-right"><span class="badge expense">{{ abs($subscription->daysUntilBilling()) }} days</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h2>📋 All Active Subscriptions</h2>
<table>
    <thead>
        <tr>
            <th>Subscription</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Frequency</th>
            <th>Next Billing</th>
            <th class="text-center">Auto Pay</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subscriptions as $subscription)
        <tr>
            <td>{{ $subscription->name }}</td>
            <td>{{ $subscription->category->name }}</td>
            <td class="text-right amount negative">{{ number_format($subscription->price, 2) }} {{ $subscription->currency }}</td>
            <td>
                <span class="badge info">
                    Every {{ $subscription->interval_count > 1 ? $subscription->interval_count . ' ' : '' }}{{ ucfirst($subscription->interval) }}{{ $subscription->interval_count > 1 ? 's' : '' }}
                </span>
            </td>
            <td>
                {{ $subscription->next_billing_date->format('d/m/Y') }}
                @if($subscription->isDueSoon())
                    <span class="badge warning">Due Soon</span>
                @endif
            </td>
            <td class="text-center">{{ $subscription->auto_pay ? '✓' : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-20" style="background: #f3f4f6; padding: 15px;">
    <h3 style="margin-bottom: 10px;">💡 Cost Analysis</h3>
    <p><strong>Average Monthly Cost:</strong> ₺{{ number_format($total_monthly_cost, 2) }}</p>
    <p><strong>Projected Annual Cost:</strong> ₺{{ number_format($total_yearly_cost, 2) }}</p>
    <p><strong>Number of Subscriptions:</strong> {{ $subscriptions->count() }}</p>
</div>
@endsection

