@extends('layouts.app')

@section('title', 'Редактировать профиль')

@push('styles')
<style>
    .avatar-upload{display:flex;flex-direction:column;align-items:center;gap:var(--space-md);margin-bottom:var(--space-lg)}
    .avatar-upload-circle{width:80px;height:80px;border-radius:50%;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:var(--text-muted);position:relative;cursor:pointer;border:2px dashed var(--border-input);transition:border-color var(--transition-fast)}
    .avatar-upload-circle:hover{border-color:var(--accent-blue)}
    .avatar-upload-circle svg{position:absolute;bottom:-2px;right:-2px;width:24px;height:24px;background:var(--accent-blue);border-radius:50%;padding:4px;color:white}
    .avatar-upload-hint{font-size:0.75rem;color:var(--text-muted)}
    .form-select{width:100%;height:48px;padding:0 var(--space-md);background:var(--bg-input);border:1px solid var(--border-input);border-radius:var(--radius-md);color:var(--text-primary);font-family:inherit;font-size:0.9375rem;outline:none;transition:all var(--transition-fast);-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}
    .form-select:focus{background-color:var(--bg-input-focus);border-color:var(--border-input-focus);box-shadow:0 0 0 3px rgba(59,130,246,0.1)}
    .form-select option{background:var(--bg-card);color:var(--text-primary)}
    .form-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-lg);position:relative;overflow:hidden}
    .form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent)}
</style>
@endpush

@section('content')

    <h1 class="heading-md mb-lg">Редактировать профиль</h1>

    <div class="form-card">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar upload --}}
            <div class="avatar-upload">
                <label class="avatar-upload-circle">
                    {{ mb_substr($user->name, 0, 2) }}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <input type="file" name="avatar" accept="image/*" hidden>
                </label>
                <div class="avatar-upload-hint">Нажмите, чтобы загрузить фото</div>
                @error('avatar')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Name --}}
            <div class="form-group">
                <label class="form-label">Имя и фамилия</label>
                <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Введите имя и фамилию">
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Specialization --}}
            <div class="form-group">
                <label class="form-label">Специализация</label>
                <input type="text" name="specialization" class="form-input @error('specialization') is-invalid @enderror" value="{{ old('specialization', $user->specialization) }}" placeholder="Например: Кардиолог">
                @error('specialization')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- City --}}
            <div class="form-group">
                <label class="form-label">Город</label>
                <select name="city" class="form-select @error('city') is-invalid @enderror">
                    @php
                        $cities = [
                            'Алматы' => 'Алматы',
                            'Астана' => 'Астана',
                            'Шымкент' => 'Шымкент',
                            'Караганда' => 'Караганда',
                            'Актобе' => 'Актобе',
                            'Тараз' => 'Тараз',
                            'Павлодар' => 'Павлодар',
                            'Семей' => 'Семей',
                            'Атырау' => 'Атырау',
                            'Костанай' => 'Костанай',
                        ];
                    @endphp
                    @foreach($cities as $value => $label)
                        <option value="{{ $value }}" {{ old('city', $user->city) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('city')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top:var(--space-md)">Сохранить</button>
        </form>
    </div>

@endsection
