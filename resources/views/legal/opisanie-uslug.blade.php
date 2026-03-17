@extends('layouts.guest')

@section('title', 'Описание услуг')

@push('styles')
<style>
    .legal-page { text-align: left; max-width: 640px; margin: 0 auto; }
    .legal-page h1 { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 4px; text-align: center; }
    .legal-page .legal-subtitle { font-size: 0.8125rem; color: var(--text-secondary); text-align: center; margin-bottom: var(--space-lg); line-height: 1.6; }
    .legal-page h2 { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-top: var(--space-lg); margin-bottom: var(--space-sm); }
    .legal-page p, .legal-page li { font-size: 0.8125rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: var(--space-xs); }
    .legal-page ul { padding-left: 1.25rem; margin-bottom: var(--space-sm); }
    .legal-page ul li { list-style: disc; }
    .legal-back { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8125rem; color: var(--accent-blue); margin-bottom: var(--space-md); }
    .legal-back svg { width: 16px; height: 16px; }
    .legal-table { width: 100%; border-collapse: collapse; margin: var(--space-sm) 0 var(--space-md); font-size: 0.8125rem; }
    .legal-table th, .legal-table td { border: 1px solid var(--border-primary); padding: 8px 12px; text-align: left; color: var(--text-secondary); }
    .legal-table th { background: var(--bg-card); color: var(--text-primary); font-weight: 600; }
</style>
@endpush

@section('content')
<div class="legal-page">
    <a href="{{ route('login') }}" class="legal-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Назад
    </a>

    <h1>ОПИСАНИЕ УСЛУГ</h1>
    <p class="legal-subtitle">ОО «Казахстанское общество врачей любителей футбола»<br>Интернет-портал: kmft.kz</p>

    <h2>1. О платформе</h2>
    <p>KMFT.KZ — закрытый интернет-портал Общественного объединения «Казахстанское общество врачей любителей футбола», предназначенный для организации любительских футбольных турниров среди медицинских работников Казахстана.</p>
    <p>Портал обеспечивает полный цикл участия: регистрацию команды, оплату членского взноса, формирование состава, доступ к расписанию и результатам турниров.</p>

    <h2>2. Система доступа</h2>
    <p>Регистрация на платформе является закрытой. Участниками могут стать только лица, допущенные Администратором системы. Доступ предоставляется в следующем порядке:</p>
    <table class="legal-table">
        <tr><th>Шаг</th><th>Действие</th></tr>
        <tr><td>1. Внесение Капитана</td><td>Администратор добавляет номер телефона Капитана в систему</td></tr>
        <tr><td>2. Регистрация Капитана</td><td>Капитан регистрируется на kmft.kz по своему номеру телефона</td></tr>
        <tr><td>3. Оплата Капитана</td><td>Капитан уплачивает ежегодный членский взнос — 15 000 ₸</td></tr>
        <tr><td>4. Формирование команды</td><td>Капитан приглашает Игроков в свою команду через портал</td></tr>
        <tr><td>5. Регистрация Игрока</td><td>Игрок регистрируется по приглашению и уплачивает взнос — 15 000 ₸</td></tr>
    </table>
    <p>Лица без приглашения или без внесения Администратором не могут зарегистрироваться на портале.</p>

    <h2>3. Членский взнос</h2>
    <p>Размер взноса: 15 000 тенге (₸) в год с каждого Участника.</p>
    <p>Взнос является ежегодным и даёт право на участие во всех турнирах сезона. Оплачивается каждым Участником самостоятельно — Капитаном и Игроками отдельно.</p>

    <h2>4. Форматы турниров</h2>
    <table class="legal-table">
        <tr><th>Формат</th><th>Описание</th></tr>
        <tr><td>Мини-футбол 5×5</td><td>Площадки с малыми воротами, 2 тайма по 15–20 минут</td></tr>
        <tr><td>Футбол 7×7</td><td>Площадки среднего размера, 2 тайма по 25 минут</td></tr>
        <tr><td>Футбол 11×11</td><td>Классический формат на полноразмерном поле</td></tr>
        <tr><td>Смешанные форматы</td><td>По отдельному регламенту, условия публикуются в карточке турнира</td></tr>
    </table>

    <h2>5. Что входит в членский взнос</h2>
    <ul>
        <li>Допуск к участию во всех турнирах сезона</li>
        <li>Аренда спортивного объекта на время матчей</li>
        <li>Судейство и хронометраж</li>
        <li>Публикация результатов и статистики на портале kmft.kz</li>
        <li>Грамоты и призы победителям (при наличии в регламенте турнира)</li>
    </ul>

    <h2>6. Дополнительно</h2>
    <p>Конкретное расписание, требования к форме и иные условия публикуются в карточке каждого турнира на портале kmft.kz. Организатор вправе устанавливать дополнительные требования к участникам (возраст, медицинский допуск и др.).</p>
    <p>По всем вопросам: kmft.kz | г. Алматы, ул. Полежаева, 30А/8</p>
</div>
@endsection
