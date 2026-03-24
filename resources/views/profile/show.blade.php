@extends('layouts.app')

@section('title', 'Мой профиль')

@push('styles')
<style>
    .profile-hero{text-align:center;padding:var(--space-xl) 0 var(--space-lg)}
    .profile-avatar-lg{width:80px;height:80px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:var(--text-muted);margin:0 auto var(--space-md)}
    .profile-hero-name{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .profile-hero-badge{margin-top:var(--space-sm)}
    .info-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-xl);overflow:hidden;margin-bottom:var(--space-lg);position:relative}
    .info-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .info-row{display:flex;justify-content:space-between;align-items:center;padding:var(--space-md) var(--space-lg);border-bottom:1px solid var(--border-subtle)}
    .info-row:last-child{border-bottom:none}
    .info-label{font-size:0.8125rem;color:var(--text-muted);flex-shrink:0}
    .info-value{font-size:0.875rem;font-weight:600;color:var(--text-primary);text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;margin-left:var(--space-md)}
    .info-value .badge{vertical-align:middle}
    .sub-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-lg);margin-bottom:var(--space-lg);position:relative;overflow:hidden}
    .sub-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .sub-status{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-md)}
    .sub-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
    .sub-dot.active{background:var(--primary)}
    .sub-dot.inactive{background:var(--error)}
    .sub-text{font-size:0.875rem;font-weight:600;color:var(--text-primary)}
    .sub-detail{font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-md)}
    .actions{display:flex;flex-direction:column;gap:var(--space-sm)}
    .link-row{display:flex;align-items:center;justify-content:space-between;padding:var(--space-md) var(--space-lg);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);text-decoration:none;color:var(--text-primary);font-size:0.875rem;font-weight:600;transition:background var(--transition-fast);margin-bottom:var(--space-sm)}
    .link-row:hover{background:var(--bg-card-hover);color:var(--text-primary)}
    .link-row svg{width:16px;height:16px;color:var(--text-muted);flex-shrink:0}
</style>
@endpush

@section('content')

    {{-- Profile hero --}}
    <div class="profile-hero">
        <div class="profile-avatar-lg">{{ mb_substr($user->name, 0, 2) }}</div>
        <div class="profile-hero-name">{{ $user->name }}</div>
        <div class="profile-hero-badge">
            @if($user->isVerified())
                <span class="badge badge-active">Верифицирован</span>
            @else
                <span class="badge badge-upcoming">Не верифицирован</span>
            @endif
        </div>
    </div>

    {{-- Info card --}}
    <div class="info-card">
        <div class="info-row">
            <span class="info-label">Телефон</span>
            <span class="info-value">{{ $user->phone ?? 'Не указан' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Специализация</span>
            <span class="info-value">{{ $user->specialization ?? 'Не указана' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Город</span>
            <span class="info-value">{{ $user->city ?? 'Не указан' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Верификация</span>
            <span class="info-value">
                @if($user->isVerified())
                    <span class="badge badge-active">Верифицирован</span>
                @else
                    <span class="badge badge-upcoming">На проверке</span>
                @endif
            </span>
        </div>
    </div>

    {{-- Subscription --}}
    <div class="sub-card">
        <div class="sub-status">
            <div class="sub-dot {{ $user->isActive() ? 'active' : 'inactive' }}"></div>
            <div class="sub-text">
                {{ $user->isActive() ? 'Подписка активна' : 'Подписка неактивна' }}
            </div>
        </div>
        @if($user->subscription_expires_at)
            <div class="sub-detail">
                Активна до {{ $user->subscription_expires_at->translatedFormat('F Y') }}
            </div>
        @else
            <div class="sub-detail">Нет активной подписки</div>
        @endif
        @if($user->isActive())
            <a href="{{ route('payment.create') }}" class="btn btn-secondary" style="height:44px;font-size:0.875rem;text-decoration:none">Продлить подписку</a>
        @else
            <a href="{{ route('payment.create') }}" class="btn btn-primary" style="height:44px;font-size:0.875rem;text-decoration:none">Оплатить взнос</a>
        @endif
    </div>

    {{-- Actions --}}
    <div class="actions">
        <a href="{{ route('profile.edit') }}" class="btn btn-primary" style="height:48px;text-decoration:none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Редактировать профиль
        </a>
    </div>

    {{-- Payment history link --}}
    <a href="{{ route('profile.payments') }}" class="link-row" style="margin-top:var(--space-lg)">
        <span>История оплат</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>

@endsection
