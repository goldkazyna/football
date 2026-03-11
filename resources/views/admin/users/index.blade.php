@extends('layouts.admin')

@section('title', 'Пользователи')

@push('styles')
<style>
    .page-title-row{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-md)}
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary)}
    .count-badge{display:inline-flex;align-items:center;justify-content:center;height:22px;padding:0 8px;border-radius:var(--radius-full);background:rgba(59,130,246,0.12);color:var(--accent-blue);font-size:0.6875rem;font-weight:700}
    .search-box{margin-bottom:var(--space-md)}
    .search-box .form-input{padding-left:40px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:12px center}
    .user-card{display:block;background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm);text-decoration:none;color:inherit;transition:background var(--transition-fast)}
    .user-card:hover{background:var(--bg-card-hover);color:inherit}
    .user-card-top{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-sm)}
    .user-avatar{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0}
    .user-info{flex:1;min-width:0}
    .user-name{font-size:0.8125rem;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .user-phone{font-size:0.6875rem;color:var(--text-muted)}
    .user-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:2px}
    .tag{display:inline-flex;align-items:center;height:20px;padding:0 8px;border-radius:var(--radius-full);font-size:0.625rem;font-weight:600}
    .tag-green{background:rgba(34,197,94,0.12);color:var(--primary)}
    .tag-yellow{background:rgba(245,158,11,0.12);color:var(--warning)}
    .tag-gray{background:rgba(148,163,184,0.1);color:var(--text-muted)}
    .tag-red{background:rgba(239,68,68,0.12);color:var(--error)}
    .tag-blue{background:rgba(59,130,246,0.12);color:var(--accent-blue)}
    .user-team{font-size:0.6875rem;color:var(--text-secondary);margin-top:var(--space-xs);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .user-team svg{width:12px;height:12px;display:inline;vertical-align:-2px;margin-right:4px}
</style>
@endpush

@section('content')
    <div class="page-title-row">
        <div class="page-title">Пользователи</div>
        <div class="count-badge">{{ $users->total() }}</div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="search-box">
            <input type="text" name="search" class="form-input" placeholder="Поиск по имени или телефону..." value="{{ request('search') }}">
        </div>

        <div class="filters" style="margin-bottom:var(--space-md)">
            <a href="{{ route('admin.users.index', request()->except(['filter', 'page'])) }}" class="filter-btn {{ !request('filter') ? 'active' : '' }}">Все</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['filter' => 'verified'])) }}" class="filter-btn {{ request('filter') === 'verified' ? 'active' : '' }}">Верифицированы</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['filter' => 'pending'])) }}" class="filter-btn {{ request('filter') === 'pending' ? 'active' : '' }}">Ожидают</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['filter' => 'unpaid'])) }}" class="filter-btn {{ request('filter') === 'unpaid' ? 'active' : '' }}">Не оплачено</a>
        </div>
    </form>

    @forelse($users as $user)
        <a href="{{ route('admin.users.show', $user) }}" class="user-card">
            <div class="user-card-top">
                <div class="user-avatar" style="background:linear-gradient(135deg,{{ ['#3b82f6,#6366f1','var(--primary),#16a34a','#f59e0b,#d97706','#64748b,#475569','#8b5cf6,#7c3aed','#ec4899,#db2777'][$loop->index % 6] }})">
                    {{ mb_substr($user->name, 0, 2) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-phone">{{ $user->phone }}</div>
                </div>
            </div>
            <div class="user-tags">
                @if($user->verification_status === 'verified')
                    <span class="tag tag-green">Верифицирован</span>
                @elseif($user->verification_status === 'pending')
                    <span class="tag tag-yellow">Ожидает</span>
                @else
                    <span class="tag tag-gray">Не подано</span>
                @endif

                @if($user->role === 'admin')
                    <span class="tag tag-blue">Админ</span>
                @endif

                @if($user->subscription_active)
                    <span class="tag tag-green">Активна</span>
                @elseif($user->subscription_expired)
                    <span class="tag tag-red">Просрочена</span>
                @endif
            </div>
            <div class="user-team">
                @if($user->team)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    {{ $user->team->name }}
                @else
                    &mdash;
                @endif
            </div>
        </a>
    @empty
        <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);">
            Пользователи не найдены
        </div>
    @endforelse

    @if($users->hasPages())
        <div style="margin-top:var(--space-lg);display:flex;justify-content:center;">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
@endsection
