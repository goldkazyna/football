@extends('layouts.admin')

@section('title', 'Управление турнирами')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-md)}
    .tournament-card{display:block;background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm);text-decoration:none;color:inherit;transition:background var(--transition-fast)}
    .tournament-card:hover{background:var(--bg-card-hover);color:inherit}
    .tournament-card-top{display:flex;align-items:center;justify-content:space-between;gap:var(--space-sm);margin-bottom:var(--space-sm)}
    .tournament-card-name{font-size:0.9375rem;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;flex:1}
    .tournament-card-meta{display:flex;align-items:center;gap:var(--space-md);font-size:0.75rem;color:var(--text-secondary)}
    .tournament-card-meta span{display:flex;align-items:center;gap:4px}
    .tournament-card-meta svg{width:14px;height:14px;flex-shrink:0}
</style>
@endpush

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-md);">
        <div class="page-title" style="margin-bottom:0;">Управление турнирами</div>
        <a href="{{ route('admin.tournaments.create') }}" class="btn-sm btn-sm-primary" style="text-decoration:none;">+ Создать</a>
    </div>

    @forelse($tournaments as $tournament)
        <a href="{{ route('admin.tournaments.show', $tournament) }}" class="tournament-card">
            <div class="tournament-card-top">
                <span class="tournament-card-name">{{ $tournament->name }}</span>
                @if($tournament->status === 'draft')
                    <span class="badge badge-finished">Черновик</span>
                @elseif($tournament->status === 'registration')
                    <span class="badge badge-active">Регистрация</span>
                @elseif($tournament->status === 'closed')
                    <span class="badge badge-upcoming">Закрыта</span>
                @else
                    <span class="badge">{{ $tournament->status }}</span>
                @endif
            </div>
            <div class="tournament-card-meta">
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    {{ $tournament->active_applications_count }}{{ $tournament->max_teams ? '/'.$tournament->max_teams : '' }} команд
                </span>
                @if($tournament->venue)
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $tournament->venue }}
                </span>
                @endif
                @if($tournament->start_date)
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $tournament->start_date->format('d.m.Y') }}
                </span>
                @endif
                @if($tournament->pending_applications_count > 0)
                <span style="color:var(--warning);font-weight:600;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    {{ $tournament->pending_applications_count }} {{ $tournament->pending_applications_count === 1 ? 'заявка' : 'заявок' }}
                </span>
                @endif
            </div>
        </a>
    @empty
        <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);">
            Турниров пока нет
        </div>
    @endforelse

    @if($tournaments->hasPages())
        <div style="margin-top:var(--space-lg);display:flex;justify-content:center;">
            {{ $tournaments->links() }}
        </div>
    @endif
@endsection
