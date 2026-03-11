@extends('layouts.admin')

@section('title', 'Управление командами')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-md)}
    .search-box{margin-bottom:var(--space-md)}
    .search-box .form-input{padding-left:40px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:12px center}
    .team-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm)}
    .team-card-top{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-sm)}
    .team-emoji{width:40px;height:40px;border-radius:var(--radius-sm);background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;overflow:hidden}
    .team-emoji img{width:100%;height:100%;object-fit:cover}
    .team-info{flex:1;min-width:0}
    .team-name{font-size:0.8125rem;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-city{font-size:0.6875rem;color:var(--text-muted)}
    .team-meta{display:flex;align-items:center;justify-content:space-between;gap:var(--space-sm)}
    .team-details{font-size:0.6875rem;color:var(--text-secondary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-details svg{width:12px;height:12px;display:inline;vertical-align:-2px;margin-right:2px}
    .btn-edit{height:30px;padding:0 12px;background:transparent;color:var(--text-secondary);border:1px solid var(--border-input);border-radius:var(--radius-sm);font-size:0.6875rem;font-weight:600;font-family:inherit;cursor:pointer;white-space:nowrap;transition:all var(--transition-fast);flex-shrink:0;text-decoration:none;display:inline-flex;align-items:center}
    .btn-edit:hover{background:rgba(255,255,255,0.04);border-color:rgba(148,163,184,0.25);color:var(--text-secondary)}
</style>
@endpush

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-md);">
        <div class="page-title" style="margin-bottom:0;">Управление командами</div>
        <a href="{{ route('admin.teams.create') }}" class="btn-sm btn-sm-primary" style="text-decoration:none;">+ Создать</a>
    </div>

    <form method="GET" action="{{ route('admin.teams.index') }}">
        <div class="search-box">
            <input type="text" name="search" class="form-input" placeholder="Поиск команды..." value="{{ request('search') }}">
        </div>
    </form>

    @forelse($teams as $team)
        <div class="team-card">
            <div class="team-card-top">
                <div class="team-emoji">
                    @if($team->logo)
                        <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                    @else
                        ⚽
                    @endif
                </div>
                <div class="team-info">
                    <div class="team-name">{{ $team->name }}</div>
                    <div class="team-city">{{ $team->city }}</div>
                </div>
            </div>
            <div class="team-meta">
                <div class="team-details">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    @if($team->captain)
                        Капитан: {{ $team->captain->name }} &middot;
                    @endif
                    {{ $team->members_count ?? $team->members->count() }} игроков
                </div>
                <a href="{{ route('admin.teams.show', $team) }}" class="btn-edit">Просмотр</a>
            </div>
        </div>
    @empty
        <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);">
            Команды не найдены
        </div>
    @endforelse

    @if(method_exists($teams, 'hasPages') && $teams->hasPages())
        <div style="margin-top:var(--space-lg);display:flex;justify-content:center;">
            {{ $teams->withQueryString()->links() }}
        </div>
    @endif
@endsection
