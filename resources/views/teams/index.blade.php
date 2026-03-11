@extends('layouts.app')

@section('title', 'Команды')

@push('styles')
<style>
    .page-title{font-size:1.375rem;font-weight:800;margin-bottom:var(--space-md)}
    .search-wrap{position:relative;margin-bottom:var(--space-md)}
    .search-wrap svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--text-muted);pointer-events:none}
    .search-wrap .form-input{padding-left:40px}
    .city-filters{margin-bottom:var(--space-lg)}
    .team-list{display:flex;flex-direction:column;gap:var(--space-sm)}
    .team-card{display:flex;align-items:center;gap:var(--space-md);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);text-decoration:none;color:inherit;transition:background var(--transition-fast)}
    .team-card:hover{background:var(--bg-card-hover);color:inherit}
    .team-logo{width:44px;height:44px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;overflow:hidden}
    .team-logo img{width:100%;height:100%;object-fit:cover}
    .team-info{flex:1;min-width:0}
    .team-name{font-size:0.875rem;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-meta{font-size:0.75rem;color:var(--text-secondary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-captain{font-size:0.6875rem;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px}
    .team-arrow{color:var(--text-muted);flex-shrink:0}
    .team-arrow svg{width:16px;height:16px}
</style>
@endpush

@section('content')
    <h1 class="page-title">Команды</h1>

    <form method="GET" action="{{ route('teams.index') }}">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-input" placeholder="Поиск команды..." value="{{ request('search') }}">
        </div>

        <div class="city-filters filters">
            <a href="{{ route('teams.index', array_merge(request()->except('city', 'page'))) }}" class="filter-btn {{ !request('city') ? 'active' : '' }}">Все</a>
            @foreach($cities as $city)
                <a href="{{ route('teams.index', array_merge(request()->except('page'), ['city' => $city])) }}" class="filter-btn {{ request('city') === $city ? 'active' : '' }}">{{ $city }}</a>
            @endforeach
        </div>
    </form>

    <div class="team-list">
        @forelse($teams as $team)
            <a href="{{ route('teams.show', $team) }}" class="team-card">
                <div class="team-logo">
                    @if($team->logo)
                        <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                    @else
                        ⚽
                    @endif
                </div>
                <div class="team-info">
                    <div class="team-name">{{ $team->name }}</div>
                    <div class="team-meta">{{ $team->city }} &middot; {{ $team->members_count ?? $team->members->count() }} {{ trans_choice('игрок|игрока|игроков', $team->members_count ?? $team->members->count()) }}</div>
                    @if($team->captain)
                        <div class="team-captain">Капитан: {{ $team->captain->name }}</div>
                    @endif
                </div>
                <div class="team-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </a>
        @empty
            <div style="text-align:center;padding:var(--space-xl) 0;color:var(--text-muted);">
                <p>Команды не найдены</p>
            </div>
        @endforelse
    </div>

    @if($teams->hasPages())
        <div style="margin-top:var(--space-lg);display:flex;justify-content:center;">
            {{ $teams->withQueryString()->links() }}
        </div>
    @endif
@endsection
