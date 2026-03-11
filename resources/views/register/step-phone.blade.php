@extends('layouts.guest')

@section('title', 'Регистрация — Номер телефона')

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

    .phone-masked-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .phone-masked-wrapper .phone-prefix-label {
        position: absolute;
        left: 16px;
        font-size: 0.9375rem;
        color: var(--text-primary);
        pointer-events: none;
        font-weight: 500;
    }
    .phone-masked-wrapper .form-input {
        padding-left: 46px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    if (!Alpine.data._phoneMaskRegistered) {
        Alpine.data._phoneMaskRegistered = true;
        Alpine.data('phoneMask', (initial) => ({
            digits: '',
            init() {
                let d = (initial || '').replace(/[^0-9]/g, '');
                if (d.length === 11 && (d[0] === '7' || d[0] === '8')) d = d.substring(1);
                this.digits = d.substring(0, 10);
            },
            get display() {
                return this.formatDisplay(this.digits);
            },
            get phone() {
                return '+7' + this.digits;
            },
            formatDisplay(d) {
                if (d.length === 0) return '';
                let out = '(' + d.substring(0, 3);
                if (d.length >= 3) out += ') ' + d.substring(3, 6);
                if (d.length >= 6) out += '-' + d.substring(6, 8);
                if (d.length >= 8) out += '-' + d.substring(8, 10);
                return out;
            },
            onInput(e) {
                let raw = e.target.value.replace(/[^0-9]/g, '');
                if (raw.length >= 11 && (raw[0] === '7' || raw[0] === '8')) raw = raw.substring(1);
                this.digits = raw.substring(0, 10);
                this.$nextTick(() => { e.target.value = this.display; });
            },
            onBackspace(e) {
                if (this.digits.length > 0) {
                    this.digits = this.digits.slice(0, -1);
                    e.target.value = this.display;
                }
            }
        }));
    }
});
</script>
@endpush

@section('content')
    <!-- Steps -->
    <div class="steps animate-in animate-in-delay-1">
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-2">
        <h1>Введите номер телефона</h1>
        <p>Укажите ваш номер для регистрации</p>
    </div>

    <!-- Form -->
    <form class="animate-in animate-in-delay-3" method="POST" action="{{ route('register.process', 'phone') }}">
        @csrf

        <div class="form-group" x-data="phoneMask('{{ old('phone', $data['phone'] ?? '') }}')">
            <label class="form-label">Номер телефона</label>
            <input type="hidden" name="phone" :value="phone">
            <div class="phone-masked-wrapper">
                <span class="phone-prefix-label">+7</span>
                <input
                    type="tel"
                    class="form-input @error('phone') is-invalid @enderror"
                    placeholder="(700) 123-45-67"
                    inputmode="numeric"
                    :value="display"
                    @input="onInput($event)"
                    @keydown.backspace.prevent="onBackspace($event)"
                >
            </div>
            @error('phone')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
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
        Шаг 1 из 5
    </div>
@endsection
