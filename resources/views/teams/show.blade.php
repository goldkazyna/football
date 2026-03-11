@extends('layouts.app')

@section('title', $team->name)

@push('styles')
<style>
    .team-hero{text-align:center;padding:var(--space-lg) 0 var(--space-md)}
    .team-hero-logo{width:72px;height:72px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:3rem;margin:0 auto var(--space-sm);overflow:hidden;line-height:1}
    .team-hero-logo img{width:100%;height:100%;object-fit:cover}
    .team-hero-name{font-size:1.375rem;font-weight:800;margin-bottom:var(--space-xs)}
    .team-hero-city{font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-xs)}
    .team-hero-motto{font-size:0.75rem;color:var(--text-muted);font-style:italic}
    .captain-card{display:flex;align-items:center;gap:var(--space-md);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);margin-bottom:var(--space-lg)}
    .captain-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent-blue));display:flex;align-items:center;justify-content:center;font-size:0.8125rem;font-weight:700;color:#fff;flex-shrink:0}
    .captain-info{flex:1;min-width:0}
    .captain-name{font-size:0.875rem;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .captain-label{display:flex;align-items:center;gap:var(--space-xs);margin-top:2px}
    .section-title{font-size:1rem;font-weight:700;margin-bottom:var(--space-md)}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-sm);margin-bottom:var(--space-lg)}
    .stat-box{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-sm);padding:var(--space-sm) var(--space-xs);text-align:center}
    .stat-val{font-size:1.125rem;font-weight:800;color:var(--text-primary)}
    .stat-label{font-size:0.625rem;color:var(--text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .player-list{display:flex;flex-direction:column;gap:var(--space-xs);margin-bottom:var(--space-lg)}
    .player-row{display:flex;align-items:center;gap:var(--space-sm);padding:10px var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-sm)}
    .player-avatar{width:36px;height:36px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:0.6875rem;font-weight:700;color:var(--text-secondary);flex-shrink:0;border:1px solid var(--border-subtle)}
    .player-info{flex:1;min-width:0}
    .player-name{font-size:0.8125rem;font-weight:600;display:flex;align-items:center;gap:var(--space-xs);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .player-position{font-size:0.6875rem;color:var(--text-muted);margin-top:1px}
    .apply-section{margin-top:var(--space-lg)}
</style>
@endpush

@section('content')
    <div class="team-hero">
        <div class="team-hero-logo">
            @if($team->logo)
                <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
            @else
                ⚽
            @endif
        </div>
        <div class="team-hero-name">{{ $team->name }}</div>
        <div class="team-hero-city">{{ $team->city }}</div>
        @if($team->description)
            <div class="team-hero-motto">"{{ $team->description }}"</div>
        @endif
    </div>

    @if($team->captain)
        <div class="captain-card">
            <div class="captain-avatar">{{ mb_substr($team->captain->name, 0, 2) }}</div>
            <div class="captain-info">
                <div class="captain-name">{{ $team->captain->name }}</div>
                <div class="captain-label">
                    <span class="badge badge-active">Капитан</span>
                </div>
            </div>
        </div>
    @endif

    <h2 class="section-title">Игроки</h2>
    <div class="player-list">
        @forelse($team->members as $member)
            <div class="player-row">
                <div class="player-avatar">{{ mb_substr($member->name, 0, 2) }}</div>
                <div class="player-info">
                    <div class="player-name">
                        {{ $member->name }}
                        @if($team->captain_id === $member->id)
                            <span class="badge badge-active" style="font-size:0.5625rem;padding:1px 6px">Капитан</span>
                        @endif
                    </div>
                    @if($member->position)
                        <div class="player-position">{{ $member->position }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:var(--space-md);color:var(--text-muted);font-size:0.8125rem;">
                В команде пока нет игроков
            </div>
        @endforelse
    </div>

    @auth
        @php
            $isMember = $team->members->contains(auth()->id());
            $hasPendingApplication = $team->applications()->where('user_id', auth()->id())->where('status', 'pending')->exists();
        @endphp

        @if(!$isMember && !$hasPendingApplication)
            <div class="apply-section">
                <form method="POST" action="{{ route('teams.apply', $team) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Подать заявку в команду</button>
                </form>
            </div>
        @elseif($hasPendingApplication)
            <div class="apply-section">
                <button class="btn" disabled style="opacity:0.6;cursor:not-allowed;">Заявка на рассмотрении</button>
            </div>
        @endif
    @endauth
@endsection
