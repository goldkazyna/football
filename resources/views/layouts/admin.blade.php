<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ-панель') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @push('styles')
    @endpush
    <style>
        .desktop-nav{display:none}
        @media(min-width:768px){
            .desktop-nav{display:flex;align-items:center;gap:4px;margin:0 auto;padding:var(--space-sm) var(--space-md);max-width:900px;}
            .desktop-nav a{display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);font-size:0.8125rem;font-weight:600;color:var(--text-muted);text-decoration:none;transition:all var(--transition-fast);white-space:nowrap}
            .desktop-nav a:hover{background:rgba(255,255,255,0.04);color:var(--text-secondary)}
            .desktop-nav a.active{background:rgba(34,197,94,0.1);color:var(--primary)}
            .desktop-nav a svg{width:18px;height:18px;flex-shrink:0}
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="bg-pattern"></div>
    <div class="page-wrapper has-bottom-nav">

        {{-- Header --}}
        <header class="site-header">
            <div class="site-header-inner">
                <a href="{{ route('admin.dashboard') }}" class="site-header-logo">
                    <img src="{{ asset('img/logo.jpeg') }}" alt="Logo">
                    <span>Админ-панель</span>
                </a>
                <div class="site-header-actions" style="display:flex;align-items:center;gap:var(--space-sm);">
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:0.75rem;font-family:inherit;padding:4px 8px;border-radius:var(--radius-sm);transition:color var(--transition-fast);" onmouseover="this.style.color='var(--error)'" onmouseout="this.style.color='var(--text-muted)'">Выйти</button>
                    </form>
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent-blue),#6366f1);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;">
                        {{ mb_substr(auth()->user()->name ?? 'СА', 0, 2) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Desktop Navigation --}}
        <nav class="desktop-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Панель
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Юзеры
            </a>
            <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Оплаты
            </a>
            <a href="{{ route('whitelist.index') }}" class="{{ request()->routeIs('whitelist.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Список
            </a>
            <a href="{{ route('admin.teams.index') }}" class="{{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Команды
            </a>
            <a href="{{ route('admin.tournaments.index') }}" class="{{ request()->routeIs('admin.tournaments.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
                Турниры
            </a>
            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                Настройки
            </a>
        </nav>

        {{-- Main content --}}
        <main class="page-content">
            @if(session('success'))
                <div class="warning-banner" style="background:rgba(34,197,94,0.1);border-color:rgba(34,197,94,0.2);color:var(--success);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="warning-banner" style="background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2);color:var(--error);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Bottom Navigation (mobile) --}}
        <nav class="bottom-nav">
            <div class="bottom-nav-inner">
                <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Панель
                </a>
                <a href="{{ route('admin.users.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Юзеры
                </a>
                <a href="{{ route('admin.payments.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Оплаты
                </a>
                <a href="{{ route('admin.tournaments.index') }}" class="bottom-nav-item {{ request()->routeIs('admin.tournaments.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
                    Турниры
                </a>
                <a href="{{ route('admin.settings') }}" class="bottom-nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    Настройки
                </a>
            </div>
        </nav>

    </div>

    @stack('scripts')
</body>
</html>
