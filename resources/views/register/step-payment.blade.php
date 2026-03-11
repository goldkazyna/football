@extends('layouts.guest')

@section('title', 'Регистрация — Оплата')

@push('styles')
<style>
    .steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: var(--space-xl);
    }

    .step-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--border-input);
        transition: all var(--transition-base);
    }

    .step-dot.active {
        width: 24px;
        border-radius: 4px;
        background: var(--primary);
    }

    .step-dot.done {
        background: var(--primary);
    }

    .register-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }

    .register-header h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .register-header p {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .payment-card {
        background: var(--bg-input);
        border: 1px solid var(--border-input);
        border-radius: var(--radius-lg);
        padding: var(--space-xl);
        text-align: center;
        margin-bottom: var(--space-lg);
    }

    .payment-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(34, 197, 94, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--space-md);
    }

    .payment-icon svg {
        width: 28px;
        height: 28px;
        color: var(--primary);
    }

    .payment-label {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-bottom: var(--space-xs);
    }

    .payment-amount {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.03em;
        margin-bottom: 4px;
    }

    .payment-amount span {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .payment-period {
        font-size: 0.8125rem;
        color: var(--text-muted);
    }

    .payment-details {
        margin-bottom: var(--space-lg);
    }

    .payment-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-subtle);
    }

    .payment-detail:last-child {
        border-bottom: none;
    }

    .payment-detail-label {
        font-size: 0.8125rem;
        color: var(--text-muted);
    }

    .payment-detail-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .nav-buttons {
        display: flex;
        gap: var(--space-sm);
    }

    .btn-back {
        width: 48px;
        min-width: 48px;
        height: 48px;
        flex-shrink: 0;
        background: transparent;
        border: 1px solid var(--border-input);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.04);
    }

    .btn-back svg {
        width: 20px;
        height: 20px;
    }
</style>
@endpush

@section('content')
    <!-- Steps -->
    <div class="steps animate-in animate-in-delay-1">
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot active"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-1">
        <h1>Завершение регистрации</h1>
        <p>Проверьте данные и завершите регистрацию</p>
    </div>

    <!-- Summary -->
    <div class="payment-card animate-in animate-in-delay-2">
        <div class="payment-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <div class="payment-label">Всё готово</div>
    </div>

    <div class="payment-details animate-in animate-in-delay-2">
        <div class="payment-detail">
            <span class="payment-detail-label">Телефон</span>
            <span class="payment-detail-value">+7 {{ $data['phone'] ?? '' }}</span>
        </div>
        <div class="payment-detail">
            <span class="payment-detail-label">Имя</span>
            <span class="payment-detail-value">{{ $data['name'] ?? '' }}</span>
        </div>
        <div class="payment-detail">
            <span class="payment-detail-label">Специализация</span>
            <span class="payment-detail-value">{{ $data['specialization'] ?? '' }}</span>
        </div>
        <div class="payment-detail">
            <span class="payment-detail-label">Город</span>
            <span class="payment-detail-value">{{ $data['city'] ?? '' }}</span>
        </div>
        <div class="payment-detail">
            <span class="payment-detail-label">Команда</span>
            <span class="payment-detail-value">
                @if(($data['team_mode'] ?? 'join') === 'create')
                    {{ $data['team_name'] ?? 'Новая команда' }}
                @else
                    {{ $data['team_name_display'] ?? 'Выбранная команда' }}
                @endif
            </span>
        </div>
        <div class="payment-detail">
            <span class="payment-detail-label">Документ</span>
            <span class="payment-detail-value">Загружен</span>
        </div>
    </div>

    <!-- Form -->
    <form class="animate-in animate-in-delay-3" method="POST" action="{{ route('register.process', 'payment') }}">
        @csrf

        <div class="nav-buttons">
            <a href="{{ route('register.step', 'verification') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <button type="submit" class="btn btn-primary" style="flex:1;">Завершить регистрацию</button>
        </div>
    </form>

    <div class="auth-footer animate-in animate-in-delay-4">
        Шаг 5 из 5
    </div>
@endsection
