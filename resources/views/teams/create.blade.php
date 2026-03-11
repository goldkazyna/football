@extends('layouts.app')

@section('title', 'Создание команды')

@push('styles')
<style>
    .page-title{font-size:1.375rem;font-weight:800;margin-bottom:var(--space-lg)}
    .upload-zone{width:96px;height:96px;border-radius:50%;border:2px dashed var(--border-input);background:var(--bg-input);display:flex;flex-direction:column;align-items:center;justify-content:center;margin:0 auto var(--space-lg);cursor:pointer;transition:all var(--transition-fast);position:relative;overflow:hidden}
    .upload-zone:hover{border-color:var(--accent-blue);background:var(--bg-input-focus)}
    .upload-zone svg{width:24px;height:24px;color:var(--text-muted);margin-bottom:4px}
    .upload-zone span{font-size:0.625rem;color:var(--text-muted);font-weight:600}
    .upload-zone input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer}
    .upload-zone img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
    .form-select{width:100%;height:48px;padding:0 var(--space-md);background:var(--bg-input);border:1px solid var(--border-input);border-radius:var(--radius-md);color:var(--text-primary);font-family:inherit;font-size:0.9375rem;outline:none;transition:all var(--transition-fast);appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center}
    .form-select:focus{background-color:var(--bg-input-focus);border-color:var(--border-input-focus);box-shadow:0 0 0 3px rgba(59,130,246,0.1)}
    .form-select option{background:var(--bg-card);color:var(--text-primary)}
    .form-textarea{width:100%;min-height:96px;padding:var(--space-md);background:var(--bg-input);border:1px solid var(--border-input);border-radius:var(--radius-md);color:var(--text-primary);font-family:inherit;font-size:0.9375rem;outline:none;transition:all var(--transition-fast);resize:vertical}
    .form-textarea::placeholder{color:var(--text-muted)}
    .form-textarea:focus{background:var(--bg-input-focus);border-color:var(--border-input-focus);box-shadow:0 0 0 3px rgba(59,130,246,0.1)}
    .submit-section{margin-top:var(--space-lg)}
</style>
@endpush

@section('content')
    <h1 class="page-title">Создание команды</h1>

    <form method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data">
        @csrf

        <label class="upload-zone" id="logoZone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>Логотип</span>
            <input type="file" name="logo" accept="image/*">
        </label>
        @error('logo')
            <div style="color:var(--error);font-size:0.75rem;text-align:center;margin-top:calc(-1 * var(--space-md));margin-bottom:var(--space-md);">{{ $message }}</div>
        @enderror

        <div class="form-group">
            <label class="form-label">Название команды</label>
            <input type="text" name="name" class="form-input" placeholder="Введите название" value="{{ old('name') }}" required>
            @error('name')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Город</label>
            <select name="city" class="form-select" required>
                <option value="" disabled {{ old('city') ? '' : 'selected' }}>Выберите город</option>
                @foreach(['Алматы','Астана','Шымкент','Караганда','Актобе','Павлодар','Семей','Атырау','Актау','Орал','Костанай','Петропавловск','Талдыкорган','Тараз','Кокшетау','Туркестан'] as $city)
                    <option value="{{ $city }}" {{ old('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
            @error('city')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Девиз / Описание</label>
            <textarea name="description" class="form-textarea" placeholder="Расскажите о вашей команде...">{{ old('description') }}</textarea>
            @error('description')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="submit-section">
            <button type="submit" class="btn btn-primary">Создать команду</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.querySelector('#logoZone input[type="file"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const zone = document.getElementById('logoZone');
                zone.querySelector('svg')?.remove();
                zone.querySelector('span')?.remove();
                let img = zone.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    zone.appendChild(img);
                }
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
