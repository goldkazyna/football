@extends('layouts.app')

@section('title', 'Редактирование команды')

@push('styles')
<style>
    .page-title{font-size:1.375rem;font-weight:800;margin-bottom:var(--space-lg)}
    .upload-zone{width:96px;height:96px;border-radius:50%;border:2px dashed var(--border-input);background:var(--bg-input);display:flex;flex-direction:column;align-items:center;justify-content:center;margin:0 auto var(--space-lg);cursor:pointer;transition:all var(--transition-fast);position:relative;overflow:hidden}
    .upload-zone:hover{border-color:var(--accent-blue);background:var(--bg-input-focus)}
    .upload-zone svg{width:24px;height:24px;color:var(--text-muted);margin-bottom:4px}
    .upload-zone span{font-size:0.625rem;color:var(--text-muted);font-weight:600}
    .upload-zone input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer}
    .upload-zone img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
    .submit-section{margin-top:var(--space-lg)}
</style>
@endpush

@section('content')
    <h1 class="page-title">Редактирование команды</h1>

    <form method="POST" action="{{ route('teams.update', $team) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label class="upload-zone" id="logoZone">
            @if($team->logo)
                <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <span>Логотип</span>
            @endif
            <input type="file" name="logo" accept="image/*">
        </label>

        <div class="form-group">
            <label class="form-label">Название команды</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $team->name) }}" required>
            @error('name')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Город</label>
            <select name="city" class="form-select" required>
                <option value="" disabled>Выберите город</option>
                @foreach(['Алматы','Астана','Шымкент','Караганда','Актобе','Тараз','Павлодар','Усть-Каменогорск','Семей','Атырау','Костанай','Кызылорда','Петропавловск','Актау','Туркестан'] as $city)
                    <option value="{{ $city }}" {{ old('city', $team->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
            @error('city')
                <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-input" placeholder="Описание команды..." rows="3" style="height:auto;padding-top:12px;resize:vertical;">{{ old('description', $team->description) }}</textarea>
        </div>

        <div class="submit-section">
            <button type="submit" class="btn btn-primary" style="width:100%;">Сохранить</button>
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
