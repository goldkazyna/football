@extends('layouts.app')

@section('title', 'Управление командой')

@push('styles')
<style>
    .page-title{font-size:1.375rem;font-weight:800;margin-bottom:var(--space-sm)}
    .page-subtitle{font-size:0.8125rem;color:var(--text-muted);margin-bottom:var(--space-lg)}
    .action-buttons{display:flex;gap:var(--space-sm);margin-bottom:var(--space-xl)}
    .action-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);text-decoration:none;color:var(--text-primary);font-size:0.8125rem;font-weight:600;transition:all var(--transition-fast)}
    .action-btn:hover{background:var(--bg-card-hover);color:var(--text-primary)}
    .action-btn svg{width:18px;height:18px;flex-shrink:0}
    .action-btn.primary{background:rgba(34,197,94,0.1);border-color:rgba(34,197,94,0.2);color:var(--primary)}
    .action-btn.primary:hover{background:rgba(34,197,94,0.15)}
    .section-title{font-size:1rem;font-weight:700;margin-bottom:var(--space-md);display:flex;align-items:center;gap:var(--space-sm)}
    .section-count{display:inline-flex;align-items:center;justify-content:center;height:20px;padding:0 8px;border-radius:var(--radius-full);background:rgba(59,130,246,0.12);color:var(--accent-blue);font-size:0.6875rem;font-weight:700}
    .section-block{margin-bottom:var(--space-xl)}
    .request-list{display:flex;flex-direction:column;gap:var(--space-sm)}
    .request-card{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md)}
    .req-avatar{width:40px;height:40px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:0.6875rem;font-weight:700;color:var(--text-secondary);flex-shrink:0}
    .req-info{flex:1;min-width:0}
    .req-name{font-size:0.8125rem;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .req-spec{font-size:0.6875rem;color:var(--text-muted)}
    .req-actions{display:flex;gap:6px;flex-shrink:0}
    .btn-accept{height:32px;padding:0 12px;border:none;border-radius:var(--radius-sm);background:var(--primary);color:#fff;font-size:0.6875rem;font-weight:700;font-family:inherit;cursor:pointer}
    .btn-reject-sm{height:32px;padding:0 12px;border:1px solid var(--error);border-radius:var(--radius-sm);background:transparent;color:var(--error);font-size:0.6875rem;font-weight:700;font-family:inherit;cursor:pointer}
    .player-list{display:flex;flex-direction:column;gap:var(--space-sm)}
    .player-row{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md)}
    .player-avatar{width:40px;height:40px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:0.6875rem;font-weight:700;color:var(--text-secondary);flex-shrink:0}
    .player-avatar.captain{background:linear-gradient(135deg,var(--primary),var(--accent-blue));color:#fff}
    .player-info{flex:1;min-width:0}
    .player-name{font-size:0.8125rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .player-meta{font-size:0.6875rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .captain-tag{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:var(--radius-full);background:rgba(245,158,11,0.12);color:var(--warning);font-size:0.625rem;font-weight:700;text-transform:uppercase}
    .btn-remove{height:28px;padding:0 10px;border:1px solid var(--error);border-radius:var(--radius-sm);background:transparent;color:var(--error);font-size:0.625rem;font-weight:700;font-family:inherit;cursor:pointer;flex-shrink:0}
    .empty-hint{font-size:0.8125rem;color:var(--text-muted);padding:var(--space-md);text-align:center}
    .ver-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm)}
    .ver-header{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-sm)}
    .ver-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#16a34a);display:flex;align-items:center;justify-content:center;font-size:0.6875rem;font-weight:700;color:#fff;flex-shrink:0}
    .ver-info{flex:1;min-width:0}
    .ver-name{font-size:0.8125rem;font-weight:600;color:var(--text-primary)}
    .ver-meta{font-size:0.6875rem;color:var(--text-muted)}
    .ver-date{font-size:0.6875rem;color:var(--text-muted);flex-shrink:0}
    .ver-details{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:var(--space-sm)}
    .ver-tag{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:var(--radius-full);font-size:0.6875rem;font-weight:600;background:var(--bg-input);color:var(--text-secondary)}
    .ver-tag svg{width:12px;height:12px}
    .ver-actions{display:flex;gap:var(--space-sm)}
    .ver-actions form{flex:1}
</style>
@endpush

@section('content')

    @php
        $hasSubscription = auth()->user()->isActive() || auth()->user()->isSuperAdmin();
    @endphp

    <h1 class="page-title">Управление командой</h1>
    <div class="page-subtitle">{{ $team->name }} · {{ $team->city }}</div>

    @unless($hasSubscription)
        <div class="warning-banner" style="background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.2);color:var(--warning);margin-bottom:var(--space-lg);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div>
                <div style="font-weight:700;margin-bottom:2px;">Требуется подписка</div>
                <div style="font-size:0.75rem;opacity:0.85;">Для управления командой необходима активная подписка. Оформите подписку, чтобы добавлять игроков, принимать заявки и редактировать команду.</div>
            </div>
        </div>
        <div class="action-buttons">
            <a href="{{ route('payment.create') }}" class="action-btn primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Оформить подписку
            </a>
        </div>
    @else
        {{-- Action buttons --}}
        <div class="action-buttons">
            <a href="{{ route('whitelist.index') }}" class="action-btn primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Добавить игроков
            </a>
            <a href="{{ route('teams.edit', $team) }}" class="action-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Редактировать
            </a>
        </div>
    @endunless

    {{-- Pending verifications --}}
    @if($pendingVerifications->count() && $hasSubscription)
    <div class="section-block">
        <h2 class="section-title">Верификация игроков <span class="section-count">{{ $pendingVerifications->count() }}</span></h2>
        @foreach($pendingVerifications as $verification)
            <div class="ver-card">
                <div class="ver-header">
                    <div class="ver-avatar">{{ mb_substr($verification->name, 0, 2) }}</div>
                    <div class="ver-info">
                        <div class="ver-name">{{ $verification->name }}</div>
                        <div class="ver-meta">{{ $verification->iin }}</div>
                    </div>
                    <div class="ver-date">{{ $verification->created_at->format('d.m') }}</div>
                </div>
                <div class="ver-details">
                    @if($verification->specialization)
                        <span class="ver-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            {{ $verification->specialization }}
                        </span>
                    @endif
                    @if($verification->city)
                        <span class="ver-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $verification->city }}
                        </span>
                    @endif
                </div>
                @if($verification->verification_document)
                    <a href="{{ Storage::url($verification->verification_document) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:0.6875rem;color:var(--accent-blue);text-decoration:none;margin-bottom:var(--space-sm);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Посмотреть документ
                    </a>
                @endif
                <div class="ver-actions">
                    <form method="POST" action="{{ route('my-team.verify', $verification) }}">
                        @csrf
                        <button type="submit" class="btn-accept" style="width:100%;height:34px;">Одобрить</button>
                    </form>
                    <form method="POST" action="{{ route('my-team.reject-verification', $verification) }}">
                        @csrf
                        <button type="submit" class="btn-reject-sm" style="width:100%;height:34px;">Отклонить</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Pending applications --}}
    <div class="section-block">
        <h2 class="section-title">Заявки на вступление <span class="section-count">{{ $applications->count() }}</span></h2>
        <div class="request-list">
            @forelse($applications as $application)
                <div class="request-card">
                    <div class="req-avatar">{{ mb_substr($application->user->name ?? '??', 0, 2) }}</div>
                    <div class="req-info">
                        <div class="req-name">{{ $application->user->name ?? 'Удалён' }}</div>
                        <div class="req-spec">{{ $application->user->specialization ?? '' }} · {{ $application->user->city ?? '' }}</div>
                    </div>
                    <div class="req-actions">
                        @if($hasSubscription)
                            <form action="{{ route('my-team.applications.approve', $application) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn-accept">Принять</button>
                            </form>
                            <form action="{{ route('my-team.applications.reject', $application) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-reject-sm">Отклонить</button>
                            </form>
                        @else
                            <span style="font-size:0.6875rem;color:var(--text-muted);opacity:0.6;">Нужна подписка</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-hint">Нет новых заявок</div>
            @endforelse
        </div>
    </div>

    {{-- Current members --}}
    <div class="section-block">
        <h2 class="section-title">Игроки команды <span class="section-count">{{ $members->count() }}</span></h2>
        <div class="player-list">
            @forelse($members as $member)
                <div class="player-row">
                    <div class="player-avatar {{ $member->user_id === $team->captain_id ? 'captain' : '' }}">
                        {{ mb_substr($member->user->name ?? '??', 0, 2) }}
                    </div>
                    <div class="player-info">
                        <div class="player-name">{{ $member->user->name ?? 'Удалён' }}</div>
                        <div class="player-meta">
                            @if($member->user_id === $team->captain_id)
                                <span class="captain-tag">Капитан</span>
                            @endif
                            @if($member->user->specialization ?? null)
                                <span>{{ $member->user->specialization }}</span>
                            @endif
                            @if($member->user->city ?? null)
                                <span>{{ $member->user->city }}</span>
                            @endif
                            @if($member->user->iin ?? null)
                                <span>{{ $member->user->iin }}</span>
                            @endif
                        </div>
                    </div>
                    @if($member->user_id !== $team->captain_id && $hasSubscription)
                        <form action="{{ route('my-team.members.remove', $member) }}" method="POST" onsubmit="return confirm('Удалить игрока из команды?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-remove">Удалить</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="empty-hint">В команде пока нет игроков</div>
            @endforelse
        </div>
    </div>

@endsection
