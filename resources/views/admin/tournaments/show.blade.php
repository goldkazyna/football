@extends('layouts.admin')

@section('title', $tournament->name)

@push('styles')
<style>
    .tournament-header{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-lg);flex-wrap:wrap}
    .tournament-header h1{font-size:1.25rem;font-weight:800;color:var(--text-primary)}
    .tournament-actions{display:flex;gap:var(--space-sm);margin-bottom:var(--space-lg)}
    .section-title{font-size:0.875rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:var(--space-md)}
    .app-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm)}
    .app-card-top{display:flex;align-items:center;justify-content:space-between;gap:var(--space-sm)}
    .app-card-name{font-size:0.9375rem;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;flex:1}
    .app-card-actions{display:flex;gap:var(--space-sm);margin-top:var(--space-md);align-items:center}
    .btn-approve{height:36px;padding:0 20px;font-size:0.8125rem;font-weight:600;font-family:inherit;border:none;border-radius:var(--radius-sm);cursor:pointer;background:var(--primary);color:white;transition:background var(--transition-fast);flex-shrink:0}
    .btn-approve:hover{background:var(--primary-hover)}
    .btn-reject-outline{height:36px;padding:0 20px;font-size:0.8125rem;font-weight:600;font-family:inherit;border:1px solid rgba(239,68,68,0.3);border-radius:var(--radius-sm);cursor:pointer;background:transparent;color:var(--error);transition:all var(--transition-fast);flex-shrink:0}
    .btn-reject-outline:hover{background:rgba(239,68,68,0.1)}
    .btn-reject{height:36px;padding:0 20px;font-size:0.8125rem;font-weight:600;font-family:inherit;border:none;border-radius:var(--radius-sm);cursor:pointer;background:var(--error);color:white;transition:opacity var(--transition-fast);flex-shrink:0}
    .btn-reject:hover{opacity:0.85}
    .reject-form{margin-top:var(--space-sm);padding:var(--space-sm);background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15);border-radius:var(--radius-sm)}
    .reject-form-inner{display:flex;gap:var(--space-sm);align-items:center}
    .reject-form-inner .form-input{height:36px;font-size:0.8125rem;flex:1;min-width:0}
    .app-card-meta{font-size:0.75rem;color:var(--text-secondary);margin-top:var(--space-sm)}
    .app-card-players{font-size:0.75rem;color:var(--text-secondary);margin-top:var(--space-xs)}
    .btn-edit-sm{height:30px;padding:0 12px;background:transparent;color:var(--text-secondary);border:1px solid var(--border-input);border-radius:var(--radius-sm);font-size:0.6875rem;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;transition:all var(--transition-fast)}
    .btn-edit-sm:hover{background:rgba(255,255,255,0.04);color:var(--text-secondary)}
</style>
@endpush

@section('content')
    <div class="tournament-header">
        <h1>{{ $tournament->name }}</h1>
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

    <div class="tournament-actions">
        <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="btn-edit-sm">Редактировать</a>
    </div>

    @if($tournament->description)
        <div style="font-size:0.8125rem;color:var(--text-secondary);margin-bottom:var(--space-lg);line-height:1.5;">
            {{ $tournament->description }}
        </div>
    @endif

    <div style="display:flex;flex-wrap:wrap;gap:var(--space-sm) var(--space-lg);font-size:0.75rem;color:var(--text-secondary);margin-bottom:var(--space-lg);">
        @if($tournament->start_date)
            <span>{{ $tournament->start_date->format('d.m.Y') }}{{ $tournament->end_date ? ' — '.$tournament->end_date->format('d.m.Y') : '' }}</span>
        @endif
        @if($tournament->venue)
            <span>{{ $tournament->venue }}</span>
        @endif
        @if($tournament->max_teams)
            <span>Макс. {{ $tournament->max_teams }} команд</span>
        @endif
    </div>

    {{-- Applications --}}
    <div class="section-title">Заявки команд ({{ $tournament->applications->count() }})</div>

    @forelse($tournament->applications as $application)
        <div class="app-card">
            <div class="app-card-top">
                <span class="app-card-name">{{ $application->team->name }}</span>
                @if($application->status === 'approved')
                    <span class="badge badge-active">Одобрена</span>
                @elseif($application->status === 'pending')
                    <span class="badge badge-upcoming" style="white-space:nowrap;">На рассмотрении</span>
                @elseif($application->status === 'rejected')
                    <span class="badge badge-finished" style="background:rgba(239,68,68,0.1);color:var(--error);">Отклонена</span>
                @endif
            </div>

            <div class="app-card-meta">
                Подал: {{ $application->appliedBy->name }} &middot; {{ $application->created_at->format('d.m.Y H:i') }}
            </div>

            @if($application->players->count())
                <div class="app-card-players">
                    Игроки ({{ $application->players->count() }}): {{ $application->players->pluck('name')->join(', ') }}
                </div>
            @endif

            @if($application->status === 'pending')
                <div x-data="{ showReject: false }">
                    <div class="app-card-actions">
                        <form method="POST" action="{{ route('admin.tournaments.applications.approve', [$tournament, $application]) }}">
                            @csrf
                            <button type="submit" class="btn-approve">Одобрить</button>
                        </form>
                        <button class="btn-reject-outline" @click="showReject = !showReject" x-text="showReject ? 'Отмена' : 'Отклонить'">Отклонить</button>
                    </div>
                    <div class="reject-form" x-show="showReject" x-cloak x-transition>
                        <form method="POST" action="{{ route('admin.tournaments.applications.reject', [$tournament, $application]) }}">
                            @csrf
                            <div class="reject-form-inner">
                                <input type="text" name="reason" class="form-input" placeholder="Причина отклонения (необязательно)">
                                <button type="submit" class="btn-reject">Отклонить</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if($application->status === 'rejected' && $application->rejected_reason)
                <div style="font-size:0.75rem;color:var(--error);margin-top:var(--space-xs);">
                    Причина: {{ $application->rejected_reason }}
                </div>
            @endif
        </div>
    @empty
        <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);">
            Заявок пока нет
        </div>
    @endforelse
@endsection
