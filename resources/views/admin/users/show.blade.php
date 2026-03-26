@extends('layouts.admin')

@section('title', $user->name)

@push('styles')
<style>
    .profile-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-lg);text-align:center;margin-bottom:var(--space-md)}
    .profile-avatar{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;color:#fff;margin:0 auto var(--space-md)}
    .profile-name{font-size:1.125rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .profile-detail{font-size:0.75rem;color:var(--text-secondary);margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .profile-detail span{color:var(--text-muted)}
    .section-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-md)}
    .section-label{font-size:0.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-sm)}
    .status-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-sm)}
    .status-text{font-size:0.75rem;color:var(--text-secondary)}
    .tag{display:inline-flex;align-items:center;height:22px;padding:0 10px;border-radius:var(--radius-full);font-size:0.6875rem;font-weight:600}
    .tag-green{background:rgba(34,197,94,0.12);color:var(--primary)}
    .tag-yellow{background:rgba(245,158,11,0.12);color:var(--warning)}
    .tag-red{background:rgba(239,68,68,0.12);color:var(--error)}
    .tag-gray{background:rgba(148,163,184,0.1);color:var(--text-muted)}
    .doc-file{display:flex;align-items:center;gap:var(--space-sm);padding:var(--space-sm) var(--space-md);background:var(--bg-input);border:1px solid var(--border-input);border-radius:var(--radius-sm);margin-bottom:var(--space-md);text-decoration:none;color:inherit}
    .doc-file svg{width:20px;height:20px;color:var(--accent-blue);flex-shrink:0}
    .doc-file-name{font-size:0.75rem;font-weight:500;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .verify-actions{display:flex;gap:var(--space-sm)}
    .btn-verify{flex:1;height:38px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);font-size:0.8125rem;font-weight:600;font-family:inherit;cursor:pointer}
    .btn-verify:hover{background:var(--primary-hover)}
    .btn-reject-sm{flex:1;height:38px;background:transparent;color:var(--error);border:1px solid var(--error);border-radius:var(--radius-sm);font-size:0.8125rem;font-weight:600;font-family:inherit;cursor:pointer}
    .btn-reject-sm:hover{background:rgba(239,68,68,0.1)}
    .payment-row{display:flex;align-items:center;justify-content:space-between;padding:var(--space-sm) 0;border-bottom:1px solid var(--border-subtle)}
    .payment-row:last-child{border-bottom:none}
    .payment-info{flex:1;min-width:0}
    .payment-amount{font-size:0.8125rem;font-weight:600;color:var(--text-primary)}
    .payment-date{font-size:0.6875rem;color:var(--text-muted)}
    .action-buttons{display:flex;flex-direction:column;gap:var(--space-sm);margin-top:var(--space-md)}
    .btn-action{width:100%;height:44px;border-radius:var(--radius-md);font-size:0.8125rem;font-weight:600;font-family:inherit;cursor:pointer;border:none;transition:all var(--transition-fast)}
    .btn-role{background:transparent;color:var(--text-primary);border:1px solid var(--border-input)}
    .btn-role:hover{background:rgba(255,255,255,0.04)}
    .btn-block{background:transparent;color:var(--warning);border:1px solid var(--warning)}
    .btn-block:hover{background:rgba(245,158,11,0.1)}
    .btn-delete{background:transparent;color:var(--error);border:1px solid var(--error)}
    .btn-delete:hover{background:rgba(239,68,68,0.1)}
</style>
@endpush

@section('content')
    {{-- Profile Card --}}
    <div class="profile-card">
        <div class="profile-avatar">{{ mb_substr($user->name, 0, 2) }}</div>
        <div class="profile-name">{{ $user->name }}</div>
        <div class="profile-detail">ИИН: {{ $user->iin }}</div>
        @if($user->email)
            <div class="profile-detail">{{ $user->email }}</div>
        @endif
        @if($user->specialization)
            <div class="profile-detail" style="margin-top:var(--space-sm);">
                <span>Специализация:</span> {{ $user->specialization }}
            </div>
        @endif
        @if($user->city)
            <div class="profile-detail">
                <span>Город:</span> {{ $user->city }}
            </div>
        @endif
        <div class="profile-detail">
            <span>Регистрация:</span> {{ $user->created_at->format('d.m.Y') }}
        </div>
        @if($user->role)
            <div class="profile-detail">
                <span>Роль:</span> {{ $user->role }}
            </div>
        @endif
    </div>

    {{-- Verification Section --}}
    <div class="section-card">
        <div class="section-label">Верификация</div>
        <div class="status-row">
            <span class="status-text">Статус</span>
            @if($user->verification_status === 'verified')
                <span class="tag tag-green">Верифицирован</span>
            @elseif($user->verification_status === 'pending')
                <span class="tag tag-yellow">Ожидает</span>
            @elseif($user->verification_status === 'rejected')
                <span class="tag tag-red">Отклонен</span>
            @else
                <span class="tag tag-gray">Не подано</span>
            @endif
        </div>

        @if($user->verification_document)
            <div class="section-label" style="margin-top:var(--space-sm);">Загруженный документ</div>
            <a href="{{ Storage::url($user->verification_document) }}" target="_blank" class="doc-file">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span class="doc-file-name">{{ basename($user->verification_document) }}</span>
            </a>
        @endif

        @if($user->verification_status === 'pending')
            <div class="verify-actions">
                <form method="POST" action="{{ route('admin.users.verify', $user) }}" style="flex:1">
                    @csrf
                    <button type="submit" class="btn-verify" style="width:100%">Верифицировать</button>
                </form>
                <form method="POST" action="{{ route('admin.users.reject', $user) }}" style="flex:1">
                    @csrf
                    <button type="submit" class="btn-reject-sm" style="width:100%">Отклонить</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Payment History --}}
    @if(isset($user->payments) && $user->payments->count())
    <div class="section-card">
        <div class="section-label">История оплат</div>
        @foreach($user->payments as $payment)
            <div class="payment-row">
                <div class="payment-info">
                    <div class="payment-amount">{{ number_format($payment->amount) }} &#8376;</div>
                    <div class="payment-date">{{ $payment->created_at->format('d.m.Y') }}</div>
                </div>
                @if($payment->status === 'confirmed' || $payment->status === 'paid')
                    <span class="tag tag-green">Оплачено</span>
                @elseif($payment->status === 'pending')
                    <span class="tag tag-yellow">Ожидает</span>
                @else
                    <span class="tag tag-red">{{ $payment->status }}</span>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="action-buttons">
        {{-- Change Role --}}
        <form method="POST" action="{{ route('admin.users.role', $user) }}">
            @csrf
            @method('PATCH')
            <select name="role" onchange="this.form.submit()" class="btn-action btn-role" style="text-align:center;appearance:none;cursor:pointer;">
                <option value="" disabled selected>Назначить роль</option>
                <option value="user" {{ $user->role === 'user' ? 'disabled' : '' }}>Пользователь</option>
                <option value="admin" {{ $user->role === 'admin' ? 'disabled' : '' }}>Админ</option>
                <option value="superadmin" {{ $user->role === 'superadmin' ? 'disabled' : '' }}>Суперадмин</option>
            </select>
        </form>

        {{-- Block/Unblock --}}
        <form method="POST" action="{{ route('admin.users.block', $user) }}">
            @csrf
            @method('PATCH')
            @if($user->is_blocked)
                <button type="submit" class="btn-action btn-role">Разблокировать</button>
            @else
                <button type="submit" class="btn-action btn-block">Заблокировать</button>
            @endif
        </form>

        {{-- Delete --}}
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action btn-delete">Удалить</button>
        </form>
    </div>
@endsection
