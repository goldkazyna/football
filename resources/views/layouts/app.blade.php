<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Личный кабинет') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .desktop-nav{display:none}
        @media(min-width:768px){
            .desktop-nav{display:flex;align-items:center;gap:4px;margin:0 auto;padding:var(--space-sm) var(--space-md);max-width:900px;}
            .desktop-nav a{display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);font-size:0.8125rem;font-weight:600;color:var(--text-muted);text-decoration:none;transition:all var(--transition-fast);white-space:nowrap}
            .desktop-nav a:hover{background:rgba(255,255,255,0.04);color:var(--text-secondary)}
            .desktop-nav a.active{background:rgba(34,197,94,0.1);color:var(--primary)}
            .desktop-nav a svg{width:18px;height:18px;flex-shrink:0}
            .desktop-nav-disabled{display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-md);font-size:0.8125rem;font-weight:600;color:var(--text-muted);opacity:0.4;cursor:not-allowed;white-space:nowrap}
            .desktop-nav-disabled svg{width:18px;height:18px;flex-shrink:0}
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
                <a href="{{ route('dashboard') }}" class="site-header-logo">
                    <img src="{{ asset('img/logo.jpeg') }}" alt="Лого">
                    <span>Врачи ФК</span>
                </a>
                <div class="site-header-actions" style="display:flex;align-items:center;gap:var(--space-sm);">
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:0.75rem;font-family:inherit;padding:4px 8px;border-radius:var(--radius-sm);transition:color var(--transition-fast);" onmouseover="this.style.color='var(--error)'" onmouseout="this.style.color='var(--text-muted)'">Выйти</button>
                    </form>
                    <div style="width:32px;height:32px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:0.75rem;color:var(--text-muted);font-weight:700;">
                        {{ mb_substr(auth()->user()->name ?? 'U', 0, 2) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Desktop Navigation --}}
        @php $verified = auth()->user()->verification_status === 'approved' || auth()->user()->isSuperAdmin(); @endphp
        <nav class="desktop-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Главная
            </a>
            @if($verified)
                <a href="{{ route('tournaments.index') }}" class="{{ request()->routeIs('tournaments.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
                    Турниры
                </a>
                <a href="{{ route('my-team') }}" class="{{ request()->routeIs('my-team*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Команда
                </a>
            @else
                <span class="desktop-nav-disabled">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
                    Турниры
                </span>
                <span class="desktop-nav-disabled">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Команда
                </span>
            @endif
            <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Профиль
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

        {{-- Bottom Navigation --}}
        <nav class="bottom-nav">
            <div class="bottom-nav-inner">
                <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Главная
                </a>
                @if($verified)
                    <a href="{{ route('tournaments.index') }}" class="bottom-nav-item {{ request()->routeIs('tournaments.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
                        Турниры
                    </a>
                    <a href="{{ route('my-team') }}" class="bottom-nav-item {{ request()->routeIs('my-team*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        Команда
                    </a>
                @else
                    <span class="bottom-nav-item" style="opacity:0.4;cursor:not-allowed;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
                        Турниры
                    </span>
                    <span class="bottom-nav-item" style="opacity:0.4;cursor:not-allowed;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        Команда
                    </span>
                @endif
                <a href="{{ route('profile.show') }}" class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Профиль
                </a>
            </div>
        </nav>

    </div>

    @stack('scripts')
</body>
</html>
