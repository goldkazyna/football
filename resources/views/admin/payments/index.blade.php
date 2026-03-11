@extends('layouts.admin')

@section('title', 'Управление оплатами')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-md)}
    .pay-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm)}
    .pay-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-sm);margin-bottom:6px}
    .pay-user{font-size:0.8125rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0}
    .pay-amount{font-size:0.875rem;font-weight:700;color:var(--text-primary);white-space:nowrap;flex-shrink:0}
    .pay-meta{font-size:0.6875rem;color:var(--text-muted);margin-bottom:6px}
    .pay-bottom{display:flex;align-items:center;justify-content:space-between;gap:var(--space-sm)}
    .tag{display:inline-flex;align-items:center;height:22px;padding:0 10px;border-radius:var(--radius-full);font-size:0.6875rem;font-weight:600}
    .tag-green{background:rgba(34,197,94,0.12);color:var(--primary)}
    .tag-yellow{background:rgba(245,158,11,0.12);color:var(--warning)}
    .btn-confirm{height:30px;padding:0 12px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);font-size:0.6875rem;font-weight:600;font-family:inherit;cursor:pointer;white-space:nowrap;transition:background var(--transition-fast)}
    .btn-confirm:hover{background:var(--primary-hover)}
</style>
@endpush

@section('content')
    <div class="page-title">Управление оплатами</div>

    <div class="filters" style="margin-bottom:var(--space-md)">
        <a href="{{ route('admin.payments.index', request()->except(['filter', 'page'])) }}" class="filter-btn {{ !request('filter') ? 'active' : '' }}">Все</a>
        <a href="{{ route('admin.payments.index', array_merge(request()->except('page'), ['filter' => 'paid'])) }}" class="filter-btn {{ request('filter') === 'paid' ? 'active' : '' }}">Оплачено</a>
        <a href="{{ route('admin.payments.index', array_merge(request()->except('page'), ['filter' => 'pending'])) }}" class="filter-btn {{ request('filter') === 'pending' ? 'active' : '' }}">Ожидает</a>
    </div>

    @forelse($payments as $payment)
        <div class="pay-card">
            <div class="pay-card-top">
                <span class="pay-user">{{ $payment->user->name }}</span>
                <span class="pay-amount">{{ number_format($payment->amount) }} &#8376;</span>
            </div>
            <div class="pay-meta">{{ $payment->created_at->format('d.m.Y') }}{{ $payment->description ? ' &middot; ' . $payment->description : '' }}</div>
            <div class="pay-bottom">
                @if($payment->status === 'confirmed' || $payment->status === 'paid')
                    <span class="tag tag-green">Оплачено</span>
                @elseif($payment->status === 'pending')
                    <span class="tag tag-yellow">Ожидает</span>
                    <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}">
                        @csrf
                        <button type="submit" class="btn-confirm">Подтвердить вручную</button>
                    </form>
                @else
                    <span class="tag" style="background:rgba(148,163,184,0.1);color:var(--text-muted);">{{ $payment->status }}</span>
                @endif
            </div>
        </div>
    @empty
        <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);">
            Оплаты не найдены
        </div>
    @endforelse

    @if($payments->hasPages())
        <div style="margin-top:var(--space-lg);display:flex;justify-content:center;">
            {{ $payments->withQueryString()->links() }}
        </div>
    @endif
@endsection
