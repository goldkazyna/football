@extends('layouts.admin')

@section('title', $team->name)

@push('styles')
<style>
    .team-header{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-lg);text-align:center;margin-bottom:var(--space-md)}
    .team-header-logo{width:64px;height:64px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto var(--space-sm);overflow:hidden}
    .team-header-logo img{width:100%;height:100%;object-fit:cover}
    .team-header-name{font-size:1.125rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .team-header-city{font-size:0.8125rem;color:var(--text-secondary)}
    .section-title{font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md)}
    .section-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-md)}
    .captain-form{display:flex;gap:var(--space-sm);align-items:flex-end}
    .captain-form .form-group{flex:1;margin-bottom:0}
    .captain-form .form-select{width:100%;height:44px;padding:0 var(--space-md);background:var(--bg-input);border:1px solid var(--border-input);border-radius:var(--radius-md);color:var(--text-primary);font-family:inherit;font-size:0.875rem;outline:none;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center}
    .player-list{display:flex;flex-direction:column;gap:var(--space-xs);margin-bottom:var(--space-lg)}
    .player-row{display:flex;align-items:center;gap:var(--space-sm);padding:10px var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-sm)}
    .player-avatar{width:36px;height:36px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:0.6875rem;font-weight:700;color:var(--text-secondary);flex-shrink:0;border:1px solid var(--border-subtle)}
    .player-info{flex:1;min-width:0}
    .player-name{font-size:0.8125rem;font-weight:600;display:flex;align-items:center;gap:var(--space-xs);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .player-phone{font-size:0.6875rem;color:var(--text-muted);margin-top:1px}
    .btn-remove{width:28px;height:28px;border-radius:50%;background:rgba(239,68,68,0.1);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .btn-remove:hover{background:rgba(239,68,68,0.2)}
    .btn-remove svg{width:12px;height:12px;color:var(--error)}
</style>
@endpush

@section('content')
    {{-- Team Header --}}
    <div class="team-header">
        <div class="team-header-logo">
            @if($team->logo)
                <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
            @else
                ⚽
            @endif
        </div>
        <div class="team-header-name">{{ $team->name }}</div>
        <div class="team-header-city">{{ $team->city }}</div>
    </div>

    {{-- Captain Info --}}
    @if($team->captain)
        <div class="section-card" style="display:flex;align-items:center;gap:var(--space-sm);">
            <div class="player-avatar">{{ mb_substr($team->captain->name, 0, 2) }}</div>
            <div class="player-info">
                <div class="player-name">{{ $team->captain->name }}</div>
                <div class="player-phone">{{ $team->captain->iin }} &middot; Капитан</div>
            </div>
        </div>
    @endif

    {{-- Members List --}}
    <h2 class="section-title">Игроки ({{ $team->members->count() }})</h2>
    <div class="player-list">
        @forelse($team->members as $member)
            @if($member->user)
            <div class="player-row">
                <div class="player-avatar">{{ mb_substr($member->user->name, 0, 2) }}</div>
                <div class="player-info">
                    <div class="player-name">{{ $member->user->name }}</div>
                    <div class="player-phone">
                        {{ $member->user->iin }}
                        &middot;
                        @if($member->user->subscription_status === 'active' && $member->user->subscription_expires_at?->isFuture())
                            <span style="color:var(--primary);">Оплачен</span>
                        @else
                            <span style="color:var(--error);">Не оплачен</span>
                        @endif
                    </div>
                </div>
                @if($team->captain_id === $member->user_id)
                    <span class="badge badge-active" style="font-size:0.5625rem;padding:1px 6px;flex-shrink:0;">Капитан</span>
                @endif
            </div>
            @endif
        @empty
            <div style="text-align:center;padding:var(--space-md);color:var(--text-muted);font-size:0.8125rem;">
                В команде пока нет игроков
            </div>
        @endforelse
    </div>
@endsection
