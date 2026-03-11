@extends('layouts.app')

@section('title', 'Оплата')

@push('styles')
<style>
    .payment-page{display:flex;flex-direction:column;align-items:center;padding:var(--space-xl) 0}
    .payment-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-lg);width:100%;max-width:400px;text-align:center}
    .payment-icon{width:56px;height:56px;border-radius:50%;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-md)}
    .payment-icon svg{width:24px;height:24px;color:var(--accent-blue)}
    .payment-title{font-size:1.125rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .payment-desc{font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-lg)}
    .payment-amount{font-size:2rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-lg)}
    .payment-amount span{font-size:1rem;color:var(--text-secondary);font-weight:600}
    .payment-details{text-align:left;margin-bottom:var(--space-lg)}
    .payment-detail-row{display:flex;justify-content:space-between;align-items:center;padding:var(--space-sm) 0;border-bottom:1px solid var(--border-subtle);font-size:0.8125rem}
    .payment-detail-row:last-child{border-bottom:none}
    .payment-detail-label{color:var(--text-muted)}
    .payment-detail-value{color:var(--text-primary);font-weight:600}
</style>
@endpush

@section('content')
    <div class="payment-page">
        <div class="payment-card">
            <div class="payment-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="payment-title">Членский взнос</div>
            <div class="payment-desc">Оплата членского взноса для участия в лиге</div>

            <div class="payment-amount">
                {{ number_format($amount ?? 15000) }} <span>&#8376;</span>
            </div>

            <div class="payment-details">
                <div class="payment-detail-row">
                    <span class="payment-detail-label">Плательщик</span>
                    <span class="payment-detail-value">{{ auth()->user()->name }}</span>
                </div>
                @if(isset($description))
                <div class="payment-detail-row">
                    <span class="payment-detail-label">Назначение</span>
                    <span class="payment-detail-value">{{ $description }}</span>
                </div>
                @endif
            </div>

            <form method="POST" action="{{ route('payment.process') }}">
                @csrf
                <input type="hidden" name="amount" value="{{ $amount ?? 15000 }}">
                <button type="submit" class="btn btn-primary">Оплатить</button>
            </form>
        </div>
    </div>
@endsection
