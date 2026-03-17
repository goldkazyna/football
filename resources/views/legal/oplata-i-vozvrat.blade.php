@extends('layouts.guest')

@section('title', 'Оплата и возврат')

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
    .legal-requisites { background: var(--bg-card); border-radius: var(--radius-md); padding: var(--space-md); margin-top: var(--space-md); }
    .legal-requisites p { margin-bottom: 2px; }
</style>
@endpush

@section('content')
<div class="legal-page">
    <a href="{{ route('login') }}" class="legal-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Назад
    </a>

    <h1>ОПЛАТА И ВОЗВРАТ</h1>
    <p class="legal-subtitle">ОО «Казахстанское общество врачей любителей футбола»<br>Интернет-портал: kmft.kz</p>

    <h2>1. Членский взнос</h2>
    <p>Участие в турнирах на платформе kmft.kz является платным. С каждого Участника — Капитана или Игрока — взимается ежегодный членский взнос.</p>
    <table class="legal-table">
        <tr><th>Параметр</th><th>Условие</th></tr>
        <tr><td>Размер взноса</td><td>15 000 тенге (₸) в год</td></tr>
        <tr><td>Кто платит</td><td>Каждый Участник самостоятельно — Капитан и Игрок отдельно</td></tr>
        <tr><td>Срок действия</td><td>Один календарный год с момента оплаты</td></tr>
        <tr><td>Что даёт взнос</td><td>Допуск ко всем турнирам сезона на kmft.kz</td></tr>
        <tr><td>Валюта</td><td>Тенге Республики Казахстан (₸)</td></tr>
    </table>

    <h2>2. Способ оплаты</h2>
    <p>Оплата членского взноса осуществляется онлайн через портал kmft.kz банковской картой (Visa, Mastercard, карты казахстанских банков).</p>
    <p>Все платежи обрабатываются через сертифицированный платёжный шлюз с использованием протокола шифрования SSL. Данные банковской карты не сохраняются на серверах Организатора.</p>

    <h2>3. Порядок оплаты</h2>
    <ul>
        <li>Зарегистрируйтесь на kmft.kz (для Капитана — после внесения номера Администратором; для Игрока — по приглашению Капитана)</li>
        <li>Войдите в личный кабинет и перейдите в раздел оплаты</li>
        <li>Проверьте сумму: 15 000 ₸</li>
        <li>Введите данные банковской карты в защищённой форме оплаты</li>
        <li>После успешной оплаты статус вашего аккаунта автоматически обновится — доступ к турнирам будет открыт</li>
    </ul>

    <h2>4. Условия возврата</h2>
    <table class="legal-table">
        <tr><th>Ситуация</th><th>Условие возврата</th></tr>
        <tr><td>Отказ в течение 14 дней с оплаты (без участия в турнирах)</td><td>Полный возврат — 15 000 ₸</td></tr>
        <tr><td>Отказ после 14 дней с оплаты</td><td>Возврат не производится</td></tr>
        <tr><td>Участие хотя бы в одном турнире</td><td>Возврат не производится</td></tr>
        <tr><td>Отмена всех турниров сезона по инициативе Организатора</td><td>Полный возврат или зачёт на следующий сезон</td></tr>
    </table>

    <h2>5. Как оформить возврат</h2>
    <ul>
        <li>Направить заявку через форму обратной связи на kmft.kz</li>
        <li>Указать: ФИО, номер телефона, дату оплаты</li>
        <li>Заявка рассматривается в течение 3 (трёх) рабочих дней</li>
        <li>Средства возвращаются на карту, с которой была произведена оплата, в течение 5–10 рабочих дней (в зависимости от банка)</li>
    </ul>

    <h2>6. Что не подлежит возврату</h2>
    <ul>
        <li>Взнос по истечении 14 календарных дней с момента оплаты</li>
        <li>Взнос после участия хотя бы в одном турнире</li>
        <li>Взнос при дисквалификации Участника за нарушение регламента</li>
    </ul>

    <h2>7. Реквизиты для оплаты</h2>
    <div class="legal-requisites">
        <p>Организация: ОО «Казахстанское общество врачей любителей футбола»</p>
        <p>ИИК: KZ4196502F0009697460KZT</p>
        <p>Банк: Филиал АО «ForteBank» в г. Алматы</p>
        <p>БИК: IRTYKZKA</p>
        <p>БИН: 180140041987</p>
    </div>
</div>
@endsection
