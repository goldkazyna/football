@extends('layouts.app')

@section('title', 'Моя команда')

@push('styles')
<style>
    .team-hero{text-align:center;padding:var(--space-xl) 0 var(--space-lg)}
    .team-logo{width:72px;height:72px;border-radius:var(--radius-lg);background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto var(--space-md)}
    .team-name{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .team-city{font-size:0.875rem;color:var(--text-secondary)}
    .section-title{font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md)}
    .player-list{display:flex;flex-direction:column;gap:var(--space-sm);margin-bottom:var(--space-lg)}
    .player-item{display:flex;align-items:center;gap:var(--space-md);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);position:relative;overflow:hidden}
    .player-item::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .player-avatar{width:40px;height:40px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:var(--text-muted);flex-shrink:0;position:relative}
    .player-status-dot{position:absolute;bottom:0;right:0;width:10px;height:10px;border-radius:50%;border:2px solid var(--bg-card)}
    .player-status-dot.active{background:var(--primary)}
    .player-status-dot.inactive{background:var(--text-muted)}
    .player-info{min-width:0;flex:1}
    .player-name{font-size:0.875rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .player-role{font-size:0.75rem;color:var(--text-secondary)}
    .captain-badge{display:inline-flex;align-items:center;gap:3px;font-size:0.625rem;font-weight:700;color:var(--warning);background:rgba(245,158,11,0.1);padding:2px 8px;border-radius:var(--radius-full);text-transform:uppercase;letter-spacing:0.03em}
    .player-position{font-size:0.75rem;color:var(--text-muted);font-weight:600;flex-shrink:0}
    .danger-zone{margin-top:var(--space-xl);padding-top:var(--space-lg);border-top:1px solid var(--border-subtle)}
    .danger-hint{font-size:0.75rem;color:var(--text-muted);margin-top:var(--space-sm);text-align:center}
    .btn-danger{background:transparent;color:var(--error);border:1px solid rgba(239,68,68,0.3)}
    .btn-danger:hover{background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.5)}
    .empty-state{text-align:center;padding:var(--space-xl) var(--space-md);color:var(--text-muted);font-size:0.875rem}
    .empty-state a{color:var(--accent-blue);text-decoration:underline}
</style>
@endpush

@section('content')

    @if($team)
        {{-- Team hero --}}
        <div class="team-hero">
            <div class="team-logo">⚽</div>
            <div class="team-name">{{ $team->name }}</div>
            <div class="team-city">{{ $team->city }}</div>
        </div>

        {{-- Captain manage link --}}
        @if($team->captain_id === auth()->id())
            <div style="display:flex;gap:var(--space-sm);margin-bottom:var(--space-lg)">
                <a href="{{ route('my-team.applications') }}" class="btn btn-secondary" style="flex:1;text-decoration:none;height:44px;font-size:0.875rem">
                    Заявки
                </a>
                <a href="{{ route('teams.edit', $team) }}" class="btn btn-secondary" style="flex:1;text-decoration:none;height:44px;font-size:0.875rem">
                    Редактировать
                </a>
            </div>
        @endif

        {{-- Players --}}
        <div class="section-title">Состав команды</div>
        <div class="player-list">
            @foreach($team->members as $member)
                <div class="player-item">
                    <div class="player-avatar">
                        {{ mb_substr($member->user->name ?? '??', 0, 2) }}
                        <span class="player-status-dot {{ $member->user && $member->user->isActive() ? 'active' : 'inactive' }}"></span>
                    </div>
                    <div class="player-info">
                        <div class="player-name">{{ $member->user->name ?? 'Удалён' }}</div>
                        <div class="player-role">
                            @if($member->user_id === $team->captain_id)
                                <span class="captain-badge">Капитан</span>
                            @else
                                {{ $member->user->specialization ?? '' }}
                            @endif
                        </div>
                        @if($member->user)
                            <div style="font-size:0.6875rem;color:var(--text-muted);margin-top:2px;">
                                {{ $member->user->iin }}
                                &middot;
                                @if($member->user->subscription_status === 'active' && $member->user->subscription_expires_at?->isFuture())
                                    <span style="color:var(--primary);">Оплачен</span>
                                @else
                                    <span style="color:var(--error);">Не оплачен</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Leave team --}}
        @if($team->captain_id !== auth()->id())
            <div class="danger-zone">
                <form action="{{ route('my-team.leave') }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите покинуть команду?')">
                    @csrf
                    <button type="submit" class="btn btn-danger">Покинуть команду</button>
                </form>
                <div class="danger-hint">Вы можете перейти в другую команду или стать свободным агентом</div>
            </div>
        @endif

    @else
        {{-- No team state --}}
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;color:var(--text-muted);margin-bottom:var(--space-md)"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <p>Вы не состоите в команде</p>

            @if(in_array(auth()->user()->role, ['captain', 'superadmin', 'tournament_admin']))
                <a href="{{ route('teams.create') }}" class="btn btn-primary" style="margin-top:var(--space-lg);text-decoration:none;height:48px">
                    Создать команду
                </a>
            @endif

            <p style="margin-top:var(--space-md)">
                <a href="{{ route('teams.index') }}">Посмотреть все команды</a>
            </p>
        </div>
    @endif

@endsection
