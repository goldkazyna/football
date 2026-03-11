# Этап 2 — Турниры (MVP)

## Scope

Создание турниров superadmin-ом, подача заявок капитанами с выбором оплативших игроков. Без матчей, результатов, групп и таблиц.

## Статусы турнира

- `draft` — черновик, не виден пользователям
- `registration` — открыта регистрация
- `closed` — регистрация закрыта

Остальные статусы из миграции (group_stage, playoff, finished) пока не используются.

## Новые миграции

### tournament_applications
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| tournament_id | FK tournaments | |
| team_id | FK teams | |
| applied_by | FK users | капитан |
| status | enum: pending, approved, rejected | default: pending |
| rejected_reason | text nullable | |
| timestamps | | |

Unique constraint: (tournament_id, team_id)

### tournament_application_players
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| application_id | FK tournament_applications | cascade delete |
| user_id | FK users | |
| timestamps | | |

## Модели

### Tournament
- Связи: creator (belongsTo User), applications (hasMany)
- Scopes: visible() — status != draft, openForRegistration() — status = registration
- Метод: isFull() — approved applications >= max_teams

### TournamentApplication
- Связи: tournament, team, players (belongsToMany User через pivot), appliedBy (belongsTo User)

## Контроллеры

### Admin\TournamentController
- index — список всех турниров
- create/store — создание
- show — просмотр турнира + заявки
- edit/update — редактирование
- updateStatus — смена статуса (draft/registration/closed)
- approveApplication / rejectApplication — управление заявками

### TournamentController
- index — список турниров (status != draft)
- show — страница турнира (инфо + список одобренных команд)
- apply (GET) — форма заявки (список игроков с галочками)
- submitApplication (POST) — подача заявки

## Проверки при подаче заявки

1. Турнир в статусе `registration`
2. Капитан verified + subscription active
3. Команда ещё не подавала заявку на этот турнир
4. Не превышен max_teams (считаем approved заявки)
5. Выбранные игроки — только с subscription_status = active
6. Минимум 1 игрок выбран

## Роуты

```
# Admin
admin/tournaments — index
admin/tournaments/create — create
admin/tournaments/{tournament} — show
admin/tournaments/{tournament}/edit — edit
admin/tournaments/{tournament} PUT — update
admin/tournaments/{tournament}/status — updateStatus
admin/tournaments/{tournament}/applications/{application}/approve — approveApplication
admin/tournaments/{tournament}/applications/{application}/reject — rejectApplication

# User
tournaments — index
tournaments/{tournament} — show
tournaments/{tournament}/apply — apply (GET)
tournaments/{tournament}/apply — submitApplication (POST)
```

## Views

### Admin
- admin/tournaments/index.blade.php — список турниров
- admin/tournaments/create.blade.php — форма создания
- admin/tournaments/edit.blade.php — форма редактирования
- admin/tournaments/show.blade.php — просмотр + заявки

### User
- tournaments/index.blade.php — список турниров (карточки)
- tournaments/show.blade.php — страница турнира
- tournaments/apply.blade.php — форма заявки с галочками игроков
