@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Белый список')

@push('styles')
<style>
    .page-title-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-md);gap:var(--space-sm)}
    .page-title-left{display:flex;align-items:center;gap:var(--space-sm);min-width:0}
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);white-space:nowrap}
    .count-badge{display:inline-flex;align-items:center;justify-content:center;height:22px;padding:0 8px;border-radius:var(--radius-full);background:rgba(59,130,246,0.12);color:var(--accent-blue);font-size:0.6875rem;font-weight:700;flex-shrink:0}
    .wl-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:var(--space-md);margin-bottom:var(--space-sm)}
    .wl-card-top{display:flex;align-items:center;justify-content:space-between;gap:var(--space-sm);margin-bottom:6px}
    .wl-phone{font-size:0.875rem;font-weight:700;color:var(--text-primary);font-variant-numeric:tabular-nums}
    .wl-delete{width:30px;height:30px;border-radius:50%;background:rgba(239,68,68,0.1);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background var(--transition-fast)}
    .wl-delete:hover{background:rgba(239,68,68,0.2)}
    .wl-delete svg{width:14px;height:14px;color:var(--error)}
    .wl-meta{font-size:0.6875rem;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .wl-registered{display:flex;align-items:center;gap:4px;margin-top:4px;font-size:0.6875rem}
    .wl-registered svg{width:14px;height:14px;flex-shrink:0}
    .wl-registered.yes{color:var(--primary)}
    .wl-registered.no{color:var(--text-muted)}
    .modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:200;align-items:flex-end;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-content{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg) var(--radius-lg) 0 0;padding:var(--space-lg);width:100%;max-width:480px}
    .modal-title{font-size:1rem;font-weight:700;color:var(--text-primary);margin-bottom:var(--space-md)}
    .modal-actions{display:flex;gap:var(--space-sm);margin-top:var(--space-md)}
    .modal-actions .btn{flex:1;height:44px}
    .btn-cancel{background:transparent;color:var(--text-secondary);border:1px solid var(--border-input);border-radius:var(--radius-md);font-size:0.875rem;font-weight:600;font-family:inherit;cursor:pointer;height:44px;flex:1}
    @media(min-width:480px){
        .modal-overlay{align-items:center}
        .modal-content{border-radius:var(--radius-lg);max-width:400px}
    }
</style>
@endpush

@section('content')
    <div class="page-title-row">
        <div class="page-title-left">
            <div class="page-title">Белый список</div>
            <div class="count-badge">{{ $items->total() }}</div>
        </div>
        <button class="btn-sm btn-sm-primary" onclick="document.getElementById('addModal').classList.add('open')">+ Добавить</button>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('whitelist.index') }}" style="margin-bottom:var(--space-md);">
        <input type="text" name="search" class="form-input" placeholder="Поиск по номеру..." value="{{ request('search') }}" style="width:100%;">
    </form>

    {{-- Phone List --}}
    @forelse($items as $item)
        <div class="wl-card">
            <div class="wl-card-top">
                <span class="wl-phone">{{ $item->phone }}</span>
                <form method="POST" action="{{ route('whitelist.destroy', $item) }}" onsubmit="return confirm('Удалить номер из белого списка?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="wl-delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </form>
            </div>
            <div class="wl-meta">
                @if($item->role === 'captain')
                    <span style="color:var(--warning);font-weight:700;">Капитан</span> &middot;
                @endif
                Добавил: {{ $item->addedByUser->name ?? '—' }} &middot; {{ $item->created_at?->format('d.m.Y') ?? '—' }}
            </div>
            @if(in_array($item->phone, $registeredPhones))
                <div class="wl-registered yes">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Зарегистрирован
                </div>
            @else
                <div class="wl-registered no">&mdash;</div>
            @endif
        </div>
    @empty
        <div style="padding:var(--space-lg);text-align:center;color:var(--text-muted);">
            Белый список пуст
        </div>
    @endforelse

    {{ $items->links() }}

    {{-- Add Number Modal --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <div class="modal-title">Добавить номер</div>
            <form method="POST" action="{{ route('whitelist.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Номер телефона</label>
                    <input type="tel" name="phone" class="form-input" placeholder="+7 7XX XXX-XX-XX" required>
                </div>
                @error('phone')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:var(--space-xs);">{{ $message }}</div>
                @enderror
                @if(auth()->user()->isSuperAdmin())
                    <div class="form-group">
                        <label class="form-label">Роль</label>
                        <select name="role" class="form-select">
                            <option value="player">Игрок</option>
                            <option value="captain">Капитан</option>
                        </select>
                    </div>
                @endif
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('addModal').classList.remove('open')">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
</script>
@endpush
