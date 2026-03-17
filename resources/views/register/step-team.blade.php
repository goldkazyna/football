@extends('layouts.guest')

@section('title', 'Регистрация — Команда')

@push('styles')
<style>
    .steps{display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:var(--space-xl)}
    .step-dot{width:8px;height:8px;border-radius:50%;background:var(--border-input);transition:all var(--transition-base)}
    .step-dot.active{width:24px;border-radius:4px;background:var(--primary)}
    .step-dot.done{background:var(--primary)}
    .register-header{text-align:center;margin-bottom:var(--space-lg)}
    .register-header h1{font-size:1.375rem;font-weight:800;color:var(--text-primary);margin-bottom:6px;letter-spacing:-0.02em}
    .register-header p{font-size:0.875rem;color:var(--text-secondary)}
    .assigned-card{background:var(--bg-card);border:1px solid rgba(34,197,94,0.3);border-radius:var(--radius-lg);padding:var(--space-lg);text-align:center;margin-bottom:var(--space-lg)}
    .assigned-card .team-icon{width:56px;height:56px;border-radius:50%;background:rgba(34,197,94,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto var(--space-md)}
    .assigned-card .team-name{font-size:1.125rem;font-weight:800;color:var(--text-primary);margin-bottom:4px}
    .assigned-card .team-city{font-size:0.8125rem;color:var(--text-muted);margin-bottom:var(--space-sm)}
    .assigned-card .assigned-label{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:var(--radius-full);background:rgba(34,197,94,0.1);color:var(--primary);font-size:0.75rem;font-weight:700}
    .assigned-card .assigned-label svg{width:14px;height:14px}
    .nav-buttons{display:flex;gap:var(--space-sm)}
    .btn-back{width:48px;min-width:48px;height:48px;flex-shrink:0;background:transparent;border:1px solid var(--border-input);border-radius:var(--radius-md);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all var(--transition-fast)}
    .btn-back:hover{background:rgba(255,255,255,0.04)}
    .btn-back svg{width:20px;height:20px}
</style>
@endpush

@section('content')
    <!-- Steps -->
    <div class="steps animate-in animate-in-delay-1">
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot done"></div>
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
    </div>

    <!-- Header -->
    <div class="register-header animate-in animate-in-delay-1">
        <h1>Команда</h1>
        <p>
            @if($assignedTeam)
                Вы добавлены в команду капитаном
            @else
                Создайте свою команду или пропустите этот шаг
            @endif
        </p>
    </div>

    <form class="animate-in animate-in-delay-2" method="POST" action="{{ route('register.process', 'team') }}">
        @csrf

        @if($assignedTeam)
            {{-- Player was added by a captain — show assigned team --}}
            <input type="hidden" name="team_mode" value="assigned">
            <input type="hidden" name="team_id" value="{{ $assignedTeam->id }}">

            <div class="assigned-card">
                <div class="team-icon">⚽</div>
                <div class="team-name">{{ $assignedTeam->name }}</div>
                <div class="team-city">{{ $assignedTeam->city }}</div>
                <div class="assigned-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Ваша команда
                </div>
            </div>
        @else
            {{-- No assigned team — option to create or skip --}}
            <input type="hidden" name="team_mode" value="create" id="teamMode">

            <div id="createForm">
                <div class="form-group">
                    <label class="form-label">Название команды</label>
                    <input type="text" name="team_name" class="form-input @error('team_name') is-invalid @enderror"
                           placeholder="Например: Medics United"
                           value="{{ old('team_name', $data['team_name'] ?? '') }}">
                    @error('team_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Город команды</label>
                    <select name="team_city" class="form-select @error('team_city') is-invalid @enderror">
                        <option value="" disabled {{ old('team_city', $data['team_city'] ?? '') == '' ? 'selected' : '' }}>Выберите город</option>
                        @foreach(['Алматы', 'Астана', 'Шымкент', 'Караганда', 'Актобе', 'Тараз', 'Павлодар', 'Усть-Каменогорск', 'Семей', 'Атырау', 'Костанай', 'Кызылорда', 'Петропавловск', 'Актау', 'Туркестан'] as $city)
                            <option value="{{ $city }}" {{ old('team_city', $data['team_city'] ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    @error('team_city')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Описание <span style="color:var(--text-muted);font-weight:400;">(необязательно)</span></label>
                    <textarea name="team_description" class="form-input" placeholder="Краткое описание команды..."
                              rows="3" style="height:auto;padding-top:12px;resize:vertical;">{{ old('team_description', $data['team_description'] ?? '') }}</textarea>
                </div>
            </div>
        @endif

        <div class="nav-buttons">
            <a href="{{ route('register.step', 'profile') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            @if($assignedTeam)
                <button type="submit" class="btn btn-primary" style="flex:1;">Далее</button>
            @else
                <button type="submit" class="btn btn-primary" style="flex:1;">Создать команду</button>
                <button type="submit" class="btn btn-primary" style="flex:1;background:transparent;border:1px solid var(--border-input);color:var(--text-secondary);" onclick="document.getElementById('teamMode').value='skip'">Пропустить</button>
            @endif
        </div>
    </form>

    <div class="auth-footer animate-in animate-in-delay-4">
        Шаг 4 из 6
    </div>
@endsection
