@extends('layouts.app')

@section('title', 'Турниры')

@push('styles')
<style>
    .page-title{font-size:1.375rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:var(--space-md)}
    .tournament-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-md);margin-bottom:var(--space-md);position:relative;overflow:hidden;text-decoration:none;color:inherit;display:block;transition:background var(--transition-fast)}
    .tournament-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .tournament-card:hover{background:var(--bg-card-hover);color:inherit}
    .tournament-card-header{display:flex;align-items:center;justify-content:space-between;gap:var(--space-sm);margin-bottom:var(--space-sm)}
    .tournament-card-name{font-size:0.9375rem;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0}
    .tournament-card-meta{display:flex;flex-wrap:wrap;gap:var(--space-xs) var(--space-md);margin-bottom:var(--space-md)}
    .tournament-card-meta-item{display:flex;align-items:center;gap:var(--space-xs);font-size:0.75rem;color:var(--text-secondary)}
    .tournament-card-meta-item svg{width:14px;height:14px;flex-shrink:0;color:var(--text-muted)}
    .tournament-card-status{padding:var(--space-sm) 0 0;border-top:1px solid var(--border-subtle)}
    .status-approved{display:flex;align-items:center;gap:var(--space-xs);font-size:0.75rem;font-weight:600;color:var(--primary)}
    .status-approved svg{width:16px;height:16px;flex-shrink:0}
    .status-pending{display:flex;align-items:center;gap:var(--space-xs);font-size:0.75rem;font-weight:600;color:var(--warning)}
    .status-pending svg{width:16px;height:16px;flex-shrink:0}
    .status-rejected{display:flex;align-items:center;gap:var(--space-xs);font-size:0.75rem;font-weight:600;color:var(--error)}
    .status-rejected svg{width:16px;height:16px;flex-shrink:0}
</style>
@endpush

@section('content')
    <h1 class="page-title">Турниры</h1>

    @forelse($tournaments as $tournament)
        <a href="{{ route('tournaments.show', $tournament) }}" class="tournament-card">
            <div class="tournament-card-header">
                <span class="tournament-card-name">{{ $tournament->name }}</span>
                @if($tournament->status === 'registration')
                    <span class="badge badge-active">Регистрация</span>
                @elseif($tournament->status === 'closed')
                    <span class="badge badge-upcoming">Закрыта</span>
                @else
                    <span class="badge">{{ $tournament->status }}</span>
                @endif
            </div>
            <div class="tournament-card-meta">
                @if($tournament->start_date)
                    <span class="tournament-card-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $tournament->start_date->format('d M') }}{{ $tournament->end_date ? ' — '.$tournament->end_date->format('d M') : '' }}
                    </span>
                @endif
                <span class="tournament-card-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    {{ $tournament->active_applications_count }}{{ $tournament->max_teams ? '/'.$tournament->max_teams : '' }} команд
                </span>
                @if($tournament->venue)
                    <span class="tournament-card-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $tournament->venue }}
                    </span>
                @endif
            </div>

            {{-- Application status for captain's team --}}
            @if($team && isset($teamApplications[$tournament->id]))
                <div class="tournament-card-status">
                    @if($teamApplications[$tournament->id] === 'approved')
                        <span class="status-approved">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Заявка одобрена
                        </span>
                    @elseif($teamApplications[$tournament->id] === 'pending')
                        <span class="status-pending">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Заявка на рассмотрении
                        </span>
                    @elseif($teamApplications[$tournament->id] === 'rejected')
                        <span class="status-rejected">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Заявка отклонена
                        </span>
                    @endif
                </div>
            @endif
        </a>
    @empty
        <div style="padding:var(--space-xl);text-align:center;color:var(--text-muted);">
            Турниров пока нет
        </div>
    @endforelse

    @if($tournaments->hasPages())
        <div style="margin-top:var(--space-lg);display:flex;justify-content:center;">
            {{ $tournaments->links() }}
        </div>
    @endif
@endsection
