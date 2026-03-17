<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Футбольные турниры врачей') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="auth-page">
    <div class="bg-pattern"></div>

    <div class="auth-content">
        {{-- Logo --}}
        <div class="logo animate-in animate-in-delay-1">
            <img src="{{ asset('img/logo.jpeg') }}" alt="{{ config('app.name') }}">
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-md);font-size:0.8125rem;color:var(--success);">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-md);font-size:0.8125rem;color:var(--error);">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <div class="auth-footer">
        &copy; {{ date('Y') }} ОО «Казахстанское общество врачей любителей футбола». Все права защищены.
    </div>

    @stack('scripts')
</body>
</html>
