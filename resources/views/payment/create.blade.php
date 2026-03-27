@extends('layouts.app')

@section('title', 'Оплата')

@push('styles')
<style>
    .pay-header{text-align:center;margin-bottom:var(--space-xl)}
    .pay-header h1{font-size:1.375rem;font-weight:800;color:var(--text-primary);margin-bottom:4px}
    .pay-header p{font-size:0.875rem;color:var(--text-secondary)}

    .plan-cards{display:flex;flex-direction:column;gap:var(--space-md);margin-bottom:var(--space-lg)}

    .plan-card{position:relative;background:var(--bg-card);border:2px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);cursor:pointer;transition:all var(--transition-fast)}
    .plan-card:hover{border-color:rgba(59,130,246,0.3)}
    .plan-card.selected{border-color:var(--primary);background:rgba(34,197,94,0.03)}

    .plan-card input[type="radio"]{display:none}

    .plan-badge{position:absolute;top:-10px;right:var(--space-md);background:var(--primary);color:#fff;font-size:0.625rem;font-weight:700;padding:3px 10px;border-radius:var(--radius-full);text-transform:uppercase;letter-spacing:0.05em}

    .plan-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-sm)}
    .plan-name{font-size:1rem;font-weight:700;color:var(--text-primary)}
    .plan-check{width:22px;height:22px;border-radius:50%;border:2px solid var(--border-input);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all var(--transition-fast)}
    .plan-card.selected .plan-check{border-color:var(--primary);background:var(--primary)}
    .plan-check svg{width:12px;height:12px;color:#fff;opacity:0;transition:opacity var(--transition-fast)}
    .plan-card.selected .plan-check svg{opacity:1}

    .plan-price{display:flex;align-items:baseline;gap:4px;margin-bottom:4px}
    .plan-price-value{font-size:1.75rem;font-weight:800;color:var(--text-primary);line-height:1}
    .plan-price-unit{font-size:0.8125rem;color:var(--text-muted);font-weight:500}

    .plan-meta{font-size:0.75rem;color:var(--text-muted)}
    .plan-save{color:var(--primary);font-weight:600}

    .pay-summary{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-lg)}
    .pay-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border-subtle);font-size:0.8125rem}
    .pay-row:last-child{border-bottom:none}
    .pay-row-label{color:var(--text-muted)}
    .pay-row-value{color:var(--text-primary);font-weight:600}
    .pay-row-total .pay-row-value{font-size:1.125rem;font-weight:800;color:var(--primary)}

    .pay-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:var(--radius-md);padding:var(--space-sm) var(--space-md);color:var(--error);font-size:0.8125rem;margin-bottom:var(--space-md)}
</style>
@endpush

@section('content')
    <div class="pay-header">
        <h1>Выберите подписку</h1>
        <p>Для участия в лиге необходима активная подписка</p>
    </div>

    @if(session('error'))
        <div class="pay-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('payment.initiate') }}" x-data="{
        plan: 'yearly',
        plans: {
            monthly: { price: {{ $plans['monthly']['amount'] / 100 }}, perMonth: {{ $plans['monthly']['amount'] / 100 }}, period: '1 месяц' },
            yearly: { price: {{ $plans['yearly']['amount'] / 100 }}, perMonth: {{ intval($plans['yearly']['amount'] / 100 / 12) }}, period: '12 месяцев' }
        },
        get current() { return this.plans[this.plan]; },
        formatPrice(n) { return new Intl.NumberFormat('ru-RU').format(n); }
    }">
        @csrf

        <div class="plan-cards">
            {{-- Yearly --}}
            <label class="plan-card" :class="{ selected: plan === 'yearly' }" @click="plan = 'yearly'">
                <input type="radio" name="plan" value="yearly" :checked="plan === 'yearly'">
                <div class="plan-badge">Выгодно</div>
                <div class="plan-top">
                    <div class="plan-name">Годовая</div>
                    <div class="plan-check">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
                <div class="plan-price">
                    <span class="plan-price-value">3 500</span>
                    <span class="plan-price-unit">&#8376;/мес</span>
                </div>
                <div class="plan-meta">42 000 &#8376; за 12 месяцев &middot; <span class="plan-save">Экономия 6 000 &#8376;</span></div>
            </label>

            {{-- Monthly --}}
            <label class="plan-card" :class="{ selected: plan === 'monthly' }" @click="plan = 'monthly'">
                <input type="radio" name="plan" value="monthly" :checked="plan === 'monthly'">
                <div class="plan-top">
                    <div class="plan-name">Ежемесячная</div>
                    <div class="plan-check">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
                <div class="plan-price">
                    <span class="plan-price-value">4 000</span>
                    <span class="plan-price-unit">&#8376;/мес</span>
                </div>
                <div class="plan-meta">Оплата каждый месяц</div>
            </label>
        </div>

        {{-- Summary --}}
        <div class="pay-summary">
            <div class="pay-row">
                <span class="pay-row-label">Плательщик</span>
                <span class="pay-row-value">{{ $user->name }}</span>
            </div>
            <div class="pay-row">
                <span class="pay-row-label">Тариф</span>
                <span class="pay-row-value" x-text="plan === 'yearly' ? 'Годовая' : 'Ежемесячная'"></span>
            </div>
            <div class="pay-row">
                <span class="pay-row-label">Период</span>
                <span class="pay-row-value" x-text="current.period"></span>
            </div>
            <div class="pay-row pay-row-total">
                <span class="pay-row-label">К оплате</span>
                <span class="pay-row-value" x-text="formatPrice(current.price) + ' ₸'"></span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;height:52px;font-size:1rem;">
            Оплатить <span x-text="formatPrice(current.price) + ' ₸'"></span>
        </button>
    </form>
@endsection
