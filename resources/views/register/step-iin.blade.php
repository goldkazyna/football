@extends('layouts.guest')

@section('title', 'Регистрация — ИИН')

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
        line-height: 1.5;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-top: var(--space-lg);
        transition: color var(--transition-fast);
    }

    .back-link:hover {
        color: var(--text-secondary);
    }

    .back-link svg {
        width: 16px;
        height: 16px;
    }
</style>
@endpush

@section('content')
    <!-- Steps -->
    <div class="steps animate-in animate-in-delay-1">
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-2">
        <h1>Введите ИИН</h1>
        <p>Укажите ваш индивидуальный идентификационный номер</p>
    </div>

    <!-- Form -->
    <form class="animate-in animate-in-delay-3" method="POST" action="{{ route('register.process', 'iin') }}"
          x-data="{ iin: '{{ old('iin', $data['iin'] ?? '') }}' }">
        @csrf

        <div class="form-group">
            <label class="form-label">ИИН</label>
            <input type="hidden" name="iin" :value="iin">
            <input
                type="text"
                class="form-input @error('iin') is-invalid @enderror"
                placeholder="Введите 12-значный ИИН"
                inputmode="numeric"
                autocomplete="off"
                maxlength="12"
                :value="iin"
                @input="iin = $event.target.value.replace(/[^0-9]/g, '').substring(0, 12); $nextTick(() => { $event.target.value = iin; })"
            >
            @error('iin')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" :disabled="iin.length !== 12">
            Продолжить
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Назад ко входу
            </a>
        </div>
    </form>

    <div class="auth-footer animate-in animate-in-delay-4">
        Шаг 1 из 6
    </div>
@endsection
