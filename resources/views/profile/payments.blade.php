@extends('layouts.app')

@section('title', 'История оплат')

@push('styles')
<style>
    .payment-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-sm);position:relative;overflow:hidden}
    .payment-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .payment-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-sm)}
    .payment-amount{font-size:1.125rem;font-weight:800;color:var(--text-primary)}
    .payment-status{display:inline-flex;align-items:center;gap:4px;font-size:0.6875rem;font-weight:700;text-transform:uppercase}
    .payment-status.paid{color:var(--primary)}
    .payment-status.pending{color:var(--warning)}
    .payment-status.failed{color:var(--error)}
    .payment-status-dot{width:6px;height:6px;border-radius:50%}
    .payment-status.paid .payment-status-dot{background:var(--primary)}
    .payment-status.pending .payment-status-dot{background:var(--warning)}
    .payment-status.failed .payment-status-dot{background:var(--error)}
    .payment-meta{display:flex;flex-wrap:wrap;gap:var(--space-md);font-size:0.8125rem;color:var(--text-secondary)}
    .payment-meta-item{display:flex;align-items:center;gap:4px}
    .payment-meta-item svg{width:14px;height:14px;color:var(--text-muted);flex-shrink:0}
    .empty-state{text-align:center;padding:var(--space-xl) var(--space-md);color:var(--text-muted);font-size:0.875rem}
</style>
@endpush

@section('content')

    <h1 class="heading-md mb-lg">История оплат</h1>

    @forelse($payments as $payment)
        <div class="payment-card">
            <div class="payment-top">
                <div class="payment-amount">{{ number_format($payment->amount, 0, '', ' ') }} ₸</div>
                <div class="payment-status {{ $payment->status }}">
                    <span class="payment-status-dot"></span>
                    @switch($payment->status)
                        @case('paid')
                            Оплачено
                            @break
                        @case('pending')
                            Ожидает
                            @break
                        @case('failed')
                            Ошибка
                            @break
                        @default
                            {{ $payment->status }}
                    @endswitch
                </div>
            </div>
            <div class="payment-meta">
                <div class="payment-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $payment->created_at->translatedFormat('d M Y') }}
                </div>
                @if($payment->period)
                    <div class="payment-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Период: {{ $payment->period }}
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <p>Нет оплат</p>
        </div>
    @endforelse

    @if($payments->hasPages())
        <div style="margin-top:var(--space-lg)">
            {{ $payments->links() }}
        </div>
    @endif

@endsection
