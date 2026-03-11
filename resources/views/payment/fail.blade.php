@extends('layouts.app')

@section('title', 'Ошибка оплаты')

@push('styles')
<style>
    .result-page{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:50vh;text-align:center;padding:var(--space-xl)}
    .result-icon{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:var(--space-md)}
    .result-icon svg{width:32px;height:32px}
    .result-icon.fail{background:rgba(239,68,68,0.12)}
    .result-icon.fail svg{color:var(--error)}
    .result-title{font-size:1.25rem;font-weight:800;color:var(--text-primary);margin-bottom:var(--space-xs)}
    .result-text{font-size:0.875rem;color:var(--text-secondary);margin-bottom:var(--space-lg);max-width:300px}
    .result-actions{display:flex;flex-direction:column;gap:var(--space-sm);width:100%;max-width:280px}
</style>
@endpush

@section('content')
    <div class="result-page">
        <div class="result-icon fail">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="result-title">Ошибка оплаты</div>
        <div class="result-text">К сожалению, оплата не прошла. Пожалуйста, попробуйте еще раз или свяжитесь с администратором.</div>
        <div class="result-actions">
            <a href="{{ route('payment.create') }}" class="btn btn-primary">Попробовать снова</a>
            <a href="{{ route('dashboard') }}" class="btn" style="background:transparent;color:var(--text-secondary);border:1px solid var(--border-input);">Вернуться на главную</a>
        </div>
    </div>
@endsection
