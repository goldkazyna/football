@extends('layouts.app')

@section('title', $tournament->name)

@push('styles')
<style>
    .hero-section{text-align:center;padding:var(--space-lg) 0 var(--space-md)}
    .hero-section .badge{margin-bottom:var(--space-sm)}
    .hero-section h1{font-size:1.375rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:var(--space-sm)}
    .hero-desc{font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-md);line-height:1.5}
    .hero-meta{display:flex;flex-wrap:wrap;justify-content:center;gap:var(--space-xs) var(--space-md);margin-bottom:var(--space-lg)}
    .hero-meta-item{display:flex;align-items:center;gap:var(--space-xs);font-size:0.75rem;color:var(--text-secondary)}
    .hero-meta-item svg{width:14px;height:14px;flex-shrink:0;color:var(--text-muted)}
    .section-title{font-size:0.875rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-sm);margin-top:var(--space-lg)}
    .team-item{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-sm) 0;border-bottom:1px solid var(--border-subtle)}
    .team-item:last-child{border-bottom:none}
    .team-emoji{font-size:1.25rem;width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:var(--bg-input);border-radius:var(--radius-sm);flex-shrink:0}
    .team-info{flex:1;min-width:0}
    .team-name{font-size:0.8125rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-city{font-size:0.6875rem;color:var(--text-muted)}
    .app-status-banner{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-md);border-radius:var(--radius-md);margin-bottom:var(--space-md);font-size:0.8125rem;font-weight:600}
    .app-status-banner svg{width:20px;height:20px;flex-shrink:0}
    .app-status-approved{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:var(--primary)}
    .app-status-pending{background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);color:var(--warning)}
    .app-status-rejected{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--error)}
</style>
@endpush

@section('content')
    {{-- Hero --}}
    <div class="hero-section">
        @if($tournament->status === 'registration')
            <span class="badge badge-active">Регистрация открыта</span>
        @elseif($tournament->status === 'closed')
            <span class="badge badge-upcoming">Регистрация закрыта</span>
        @endif

        <h1>{{ $tournament->name }}</h1>

        @if($tournament->description)
            <p class="hero-desc">{{ $tournament->description }}</p>
        @endif

        <div class="hero-meta">
            @if($tournament->start_date)
                <span class="hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $tournament->start_date->format('d M') }}{{ $tournament->end_date ? ' — '.$tournament->end_date->format('d M') : '' }}
                </span>
            @endif
            @if($tournament->venue)
                <span class="hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $tournament->venue }}
                </span>
            @endif
            <span class="hero-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                {{ $tournament->approvedApplications->count() }}{{ $tournament->max_teams ? '/'.$tournament->max_teams : '' }} команд
            </span>
        </div>

        {{-- Apply button --}}
        @if($team && auth()->user()->isCaptain() && $tournament->isOpenForRegistration() && !$application)
            <a href="{{ route('tournaments.apply', $tournament) }}" class="btn btn-primary" style="text-decoration:none;">Подать заявку команды</a>
        @endif
    </div>

    {{-- Application status --}}
    @if($application)
        @if($application->status === 'approved')
            <div class="app-status-banner app-status-approved">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Заявка вашей команды одобрена
            </div>
        @elseif($application->status === 'pending')
            <div class="app-status-banner app-status-pending">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Заявка на рассмотрении
            </div>
        @elseif($application->status === 'rejected')
            <div class="app-status-banner app-status-rejected">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Заявка отклонена{{ $application->rejected_reason ? ': '.$application->rejected_reason : '' }}
            </div>
            @if($tournament->isOpenForRegistration() && auth()->user()->isCaptain())
                <div style="text-align:center;margin-bottom:var(--space-md);">
                    <a href="{{ route('tournaments.apply', $tournament) }}" class="btn btn-primary" style="text-decoration:none;">Подать заявку заново</a>
                </div>
            @endif
        @endif
    @endif

    {{-- Approved teams --}}
    @if($tournament->approvedApplications->count())
        <div class="section-title">Участники ({{ $tournament->approvedApplications->count() }})</div>
        @foreach($tournament->approvedApplications as $app)
            <div class="team-item">
                <span class="team-emoji">&#9917;</span>
                <div class="team-info">
                    <div class="team-name">{{ $app->team->name }}</div>
                    <div class="team-city">{{ $app->team->city }}</div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
