@extends('layouts.app')

@section('title', 'Заявка на турнир')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .page-subtitle{font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-lg)}
    .player-list{margin-bottom:var(--space-lg)}
    .player-item{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);margin-bottom:var(--space-sm);cursor:pointer;transition:all var(--transition-fast)}
    .player-item:hover{background:var(--bg-card-hover)}
    .player-item.disabled{opacity:0.5;cursor:not-allowed}
    .player-item.disabled:hover{background:var(--bg-card)}
    .player-checkbox{width:20px;height:20px;border-radius:4px;border:2px solid var(--border-input);background:var(--bg-input);flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all var(--transition-fast)}
    .player-checkbox.checked{background:var(--primary);border-color:var(--primary)}
    .player-checkbox svg{width:14px;height:14px;color:white;display:none}
    .player-checkbox.checked svg{display:block}
    .player-info{flex:1;min-width:0}
    .player-name{font-size:0.875rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .player-meta{font-size:0.6875rem;color:var(--text-muted)}
    .player-badge{flex-shrink:0}
    .selected-count{font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-md)}
</style>
@endpush

@section('content')
    <div class="page-title">{{ $tournament->name }}</div>
    <div class="page-subtitle">Выберите игроков для заявки от команды «{{ $team->name }}»</div>

    <form method="POST" action="{{ route('tournaments.submitApplication', $tournament) }}" x-data="{
        selected: [],
        toggle(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                this.selected.push(id);
            }
        }
    }">
        @csrf

        <div class="selected-count">
            Выбрано игроков: <strong x-text="selected.length">0</strong>
        </div>

        <div class="player-list">
            @foreach($members as $member)
                @php
                    $hasSubscription = $member->subscription_status === 'active'
                        && $member->subscription_expires_at
                        && $member->subscription_expires_at->isFuture();
                @endphp

                @if($hasSubscription)
                    <div class="player-item" @click="toggle({{ $member->id }})">
                        <div class="player-checkbox" :class="{ 'checked': selected.includes({{ $member->id }}) }">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <input type="hidden" name="players[]" value="{{ $member->id }}" x-bind:disabled="!selected.includes({{ $member->id }})">
                        <div class="player-info">
                            <div class="player-name">{{ $member->name }}</div>
                            <div class="player-meta">{{ $member->city ?? '' }}{{ $member->specialization ? ' · '.$member->specialization : '' }}</div>
                        </div>
                        <div class="player-badge">
                            <span class="badge badge-active" style="font-size:0.625rem;">Оплачен</span>
                        </div>
                    </div>
                @else
                    <div class="player-item disabled">
                        <div class="player-checkbox">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="player-info">
                            <div class="player-name">{{ $member->name }}</div>
                            <div class="player-meta">Взнос не оплачен</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        @error('players')
            <div style="color:var(--error);font-size:0.8125rem;margin-bottom:var(--space-md);">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn btn-primary" style="width:100%;height:48px;" x-bind:disabled="selected.length === 0">
            Подать заявку
        </button>
    </form>
@endsection
