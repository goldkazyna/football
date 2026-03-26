@extends('layouts.admin')

@section('title', 'Админ-панель')

@push('styles')
<style>
    .stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-sm);margin-bottom:var(--space-lg)}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);display:flex;flex-direction:column;gap:var(--space-xs)}
    .stat-card-icon{width:32px;height:32px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;margin-bottom:var(--space-xs)}
    .stat-card-icon svg{width:18px;height:18px}
    .stat-card-icon.blue{background:rgba(59,130,246,0.12);color:var(--accent-blue)}
    .stat-card-icon.green{background:rgba(34,197,94,0.12);color:var(--primary)}
    .stat-card-icon.warning{background:rgba(245,158,11,0.12);color:var(--warning)}
    .stat-value{font-size:1.375rem;font-weight:800;color:var(--text-primary);line-height:1}
    .stat-label{font-size:0.6875rem;color:var(--text-secondary);font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .section-title{font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md)}
    .section-block{margin-bottom:var(--space-lg)}

    .ver-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-md)}
    .ver-card-user{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-md)}
    .ver-card-avatar{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8125rem;font-weight:700;color:#fff;flex-shrink:0}
    .ver-card-info{flex:1;min-width:0}
    .ver-card-name{font-size:0.9375rem;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .ver-card-iin{font-size:0.75rem;color:var(--text-muted);font-variant-numeric:tabular-nums;margin-top:1px}
    .ver-card-date{font-size:0.6875rem;color:var(--text-muted);flex-shrink:0;align-self:flex-start;margin-top:2px}
    .ver-card-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:var(--space-md)}
    .ver-tag{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:var(--radius-full);font-size:0.6875rem;font-weight:600;background:var(--bg-input);color:var(--text-secondary)}
    .ver-tag svg{width:12px;height:12px}
    .ver-tag.role-captain{background:rgba(245,158,11,0.1);color:var(--warning)}
    .ver-tag.role-player{background:rgba(59,130,246,0.1);color:var(--accent-blue)}
    .ver-card-docs{display:flex;gap:var(--space-sm);margin-bottom:var(--space-lg)}
    .ver-doc-link{display:flex;align-items:center;gap:6px;padding:8px 12px;background:var(--bg-input);border:1px solid var(--border-input);border-radius:var(--radius-sm);font-size:0.6875rem;font-weight:500;color:var(--text-secondary);text-decoration:none;transition:all var(--transition-fast);flex:1;min-width:0}
    .ver-doc-link:hover{border-color:var(--accent-blue);color:var(--accent-blue);background:rgba(59,130,246,0.04)}
    .ver-doc-link svg{width:14px;height:14px;flex-shrink:0;color:var(--accent-blue)}
    .ver-doc-link span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .ver-card-actions{display:flex;gap:var(--space-sm)}
    .ver-card-actions form{flex:1;display:flex}
    .ver-card-actions .btn{width:100%;height:44px;border-radius:var(--radius-md);font-size:0.875rem;font-weight:600;font-family:inherit;cursor:pointer;transition:all var(--transition-fast);border:none;display:flex;align-items:center;justify-content:center}
    .ver-card-actions .btn-approve{background:var(--primary);color:#fff}
    .ver-card-actions .btn-approve:hover{background:var(--primary-hover)}
    .ver-card-actions .btn-reject{background:var(--bg-input);color:var(--text-secondary);border:1px solid var(--border-input)}
    .ver-card-actions .btn-reject:hover{color:var(--error);border-color:var(--error)}

    .request-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.6875rem;font-weight:700;color:#fff;flex-shrink:0}
    .request-meta{font-size:0.6875rem;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-request-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm);display:flex;align-items:center;gap:var(--space-sm)}
    .team-request-text{flex:1;min-width:0}
    .team-request-flow{font-size:0.8125rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .team-request-flow span{color:var(--primary)}
    .pay-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm)}
    .pay-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-sm);margin-bottom:6px}
    .pay-user{font-size:0.8125rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;min-width:0}
    .pay-amount{font-size:0.875rem;font-weight:700;color:var(--text-primary);white-space:nowrap;flex-shrink:0}
    .pay-meta{font-size:0.6875rem;color:var(--text-muted)}
</style>
@endpush

@section('content')
    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="stat-value">{{ $stats['users'] ?? 0 }}</div>
            <div class="stat-label">Пользователей</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-value">{{ $stats['verified'] ?? 0 }}</div>
            <div class="stat-label">Верифицированных</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="stat-value">{{ $stats['pending_verification'] ?? 0 }}</div>
            <div class="stat-label">Ожидают верификации</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="stat-value">{{ $stats['active_subscriptions'] ?? 0 }}</div>
            <div class="stat-label">Активных подписок</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div class="stat-value">{{ $stats['teams'] ?? 0 }}</div>
            <div class="stat-label">Команд</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="stat-value">{{ number_format($stats['total_payments'] ?? 0) }} &#8376;</div>
            <div class="stat-label">Всего оплат</div>
        </div>
    </div>

    {{-- Pending Verifications --}}
    <div class="section-block">
        <div class="section-title">Заявки на верификацию</div>

        @forelse($pendingVerifications as $verification)
            @php $verTeam = $verification->currentTeam(); @endphp
            <div class="ver-card">
                <div class="ver-card-user">
                    <div class="ver-card-avatar" style="background:linear-gradient(135deg,{{ $verification->role === 'captain' ? 'var(--warning),#d97706' : 'var(--accent-blue),#6366f1' }})">{{ mb_substr($verification->name, 0, 2) }}</div>
                    <div class="ver-card-info">
                        <div class="ver-card-name">{{ $verification->name }}</div>
                        <div class="ver-card-iin">{{ $verification->iin }}</div>
                    </div>
                    <div class="ver-card-date">{{ $verification->created_at->format('d.m.Y') }}</div>
                </div>
                <div class="ver-card-tags">
                    <span class="ver-tag {{ $verification->role === 'captain' ? 'role-captain' : 'role-player' }}">
                        {{ $verification->role === 'captain' ? 'Капитан' : 'Игрок' }}
                    </span>
                    @if($verification->specialization)
                        <span class="ver-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            {{ $verification->specialization }}
                        </span>
                    @endif
                    @if($verification->city)
                        <span class="ver-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $verification->city }}
                        </span>
                    @endif
                    @if($verTeam)
                        <span class="ver-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            {{ $verTeam->name }}
                        </span>
                    @endif
                </div>
                @if($verification->doc_diploma || $verification->doc_id || $verification->doc_pension)
                    <div class="ver-card-docs">
                        @foreach([['doc_diploma', 'Диплом'], ['doc_id', 'Удостоверение'], ['doc_pension', 'Выписка ПФ']] as [$field, $label])
                            @if($verification->$field)
                                <a href="{{ Storage::url($verification->$field) }}" target="_blank" class="ver-doc-link">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span>{{ $label }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
                <div class="ver-card-actions">
                    <form method="POST" action="{{ route('admin.users.verify', $verification) }}">
                        @csrf
                        <button type="submit" class="btn btn-approve">Одобрить</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.reject', $verification) }}">
                        @csrf
                        <button type="submit" class="btn btn-reject">Отклонить</button>
                    </form>
                </div>
            </div>
        @empty
            <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);font-size:0.8125rem;">
                Нет заявок на верификацию
            </div>
        @endforelse
    </div>

    {{-- Pending Team Applications --}}
    <div class="section-block">
        <div class="section-title">Заявки в команды</div>

        @forelse($pendingApplications as $application)
            <div class="team-request-card">
                <div class="request-avatar" style="background:linear-gradient(135deg,var(--warning),#d97706);">{{ mb_substr($application->user->name, 0, 2) }}</div>
                <div class="team-request-text">
                    <div class="team-request-flow">{{ $application->user->name }} <span>&rarr; {{ $application->team->name }}</span></div>
                    <div class="request-meta">{{ $application->created_at->format('d.m.Y') }}</div>
                </div>
            </div>
        @empty
            <div style="padding:var(--space-md);text-align:center;color:var(--text-muted);font-size:0.8125rem;">
                Нет заявок в команды
            </div>
        @endforelse
    </div>

    {{-- Recent Payments --}}
    @if(isset($recentPayments) && $recentPayments->count())
    <div class="section-block">
        <div class="section-title">Последние оплаты</div>

        @foreach($recentPayments as $payment)
            <div class="pay-card">
                <div class="pay-card-top">
                    <span class="pay-user">{{ $payment->user->name }}</span>
                    <span class="pay-amount">{{ number_format($payment->amount) }} &#8376;</span>
                </div>
                <div class="pay-meta">{{ $payment->created_at->format('d.m.Y') }}</div>
            </div>
        @endforeach
    </div>
    @endif
@endsection
