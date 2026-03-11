@extends('layouts.app')

@section('title', 'Оплата успешна')

@push('styles')
<style>
    .result-page{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:50vh;text-align:center;padding:var(--space-xl)}
    .result-icon{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:var(--space-md)}
    .result-icon svg{width:32px;height:32px}
    .result-icon.success{background:rgba(34,197,94,0.12)}
    .result-icon.success svg{color:var(--primary)}
    .result-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .result-text{font-size:0.875rem;color:var(--text-secondary);margin-bottom:var(--space-lg);max-width:300px}
</style>
@endpush

@section('content')
    <div class="result-page">
        <div class="result-icon success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="result-title">Оплата прошла успешно!</div>
        <div class="result-text">Ваш членский взнос оплачен. Спасибо за участие в лиге!</div>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Вернуться на главную</a>
    </div>
@endsection
