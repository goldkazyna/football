@extends('layouts.admin')

@section('title', 'Создание команды')

@push('styles')
<style>
    .page-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-lg)}
    .form-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:var(--space-lg);margin-bottom:var(--space-lg)}
</style>
@endpush

@section('content')
    <div class="page-title">Создание команды</div>

    <form method="POST" action="{{ route('admin.teams.store') }}">
        @csrf

        <div class="form-card">
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
                    @foreach(['Алматы','Астана','Шымкент','Караганда','Актобе','Тараз','Павлодар','Усть-Каменогорск','Семей','Атырау','Костанай','Кызылорда','Петропавловск','Актау','Туркестан'] as $city)
                        <option value="{{ $city }}" {{ old('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
                @error('city')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Капитан</label>
                @if($captainOptions->isEmpty())
                    <div style="padding:var(--space-md);background:rgba(245,158,11,0.1);border-radius:var(--radius-md);font-size:0.8125rem;color:var(--warning);">
                        Сначала добавьте капитана в <a href="{{ route('whitelist.index') }}" style="color:var(--warning);text-decoration:underline;">белый список</a> с ролью «Капитан»
                    </div>
                @else
                    <select name="captain_iin" class="form-select" required>
                        <option value="" disabled {{ old('captain_iin') ? '' : 'selected' }}>Выберите капитана</option>
                        @foreach($captainOptions as $opt)
                            <option value="{{ $opt->iin }}" {{ old('captain_iin') === $opt->iin ? 'selected' : '' }}>
                                {{ $opt->iin }}
                                @if($opt->name) — {{ $opt->name }} @endif
                                @if(!$opt->registered) (не зарегистрирован) @endif
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('captain_iin')
                    <div style="color:var(--error);font-size:0.75rem;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Описание <span style="color:var(--text-muted);font-weight:400;">(необязательно)</span></label>
                <textarea name="description" class="form-input" placeholder="Краткое описание команды..." rows="3" style="height:auto;padding-top:12px;resize:vertical;">{{ old('description') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;height:48px;">Создать команду</button>
    </form>
@endsection
