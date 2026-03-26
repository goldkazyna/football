@extends('layouts.guest')

@section('title', 'Вход')

@push('styles')
<style>
    .login-header {
        text-align: center;
        margin-bottom: var(--space-xl);
    }
    .login-header h1 {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .login-header p {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    .login-actions {
        display: flex;
        flex-direction: column;
        gap: var(--space-md);
    }
    .login-footer {
        margin-top: var(--space-lg);
        text-align: center;
    }
    .login-footer p {
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.7;
    }
    .login-footer a {
        color: var(--accent-blue);
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loginForm', () => ({
        iin: '',
        password: '',
        step: 'iin',
        loading: false,
        errorMsg: '',

        init() {
            this.iin = '{{ old("iin", "") }}'.replace(/[^0-9]/g, '').substring(0, 12);

            @if($errors->has('password'))
                this.step = 'password';
            @endif
        },

        get iinComplete() {
            return this.iin.length === 12;
        },

        onInput(e) {
            let raw = e.target.value.replace(/[^0-9]/g, '');
            this.iin = raw.substring(0, 12);
            if (this.step !== 'iin') {
                this.step = 'iin';
                this.password = '';
                this.errorMsg = '';
            }
            this.$nextTick(() => { e.target.value = this.iin; });
        },

        async checkIin() {
            if (!this.iinComplete) return;

            this.loading = true;
            this.errorMsg = '';

            try {
                const res = await fetch('{{ route("login.checkIin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ iin: this.iin })
                });

                const data = await res.json();

                if (data.status === 'password') {
                    this.step = 'password';
                    this.$nextTick(() => this.$refs.passwordInput?.focus());
                } else if (data.status === 'register' || data.status === 'login') {
                    this.$refs.form.submit();
                } else {
                    this.errorMsg = 'Ваш ИИН не найден. Обратитесь к администратору.';
                }
            } catch (e) {
                this.errorMsg = 'Ошибка соединения. Попробуйте ещё раз.';
            }

            this.loading = false;
        },

        submitForm(e) {
            if (this.step === 'iin') {
                e.preventDefault();
                this.checkIin();
            }
        }
    }));
});
</script>
@endpush

@section('content')
    <div class="login-header animate-in animate-in-delay-2">
        <h1>Добро пожаловать</h1>
        <p>Вход и регистрация на турнир</p>
    </div>

    <form x-data="loginForm" x-ref="form" action="{{ route('login.iin') }}" method="POST" @submit="submitForm($event)" class="animate-in animate-in-delay-3">
        @csrf
        <input type="hidden" name="iin" :value="iin">

        <div class="form-group">
            <label class="form-label">ИИН</label>
            <input
                type="text"
                class="form-input"
                placeholder="Введите 12-значный ИИН"
                inputmode="numeric"
                autocomplete="off"
                maxlength="12"
                :value="iin"
                @input="onInput($event)"
                required
            >
            @error('iin')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
            <div x-show="errorMsg" x-text="errorMsg" x-cloak style="color:var(--error);font-size:0.75rem;margin-top:4px;"></div>
        </div>

        <div x-show="step === 'password'" x-transition x-cloak class="form-group">
            <label class="form-label">Пароль</label>
            <input
                type="password"
                name="password"
                x-ref="passwordInput"
                x-model="password"
                class="form-input @error('password') is-invalid @enderror"
                placeholder="Введите пароль"
            >
            @error('password')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading || !iinComplete">
                <template x-if="loading">
                    <span>Проверяем...</span>
                </template>
                <template x-if="!loading && step === 'iin'">
                    <span>Продолжить</span>
                </template>
                <template x-if="!loading && step === 'password'">
                    <span>Войти</span>
                </template>
            </button>
        </div>
    </form>

    <div class="login-footer animate-in animate-in-delay-4">
        <p>
            Продолжая, вы соглашаетесь с<br>
            <a href="{{ route('legal.oferta') }}">Договором-офертой</a> и <a href="{{ route('legal.politika-konfidencialnosti') }}">Политикой конфиденциальности</a>
        </p>
        <p style="margin-top: 6px;">
            <a href="{{ route('legal.opisanie-uslug') }}">Описание услуг</a> &middot; <a href="{{ route('legal.oplata-i-vozvrat') }}">Оплата и возврат</a>
        </p>
    </div>
@endsection
