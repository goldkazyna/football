@extends('layouts.app')

@section('title', 'Личный кабинет')

@push('styles')
<style>
    .profile-card{display:flex;align-items:center;gap:var(--space-md);padding:var(--space-lg);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-xl);margin-bottom:var(--space-lg);position:relative;overflow:hidden}
    .profile-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .profile-avatar{width:56px;height:56px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.125rem;font-weight:700;color:var(--text-muted);flex-shrink:0}
    .profile-info{min-width:0;flex:1}
    .profile-name{font-size:1rem;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .profile-spec{font-size:0.8125rem;color:var(--text-secondary);margin-top:2px}
    .profile-badge{margin-top:6px}
    .section-title{font-size:0.9375rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md)}
    .warning-banner{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-md);background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);border-radius:var(--radius-md);margin-bottom:var(--space-lg);font-size:0.8125rem;color:var(--warning)}
    .warning-banner svg{width:20px;height:20px;flex-shrink:0}
    .team-card{display:flex;align-items:center;gap:var(--space-md);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);margin-bottom:var(--space-lg);text-decoration:none;color:inherit;transition:background var(--transition-fast)}
    .team-card:hover{background:var(--bg-card-hover);color:inherit}
    .team-icon{width:40px;height:40px;border-radius:var(--radius-sm);background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0}
    .team-card-info{min-width:0;flex:1}
    .team-card-name{font-size:0.875rem;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-card-city{font-size:0.75rem;color:var(--text-muted)}
    .team-card-arrow{color:var(--text-muted);flex-shrink:0}
    .team-card-arrow svg{width:16px;height:16px}
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-sm);margin-bottom:var(--space-lg)}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);text-align:center;position:relative;overflow:hidden}
    .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .stat-value{font-size:1.25rem;font-weight:800;color:var(--text-primary)}
    .stat-label{font-size:0.625rem;color:var(--text-muted);margin-top:2px;text-transform:uppercase;font-weight:600;letter-spacing:0.03em}
    .stat-value.yellow{color:var(--warning)}
    .stat-value.red{color:var(--error)}
    .stat-value.green{color:var(--primary)}
    .stat-value.blue{color:var(--accent-blue)}
    .matches-section{margin-bottom:var(--space-lg)}
    .match-item{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:var(--space-sm);padding:var(--space-md);background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);margin-bottom:var(--space-sm);position:relative;overflow:hidden}
    .match-item::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
    .match-team{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.8125rem;font-weight:600}
    .match-team-left{text-align:right}
    .match-team-right{text-align:left}
    .match-center{text-align:center;flex-shrink:0}
    .match-vs{font-size:0.6875rem;color:var(--text-muted);font-weight:700}
    .match-date{font-size:0.6875rem;color:var(--text-secondary);margin-top:2px}
    .empty-state{text-align:center;padding:var(--space-xl) var(--space-md);color:var(--text-muted);font-size:0.875rem}
    .status-banner{display:flex;align-items:flex-start;gap:var(--space-sm);padding:var(--space-md);border:1px solid transparent;border-radius:var(--radius-md);margin-bottom:var(--space-lg);font-size:0.8125rem;line-height:1.5}
    .status-banner svg{width:20px;height:20px;flex-shrink:0;margin-top:1px}
    .status-banner.pending{background:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.15);color:var(--accent-blue)}
    .status-banner.rejected{background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.15);color:var(--error)}
    .status-banner.approved{background:rgba(34,197,94,0.08);border-color:rgba(34,197,94,0.15);color:var(--primary)}
    .status-banner-content{flex:1}
    .status-banner-title{font-weight:700;margin-bottom:2px}
    .status-banner-text{font-size:0.75rem;opacity:0.85}
    .status-banner .btn-sm-reupload{display:inline-flex;align-items:center;gap:4px;margin-top:var(--space-sm);padding:6px 14px;border-radius:var(--radius-sm);background:rgba(255,255,255,0.08);border:1px solid currentColor;font-size:0.75rem;font-weight:600;color:inherit;text-decoration:none;cursor:pointer;font-family:inherit;transition:background var(--transition-fast)}
    .status-banner .btn-sm-reupload:hover{background:rgba(255,255,255,0.12)}
</style>
@endpush

@section('content')

    {{-- Verification status --}}
    @if($user->verification_status === 'pending')
        <div class="status-banner pending">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div class="status-banner-content">
                <div class="status-banner-title">Документы на проверке</div>
                <div class="status-banner-text">Ваши документы отправлены на верификацию. Обычно проверка занимает 1-2 дня.</div>
            </div>
        </div>
    @elseif($user->verification_status === 'rejected')
        <div class="status-banner rejected">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <div class="status-banner-content">
                <div class="status-banner-title">Верификация отклонена</div>
                <div class="status-banner-text">Ваши документы не прошли проверку. Пожалуйста, загрузите корректные документы повторно.</div>
                <form method="POST" action="{{ route('profile.reupload') }}" enctype="multipart/form-data" style="margin-top:var(--space-sm);">
                    @csrf
                    <label class="btn-sm-reupload" style="position:relative;overflow:hidden;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Загрузить документ заново
                        <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" style="position:absolute;inset:0;opacity:0;cursor:pointer;" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
    @elseif($user->verification_status === 'approved')
        {{-- Show nothing or a subtle approved state --}}
    @endif

    {{-- Warning banner if subscription inactive --}}
    @if(!$user->isActive() && $user->verification_status !== 'rejected')
        <div class="warning-banner">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Подписка неактивна. <a href="{{ route('profile.show') }}" style="color:var(--warning);text-decoration:underline;margin-left:4px">Продлить</a>
        </div>
    @endif

    {{-- Profile card --}}
    <div class="profile-card">
        <div class="profile-avatar">{{ mb_substr($user->name, 0, 2) }}</div>
        <div class="profile-info">
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-spec">{{ $user->specialization ?? 'Не указана' }}</div>
            <div class="profile-badge">
                <span class="badge {{ $user->isActive() ? 'badge-active' : 'badge-upcoming' }}">
                    {{ $user->isActive() ? 'Активен' : 'Неактивен' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Current team --}}
    <div class="section-title">Моя команда</div>
    @if($team)
        <a href="{{ route('my-team') }}" class="team-card">
            <div class="team-icon">⚽</div>
            <div class="team-card-info">
                <div class="team-card-name">{{ $team->name }}</div>
                <div class="team-card-city">{{ $team->city }}</div>
            </div>
            <div class="team-card-arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </a>
    @else
        <div class="team-card" style="justify-content:center;cursor:default;">
            <span style="font-size:0.875rem;color:var(--text-muted);">Вы не состоите в команде</span>
        </div>
    @endif

    {{-- Upcoming matches --}}
    <div class="matches-section">
        <div class="section-title">Ближайшие матчи</div>
        <div class="empty-state">
            <p>Нет запланированных матчей</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="section-title">Личная статистика</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['matches'] ?? 0 }}</div>
            <div class="stat-label">Матчей</div>
        </div>
        <div class="stat-card">
            <div class="stat-value green">{{ $stats['goals'] ?? 0 }}</div>
            <div class="stat-label">Голов</div>
        </div>
        <div class="stat-card">
            <div class="stat-value blue">{{ $stats['assists'] ?? 0 }}</div>
            <div class="stat-label">Ассистов</div>
        </div>
        <div class="stat-card">
            <div class="stat-value yellow">{{ $stats['yellow_cards'] ?? 0 }}</div>
            <div class="stat-label">ЖК</div>
        </div>
        <div class="stat-card">
            <div class="stat-value red">{{ $stats['red_cards'] ?? 0 }}</div>
            <div class="stat-label">КК</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['win_rate'] ?? '0%' }}</div>
            <div class="stat-label">Побед</div>
        </div>
    </div>

@endsection
