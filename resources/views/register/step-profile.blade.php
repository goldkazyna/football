@extends('layouts.guest')

@section('title', 'Регистрация — Профиль')

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

    .form-select {
        width: 100%;
        height: 48px;
        padding: 0 var(--space-md);
        background: var(--bg-input);
        border: 1px solid var(--border-input);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-family: inherit;
        font-size: 0.9375rem;
        outline: none;
        transition: all var(--transition-fast);
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
    }

    .form-select:focus {
        background-color: var(--bg-input-focus);
        border-color: var(--border-input-focus);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-select option {
        background: var(--bg-card);
        color: var(--text-primary);
    }

    .nav-buttons {
        display: flex;
        gap: var(--space-sm);
        margin-top: var(--space-lg);
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
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-1">
        <h1>Заполните профиль</h1>
        <p>Расскажите о себе</p>
    </div>

    <!-- Form -->
    <form class="animate-in animate-in-delay-3" method="POST" action="{{ route('register.process', 'profile') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Имя и фамилия</label>
            <input
                type="text"
                name="name"
                class="form-input @error('name') is-invalid @enderror"
                placeholder="Айбек Нурланов"
                value="{{ old('name', $data['name'] ?? '') }}"
            >
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Номер телефона</label>
            <input
                type="tel"
                name="phone"
                class="form-input @error('phone') is-invalid @enderror"
                placeholder="+7 (700) 123-45-67"
                value="{{ old('phone', $data['phone'] ?? '') }}"
            >
            @error('phone')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Специализация / должность</label>
            <input
                type="text"
                name="specialization"
                class="form-input @error('specialization') is-invalid @enderror"
                placeholder="Хирург, кардиолог..."
                value="{{ old('specialization', $data['specialization'] ?? '') }}"
            >
            @error('specialization')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Город</label>
            <select name="city" class="form-select @error('city') is-invalid @enderror">
                <option value="" disabled {{ old('city', $data['city'] ?? '') == '' ? 'selected' : '' }}>Выберите город</option>
                @foreach(['Алматы', 'Астана', 'Шымкент', 'Караганда', 'Актобе', 'Тараз', 'Павлодар', 'Усть-Каменогорск', 'Семей', 'Атырау', 'Костанай', 'Кызылорда', 'Петропавловск', 'Актау', 'Туркестан'] as $city)
                    <option value="{{ $city }}" {{ old('city', $data['city'] ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
            @error('city')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="nav-buttons">
            <a href="{{ route('register.step', 'password') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <button type="submit" class="btn btn-primary" style="flex:1;">Далее</button>
        </div>
    </form>

    <div class="auth-footer animate-in animate-in-delay-4">
        Шаг 3 из 6
    </div>
@endsection
