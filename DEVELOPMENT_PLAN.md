# ПЛАН РАЗРАБОТКИ — Платформа футбольных турниров врачей Казахстана
> Чеклист для Claude Code. Выполнять строго по порядку внутри каждого этапа.

---

## 🗂 Стек технологий
- **Backend:** Laravel 11, PHP 8.3
- **Frontend:** Blade + Alpine.js + Tailwind CSS (или Laravel + Inertia.js + Vue 3)
- **БД:** MySQL 8
- **Очереди:** Laravel Queues (database driver на MVP)
- **Хранилище файлов:** Laravel Storage (local, с возможностью перейти на S3)
- **Telegram Bot API:** для уведомлений в канал
- **Эквайринг:** Kaspi / CloudPayments / Freedom Pay (TBD)
- **Авторизация:** Laravel Sanctum / Fortify

---

## ⚡ ЭТАП 1 — MVP (до 25 марта 2026)
**Цель:** регистрация, верификация, оплата взноса, команды.

---

### 📁 1.0 Инициализация проекта

- [ ] Создать новый проект: `laravel new football-doctors --git`
- [ ] Настроить `.env`: DB, APP_URL, MAIL, QUEUE_CONNECTION=database
- [ ] Установить зависимости:
  - [ ] `composer require laravel/sanctum`
  - [ ] `composer require intervention/image` (для обработки фото)
  - [ ] `npm install -D tailwindcss alpinejs` (если Blade-стек)
  - [ ] Настроить `tailwind.config.js`, `vite.config.js`
- [ ] Создать базовый layout: `layouts/app.blade.php`, `layouts/admin.blade.php`, `layouts/guest.blade.php`
- [ ] Настроить `AppServiceProvider`: локаль, timezone (Asia/Almaty)
- [ ] Создать `.gitignore`, закоммитить начальную структуру

---

### 📁 1.1 База данных — Миграции

#### Пользователи и роли
- [ ] Миграция `users`:
  - `id`, `name`, `phone` (unique), `email` (nullable), `password` (nullable), `telegram_id` (nullable), `avatar` (nullable)
  - `role` (enum: superadmin, tournament_admin, captain, player)
  - `verification_status` (enum: pending, approved, rejected)
  - `verification_document` (nullable)
  - `subscription_status` (enum: active, inactive)
  - `subscription_expires_at` (nullable)
  - `is_blocked` (boolean, default false)
  - `city` (nullable), `specialization` (nullable)
  - `timestamps`

- [ ] Миграция `whitelist`:
  - `id`, `phone` (unique), `added_by` (FK users), `created_at`

#### Команды
- [ ] Миграция `teams`:
  - `id`, `name`, `logo` (nullable), `city`, `description` (nullable)
  - `captain_id` (FK users, nullable)
  - `timestamps`

- [ ] Миграция `team_members`:
  - `id`, `team_id` (FK), `user_id` (FK)
  - `status` (enum: pending, approved, rejected)
  - `joined_at` (nullable), `left_at` (nullable)
  - `timestamps`

#### Оплата
- [ ] Миграция `payments`:
  - `id`, `user_id` (FK), `amount`, `currency` (default: KZT)
  - `status` (enum: pending, confirmed, failed)
  - `payment_method`, `transaction_id` (nullable)
  - `confirmed_by` (FK users, nullable — для ручного подтверждения)
  - `valid_from`, `valid_until`
  - `timestamps`

#### Турниры (добавить в Этапе 2, но создать структуру сейчас)
- [ ] Миграция `tournaments` — создать пустую, заполнить в Этапе 2
- [ ] Миграция `matches` — создать пустую, заполнить в Этапе 2

---

### 📁 1.2 Модели и связи

- [ ] `User` модель:
  - [ ] Связи: `team()` (через team_members), `payments()`, `addedToWhitelist()`
  - [ ] Скоупы: `verified()`, `activeSubscription()`, `blocked()`
  - [ ] Метод: `isActive()` — верифицирован + подписка активна
  - [ ] Метод: `currentTeam()` — возвращает одобренную команду

- [ ] `Team` модель:
  - [ ] Связи: `captain()`, `members()` (через team_members), `activeMembersUsers()`
  - [ ] Скоуп: `withActiveMembers()`

- [ ] `TeamMember` модель:
  - [ ] Связи: `team()`, `user()`

- [ ] `Whitelist` модель:
  - [ ] Метод: `isWhitelisted($phone)` — static

- [ ] `Payment` модель:
  - [ ] Связи: `user()`, `confirmedBy()`
  - [ ] Скоуп: `confirmed()`, `pending()`

---

### 📁 1.3 Авторизация и регистрация

#### Middleware
- [ ] `CheckWhitelist` middleware — проверяет номер в белом списке при регистрации
- [ ] `CheckVerified` middleware — проверяет статус верификации
- [ ] `CheckSubscription` middleware — проверяет активность подписки
- [ ] `CheckRole($role)` middleware — проверяет роль пользователя
- [ ] `CheckNotBlocked` middleware — проверяет что пользователь не заблокирован
- [ ] Зарегистрировать все middleware в `bootstrap/app.php`

#### Контроллеры авторизации
- [ ] `Auth/LoginController`:
  - [ ] `showLoginForm()` — GET `/login`
  - [ ] `loginByPhone()` — POST: принять телефон, проверить белый список, OTP (или сразу вход если уже есть)
  - [ ] `loginByTelegram()` — обработка Telegram Login Widget
  - [ ] `logout()` — POST `/logout`

- [ ] `Auth/RegisterController`:
  - [ ] `showStep($step)` — GET `/register/{step}`
  - [ ] `processStep($step)` — POST `/register/{step}`
  - [ ] Шаги: phone → profile → team → verification → payment
  - [ ] Хранить прогресс в сессии

- [ ] `Auth/TelegramController`:
  - [ ] `callback()` — валидация hash от Telegram Login Widget
  - [ ] Проверить номер в белом списке
  - [ ] Создать/найти пользователя

#### Маршруты авторизации
```
GET  /login
POST /login/phone
GET  /login/telegram/callback
GET  /register/{step}
POST /register/{step}
POST /logout
```

---

### 📁 1.4 Личный кабинет игрока

- [ ] `ProfileController`:
  - [ ] `dashboard()` — GET `/dashboard`
  - [ ] `show()` — GET `/profile`
  - [ ] `edit()` — GET `/profile/edit`
  - [ ] `update()` — PUT `/profile`
  - [ ] `payments()` — GET `/profile/payments`

- [ ] `TeamMemberController` (действия игрока):
  - [ ] `myTeam()` — GET `/my-team`
  - [ ] `leaveTeam()` — POST `/my-team/leave`
  - [ ] `changeTeam()` — GET `/team/change`
  - [ ] `applyToTeam($teamId)` — POST `/teams/{team}/apply`

#### Маршруты личного кабинета
```
GET  /dashboard
GET  /profile
GET  /profile/edit
PUT  /profile
GET  /profile/payments
GET  /my-team
POST /my-team/leave
GET  /team/change
POST /teams/{team}/apply
```

---

### 📁 1.5 Команды (публичная часть + капитан)

- [ ] `TeamController`:
  - [ ] `index()` — GET `/teams` — список команд
  - [ ] `show($id)` — GET `/teams/{team}` — страница команды
  - [ ] `create()` — GET `/teams/create`
  - [ ] `store()` — POST `/teams`
  - [ ] `edit($id)` — GET `/teams/{team}/edit`
  - [ ] `update($id)` — PUT `/teams/{team}`

- [ ] `Captain/TeamManageController`:
  - [ ] `applications()` — GET `/my-team/applications` — заявки на вступление
  - [ ] `approveApplication($memberId)` — POST
  - [ ] `rejectApplication($memberId)` — POST
  - [ ] `removeMember($memberId)` — DELETE

#### Маршруты команд
```
GET    /teams
GET    /teams/create
POST   /teams
GET    /teams/{team}
GET    /teams/{team}/edit
PUT    /teams/{team}
GET    /my-team/applications
POST   /my-team/applications/{member}/approve
POST   /my-team/applications/{member}/reject
DELETE /my-team/members/{member}
```

---

### 📁 1.6 Оплата и подписка

- [ ] `PaymentController`:
  - [ ] `create()` — GET `/payment` — экран оплаты
  - [ ] `initiate()` — POST `/payment` — создать запись, редирект на эквайринг
  - [ ] `callback()` — POST `/payment/callback` — webhook от эквайринга
  - [ ] `success()` — GET `/payment/success`
  - [ ] `fail()` — GET `/payment/fail`

- [ ] `PaymentService`:
  - [ ] `initiatePayment(User $user, $amount)` — создать платёж
  - [ ] `confirmPayment(Payment $payment)` — подтвердить, обновить subscription_expires_at
  - [ ] `isSubscriptionActive(User $user)` — проверить

- [ ] Защита webhook: проверка подписи от эквайринга

#### Маршруты оплаты
```
GET  /payment
POST /payment
POST /payment/callback  (без auth middleware)
GET  /payment/success
GET  /payment/fail
```

---

### 📁 1.7 Фронтенд — Blade шаблоны (Этап 1)

- [ ] **Базовые компоненты:**
  - [ ] `components/navbar.blade.php` — навигация (роль-зависимая)
  - [ ] `components/footer.blade.php`
  - [ ] `components/alert.blade.php` — flash-уведомления (success/error/warning)
  - [ ] `components/modal.blade.php` — универсальное модальное окно
  - [ ] `components/avatar.blade.php` — аватар пользователя
  - [ ] `components/status-badge.blade.php` — статус (активный/ожидает/заблокирован)
  - [ ] `components/card-team.blade.php` — карточка команды
  - [ ] `components/stepper.blade.php` — шаги регистрации

- [ ] **Страницы регистрации:**
  - [ ] `auth/login.blade.php` — вход (телефон + Telegram)
  - [ ] `register/step-phone.blade.php`
  - [ ] `register/step-profile.blade.php`
  - [ ] `register/step-team.blade.php`
  - [ ] `register/step-verification.blade.php` — загрузка документа
  - [ ] `register/step-payment.blade.php`
  - [ ] `register/pending.blade.php` — ожидание

- [ ] **Личный кабинет:**
  - [ ] `dashboard/index.blade.php`
  - [ ] `profile/show.blade.php`
  - [ ] `profile/edit.blade.php`
  - [ ] `profile/payments.blade.php`

- [ ] **Команды:**
  - [ ] `teams/index.blade.php`
  - [ ] `teams/show.blade.php`
  - [ ] `teams/create.blade.php`
  - [ ] `my-team/show.blade.php` — мой вид команды
  - [ ] `my-team/manage.blade.php` — управление (для капитана)

---

### 📁 1.8 Базовая Admin-панель (Этап 1)

- [ ] `Admin/DashboardController`:
  - [ ] `index()` — GET `/admin` — дашборд со счётчиками

- [ ] `Admin/UserController`:
  - [ ] `index()` — список пользователей с фильтрами
  - [ ] `show($id)` — профиль пользователя
  - [ ] `verify($id)` — одобрить верификацию
  - [ ] `reject($id)` — отклонить верификацию
  - [ ] `block($id)` — заблокировать
  - [ ] `unblock($id)` — разблокировать
  - [ ] `destroy($id)` — удалить
  - [ ] `updateRole($id)` — изменить роль

- [ ] `Admin/WhitelistController`:
  - [ ] `index()` — список с возможностью добавить/удалить
  - [ ] `store()` — добавить номер
  - [ ] `destroy($id)` — удалить номер

- [ ] `Admin/PaymentController`:
  - [ ] `index()` — список всех платежей
  - [ ] `confirm($id)` — ручное подтверждение
  - [ ] `settings()` — настройка суммы взноса

- [ ] `Admin/TeamController`:
  - [ ] `index()` — список команд
  - [ ] `show($id)` — просмотр команды
  - [ ] `update($id)` — редактирование
  - [ ] `changeCaptain($id)` — смена капитана

- [ ] **Blade шаблоны admin:**
  - [ ] `admin/layout.blade.php` — сайдбар + хедер
  - [ ] `admin/dashboard.blade.php`
  - [ ] `admin/users/index.blade.php`
  - [ ] `admin/users/show.blade.php`
  - [ ] `admin/whitelist/index.blade.php`
  - [ ] `admin/payments/index.blade.php`
  - [ ] `admin/payments/settings.blade.php`
  - [ ] `admin/teams/index.blade.php`
  - [ ] `admin/teams/show.blade.php`

#### Маршруты admin (prefix: /admin, middleware: role:superadmin)
```
GET         /admin
GET/POST    /admin/users
GET         /admin/users/{user}
POST        /admin/users/{user}/verify
POST        /admin/users/{user}/reject
POST        /admin/users/{user}/block
POST        /admin/users/{user}/unblock
DELETE      /admin/users/{user}
POST        /admin/users/{user}/role
GET/POST    /admin/whitelist
DELETE      /admin/whitelist/{item}
GET         /admin/payments
POST        /admin/payments/{payment}/confirm
GET/POST    /admin/settings
GET         /admin/teams
GET/PUT     /admin/teams/{team}
POST        /admin/teams/{team}/captain
```

---

### 📁 1.9 Валидация и FormRequest классы

- [ ] `RegisterPhoneRequest`
- [ ] `RegisterProfileRequest`
- [ ] `RegisterTeamRequest`
- [ ] `RegisterVerificationRequest`
- [ ] `UpdateProfileRequest`
- [ ] `StoreTeamRequest`
- [ ] `UpdateTeamRequest`
- [ ] `AddWhitelistRequest`
- [ ] Все сообщения об ошибках — на русском

---

### 📁 1.10 Тестирование Этапа 1

- [ ] Зарегистрировать тестового пользователя через телефон
- [ ] Зарегистрировать тестового пользователя через Telegram
- [ ] Проверить блокировку для номеров не в белом списке
- [ ] Пройти все 5 шагов регистрации
- [ ] Верифицировать пользователя через админку
- [ ] Протестировать оплату (sandbox)
- [ ] Создать команду как капитан
- [ ] Подать заявку игрока в команду и одобрить
- [ ] Проверить что неактивный (не оплативший) игрок отмечен корректно
- [ ] Протестировать на мобильном устройстве (iOS Safari + Android Chrome)

---

## ⚡ ЭТАП 2 — Турниры и матчи

---

### 📁 2.1 Миграции Этапа 2

- [ ] Заполнить миграцию `tournaments`:
  - `id`, `name`, `description`, `start_date`, `end_date`, `venue`
  - `max_teams`, `status` (enum: draft, registration, group_stage, playoff, finished)
  - `created_by` (FK users)
  - `timestamps`

- [ ] Миграция `tournament_teams`:
  - `id`, `tournament_id`, `team_id`
  - `status` (enum: pending, approved, rejected)
  - `added_by` (FK users)
  - `timestamps`

- [ ] Миграция `tournament_squads`:
  - `id`, `tournament_id`, `team_id`, `user_id`
  - `timestamps`

- [ ] Миграция `groups`:
  - `id`, `tournament_id`, `name` (А, Б, В...)
  - `timestamps`

- [ ] Миграция `group_teams`:
  - `id`, `group_id`, `team_id`

- [ ] Заполнить миграцию `matches`:
  - `id`, `tournament_id`, `group_id` (nullable), `stage` (enum: group, playoff)
  - `playoff_round` (nullable: quarterfinal, semifinal, final, 3rd_place)
  - `home_team_id`, `away_team_id`
  - `home_score`, `away_score` (default 0)
  - `scheduled_at`, `venue` (nullable)
  - `status` (enum: scheduled, live, finished)
  - `timestamps`

- [ ] Миграция `match_events`:
  - `id`, `match_id`, `team_id`, `user_id`
  - `type` (enum: goal, assist, yellow_card, red_card)
  - `minute` (nullable)
  - `timestamps`

---

### 📁 2.2 Модели Этапа 2

- [ ] `Tournament` модель:
  - [ ] Связи: `teams()`, `groups()`, `matches()`, `createdBy()`
  - [ ] Скоупы: `active()`, `upcoming()`, `finished()`
  - [ ] Метод: `isRegistrationOpen()`
  - [ ] Метод: `getStandings($groupId)` — расчёт таблицы группы

- [ ] `TournamentTeam` модель
- [ ] `TournamentSquad` модель
- [ ] `Group` модель: связи `tournament()`, `teams()`, `matches()`
- [ ] `Match` модель:
  - [ ] Связи: `tournament()`, `group()`, `homeTeam()`, `awayTeam()`, `events()`
  - [ ] Метод: `getWinner()` — возвращает команду-победителя
  - [ ] Метод: `isDraw()` 
  - [ ] Observer: при изменении счёта — пересчитать таблицу группы

- [ ] `MatchEvent` модель:
  - [ ] Связи: `match()`, `team()`, `user()`
  - [ ] Observer: при создании гола — инкрементировать счёт матча

---

### 📁 2.3 Сервисы Этапа 2

- [ ] `StandingsService`:
  - [ ] `calculateGroupStandings($groupId)` — вернуть массив: команда, игры, победы, ничьи, поражения, голы за/против, разница, очки
  - [ ] Сортировка: очки → разница мячей → забитые голы

- [ ] `PlayoffService`:
  - [ ] `generatePlayoffFromGroups($tournamentId)` — создать матчи плей-офф автоматически
  - [ ] `generatePlayoffManually($tournamentId, $pairs)` — ручное формирование

- [ ] `MatchEventService`:
  - [ ] `addEvent($matchId, $type, $userId, $teamId)` — добавить событие + обновить счёт
  - [ ] `removeEvent($eventId)` — удалить событие + пересчитать счёт
  - [ ] `finishMatch($matchId)` — завершить матч, обновить таблицу

- [ ] `PlayerStatsService`:
  - [ ] `getPlayerStats($userId, $tournamentId = null)` — голы, ассисты, карточки
  - [ ] `getTopScorers($tournamentId = null, $limit = 10)`
  - [ ] `getTopAssists($tournamentId = null, $limit = 10)`
  - [ ] `getTopCards($tournamentId = null, $limit = 10)`

---

### 📁 2.4 Контроллеры Этапа 2

#### Публичная часть
- [ ] `TournamentController`:
  - [ ] `index()` — GET `/tournaments`
  - [ ] `show($id)` — GET `/tournaments/{tournament}`

- [ ] `MatchController`:
  - [ ] `show($id)` — GET `/matches/{match}`

- [ ] `PlayerController`:
  - [ ] `show($id)` — GET `/players/{user}`

- [ ] `RatingController`:
  - [ ] `index()` — GET `/ratings`

#### Капитан
- [ ] `Captain/TournamentController`:
  - [ ] `applyToTournament($tournamentId)` — POST заявка команды
  - [ ] `manageSquad($tournamentId)` — GET/POST состав команды

#### Admin — Турниры
- [ ] `Admin/TournamentController`:
  - [ ] `index()` — список турниров
  - [ ] `create()` / `store()` — создать
  - [ ] `edit($id)` / `update($id)` — редактировать
  - [ ] `destroy($id)` — удалить
  - [ ] `applications($id)` — заявки команд
  - [ ] `approveTeam($tournamentId, $teamId)` — одобрить
  - [ ] `rejectTeam($tournamentId, $teamId)` — отклонить
  - [ ] `addTeamDirect($tournamentId)` — добавить напрямую

- [ ] `Admin/GroupController`:
  - [ ] `index($tournamentId)` — управление группами
  - [ ] `store($tournamentId)` — создать группу
  - [ ] `addTeam($groupId)` — добавить команду в группу
  - [ ] `removeTeam($groupId, $teamId)` — убрать

- [ ] `Admin/ScheduleController`:
  - [ ] `index($tournamentId)` — расписание
  - [ ] `store($tournamentId)` — создать матч
  - [ ] `update($matchId)` — редактировать (дата/время/место)

- [ ] `Admin/MatchController`:
  - [ ] `live($matchId)` — GET экран ведения счёта
  - [ ] `addEvent($matchId)` — POST добавить событие (AJAX)
  - [ ] `removeEvent($matchId, $eventId)` — DELETE
  - [ ] `finish($matchId)` — POST завершить матч
  - [ ] `edit($matchId)` — GET редактирование результата
  - [ ] `updateResult($matchId)` — POST сохранить изменения

---

### 📁 2.5 Real-time обновление счёта

- [ ] Реализовать через polling (AJAX каждые 5 сек.) на MVP
- [ ] `GET /matches/{match}/state` — JSON: счёт + события (для полинга)
- [ ] Alpine.js на странице матча: обновлять счёт и ленту событий
- [ ] Опционально: Laravel Broadcasting + Pusher для настоящего real-time

---

### 📁 2.6 Blade шаблоны Этапа 2

- [ ] **Публичные:**
  - [ ] `tournaments/index.blade.php` — список турниров
  - [ ] `tournaments/show.blade.php` — страница турнира с вкладками
  - [ ] `tournaments/partials/standings.blade.php` — таблица группы
  - [ ] `tournaments/partials/bracket.blade.php` — сетка плей-офф
  - [ ] `tournaments/partials/schedule.blade.php` — расписание
  - [ ] `tournaments/partials/stats.blade.php` — бомбардиры турнира
  - [ ] `matches/show.blade.php` — карточка матча
  - [ ] `players/show.blade.php` — профиль игрока
  - [ ] `ratings/index.blade.php` — топ-рейтинги

- [ ] **Капитан:**
  - [ ] `captain/squad.blade.php` — выбор состава на турнир

- [ ] **Admin:**
  - [ ] `admin/tournaments/index.blade.php`
  - [ ] `admin/tournaments/form.blade.php` — создание/редактирование
  - [ ] `admin/tournaments/show.blade.php` — управление турниром (вкладки)
  - [ ] `admin/tournaments/applications.blade.php`
  - [ ] `admin/groups/index.blade.php`
  - [ ] `admin/schedule/index.blade.php`
  - [ ] `admin/matches/live.blade.php` — ⭐ ключевой экран (mobile-first!)
  - [ ] `admin/matches/edit.blade.php`

---

### 📁 2.7 Тестирование Этапа 2

- [ ] Создать тестовый турнир
- [ ] Добавить 4+ команды
- [ ] Создать 2 группы, распределить команды
- [ ] Создать расписание матчей
- [ ] Провести матч: добавить голы, ассисты, карточки
- [ ] Завершить матч — проверить обновление таблицы
- [ ] Провести все матчи группового этапа
- [ ] Сформировать плей-офф
- [ ] Провести плей-офф до финала
- [ ] Проверить статистику игроков и рейтинги
- [ ] Редактировать результат матча — проверить пересчёт таблицы
- [ ] Проверить ведение счёта с мобильного телефона

---

## ⚡ ЭТАП 3 — Финализация

---

### 📁 3.1 Telegram-уведомления

- [ ] Установить `composer require irazasyed/telegram-bot-sdk`
- [ ] Настроить `.env`: `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHANNEL_ID`
- [ ] `TelegramService`:
  - [ ] `sendToChannel($message)` — отправить в канал
  - [ ] `formatTournamentCreated($tournament)` — шаблон нового турнира
  - [ ] `formatSchedulePublished($tournament)` — шаблон расписания
  - [ ] `formatMatchResult($match)` — шаблон результата матча
  - [ ] `formatAnnouncement($text)` — произвольное объявление

- [ ] Вызовы из сервисов: при создании турнира, публикации расписания, завершении матча
- [ ] `Admin/NotificationController`:
  - [ ] `index()` — GET `/admin/notifications`
  - [ ] `send()` — POST ручная публикация

- [ ] Blade: `admin/notifications/index.blade.php` — выбор шаблона + отправка

---

### 📁 3.2 Дашборд Admin (расширение)

- [ ] Добавить на дашборд:
  - [ ] Счётчики: пользователи, верификации, подписки, команды, турниры
  - [ ] Последние 5 заявок на верификацию
  - [ ] Последние 5 заявок на вступление в команды
  - [ ] Ближайшие матчи
  - [ ] Последние платежи
- [ ] Простые charts (без библиотек или Chart.js) — регистрации по месяцам, оплаты

---

### 📁 3.3 Безопасность

- [ ] Проверить CSRF на всех POST/PUT/DELETE формах
- [ ] Проверить XSS: использовать `{{ }}` везде, `{!! !!}` только где нужно
- [ ] SQL-инъекции: убедиться что все запросы через Eloquent или параметризированные
- [ ] Rate limiting на:
  - [ ] `/login` — 5 попыток / минута
  - [ ] `/register` — 10 попыток / час
  - [ ] `/payment/callback` — whitelist IP эквайринга
- [ ] Документы (дипломы): хранить вне `public/`, доступ только через контроллер с проверкой роли
- [ ] `.env` не в репозитории (проверить `.gitignore`)
- [ ] Все admin-маршруты защищены middleware `role:superadmin`
- [ ] Логирование важных действий (верификация, оплата, блокировка)

---

### 📁 3.4 Бэкапы и логирование

- [ ] Установить `spatie/laravel-backup`
- [ ] Настроить `config/backup.php`: ежедневный бэкап БД
- [ ] Настроить `config/logging.php`: daily логи, уровень info+
- [ ] Логировать в канал Telegram критические ошибки (опционально)
- [ ] `php artisan schedule:run` в cron: `* * * * *`
- [ ] Задачи в расписании:
  - [ ] `backup:run` — ежедневно в 3:00
  - [ ] Проверка просроченных подписок — ежедневно

---

### 📁 3.5 Производительность и оптимизация

- [ ] Добавить индексы в миграциях на: `phone`, `user_id`, `team_id`, `tournament_id`, `status`
- [ ] Eager loading: устранить N+1 запросы (`with()` во всех контроллерах)
- [ ] Кэширование: таблицы групп, рейтинги (Cache::remember, 60 сек)
- [ ] Пагинация на всех списках (users, teams, payments)
- [ ] `php artisan optimize` перед деплоем
- [ ] Проверить время загрузки страниц (< 3 сек)

---

### 📁 3.6 UX доработки и полировка

- [ ] Проверить все статусные badges — везде одинаковые стили
- [ ] Пустые состояния (empty states) — когда нет команд, матчей, турниров
- [ ] Скелетоны загрузки на медленных запросах
- [ ] Хлебные крошки на всех внутренних страницах
- [ ] Подтверждение для всех деструктивных действий (модалка)
- [ ] Flash-сообщения после каждого действия
- [ ] Мобильная проверка всех экранов (320px, 375px, 414px)
- [ ] Проверить все формы на мобильном: размер кнопок, шрифтов

---

### 📁 3.7 Тестирование Этапа 3

- [ ] Полный сквозной тест: регистрация → верификация → оплата → турнир → матч → статистика
- [ ] Тест Telegram-уведомлений
- [ ] Тест бэкапа
- [ ] Нагрузочное тестирование (хотя бы вручную несколькими браузерами)
- [ ] Проверка в Chrome, Safari, Firefox, Edge
- [ ] Проверка iOS Safari и Android Chrome
- [ ] Проверка разрешения 360px
- [ ] Аудит безопасности (чеклист выше)

---

## 🚀 Деплой

- [ ] Выбрать хостинг (Hetzner CX21 или аналог)
- [ ] Настроить сервер: Nginx + PHP 8.3 + MySQL 8
- [ ] SSL-сертификат: Let's Encrypt + auto-renewal
- [ ] Загрузить код (git pull или rsync)
- [ ] Настроить `.env` на сервере
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] `php artisan optimize`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Настроить cron для Laravel Scheduler
- [ ] Настроить очереди: `php artisan queue:work` (supervisor)
- [ ] Проверить все маршруты на продакшне
- [ ] Настроить домен

---

## 📝 CLAUDE.md — файл для проекта

> Создать `CLAUDE.md` в корне проекта с описанием для Claude Code:

```markdown
# Football Doctors Tournament Platform

## Stack
- Laravel 11, PHP 8.3, MySQL 8
- Blade + Alpine.js + Tailwind CSS
- Queue: database | Storage: local

## Commands
- `php artisan serve` — dev server
- `npm run dev` — Vite
- `php artisan migrate:fresh --seed` — reset DB
- `php artisan test` — run tests

## Key Architecture
- Roles: superadmin, tournament_admin, captain, player
- Registration is 5-step flow, stored in session
- Whitelist check happens BEFORE any registration
- Subscription check via middleware CheckSubscription
- Match score is calculated from match_events (not stored directly)
- Standings are calculated on-the-fly via StandingsService

## Important Rules
- All validation messages must be in Russian
- Mobile-first: test every UI change at 375px
- Admin panel at /admin, protected by role:superadmin middleware
- Never expose documents folder to public directly
- Always use eager loading (with()) to avoid N+1
```

---

## 📊 Итоговый счётчик задач

| Этап | Разделов | Задач |
|---|---|---|
| Этап 1 — MVP | 10 | ~120 |
| Этап 2 — Турниры | 7 | ~90 |
| Этап 3 — Финализация | 7 | ~60 |
| Деплой | 1 | ~20 |
| **Итого** | **25** | **~290** |
