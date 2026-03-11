@extends('layouts.guest')

@section('title', 'Ожидание проверки')

@push('styles')
<style>
    .status-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }

    .status-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(245, 158, 11, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--space-lg);
        position: relative;
    }

    .status-icon-wrapper::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid rgba(245, 158, 11, 0.15);
        animation: pulse-ring 2s ease-out infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.3); opacity: 0; }
    }

    .status-icon-wrapper svg {
        width: 36px;
        height: 36px;
        color: var(--warning);
    }

    .status-header h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
        letter-spacing: -0.02em;
    }

    .status-header p {
        font-size: 0.875rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .status-list {
        display: flex;
        flex-direction: column;
        margin-bottom: var(--space-xl);
    }

    .status-item {
        display: flex;
        gap: var(--space-md);
        padding: var(--space-md) 0;
        position: relative;
    }

    .status-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px;
        top: 48px;
        bottom: 0;
        width: 2px;
        background: var(--border-subtle);
    }

    .status-item.done:not(:last-child)::after {
        background: var(--primary);
    }

    .status-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .status-item.done .status-dot {
        background: rgba(34, 197, 94, 0.15);
    }

    .status-item.done .status-dot svg {
        width: 16px;
        height: 16px;
        color: var(--primary);
    }

    .status-item.current .status-dot {
        background: rgba(245, 158, 11, 0.15);
    }

    .status-item.current .status-dot svg {
        width: 16px;
        height: 16px;
        color: var(--warning);
    }

    .status-item.pending .status-dot {
        background: var(--bg-input);
        border: 2px solid var(--border-input);
    }

    .status-item.pending .status-dot svg {
        width: 14px;
        height: 14px;
        color: var(--text-muted);
    }

    .status-content {
        flex: 1;
        padding-top: 4px;
    }

    .status-content h3 {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .status-item.pending .status-content h3 {
        color: var(--text-muted);
    }

    .status-content p {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .status-item.current .status-content p {
        color: var(--warning);
    }

    .next-steps {
        background: var(--bg-input);
        border: 1px solid var(--border-input);
        border-radius: var(--radius-md);
        padding: var(--space-md);
        margin-bottom: var(--space-lg);
    }

    .next-steps h3 {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: var(--space-sm);
    }

    .next-steps p {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .next-steps a {
        color: var(--accent-blue);
        font-weight: 500;
    }
</style>
@endpush

@section('content')
    <!-- Status header -->
    <div class="status-header animate-in animate-in-delay-1">
        <div class="status-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <h1>Ожидание проверки</h1>
        <p>Ваша заявка на рассмотрении. Мы уведомим вас о результате.</p>
    </div>

    <!-- Timeline -->
    <div class="status-list animate-in animate-in-delay-2">
        <div class="status-item done">
            <div class="status-dot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="status-content">
                <h3>Профиль заполнен</h3>
                <p>Готово</p>
            </div>
        </div>

        <div class="status-item done">
            <div class="status-dot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="status-content">
                <h3>Заявка в команду</h3>
                <p>Отправлена</p>
            </div>
        </div>

        <div class="status-item current">
            <div class="status-dot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="status-content">
                <h3>Верификация врача</h3>
                <p>Документ на проверке</p>
            </div>
        </div>

        <div class="status-item pending">
            <div class="status-dot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
            <div class="status-content">
                <h3>Активация аккаунта</h3>
                <p>Доступно после верификации</p>
            </div>
        </div>
    </div>

    <!-- What to do -->
    <div class="next-steps animate-in animate-in-delay-3">
        <h3>Что дальше?</h3>
        <p>Проверка документов занимает до 24 часов. После одобрения вы получите полный доступ к платформе.</p>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-secondary animate-in animate-in-delay-4" style="width:100%;">
        Перейти в личный кабинет
    </a>

    <div class="auth-footer animate-in animate-in-delay-4">
        Регистрация завершена
    </div>
@endsection
