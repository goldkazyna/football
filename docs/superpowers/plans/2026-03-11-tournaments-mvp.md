# Tournaments MVP Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Superadmin creates tournaments, captains apply with player selection (paid members only), superadmin approves/rejects applications.

**Architecture:** Two new migrations (tournament_applications, tournament_application_players), 2 models (Tournament, TournamentApplication), 2 controllers (Admin\TournamentController, TournamentController), 7 blade views. Follows existing patterns from admin teams CRUD.

**Tech Stack:** Laravel 11, Blade, Alpine.js, custom CSS (style.css variables)

---

## Chunk 1: Database & Models

### Task 1: Create Tournament model

**Files:**
- Create: `app/Models/Tournament.php`

- [ ] **Step 1: Create Tournament model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'start_date', 'end_date',
        'venue', 'max_teams', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TournamentApplication::class);
    }

    public function approvedApplications(): HasMany
    {
        return $this->applications()->where('status', 'approved');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '!=', 'draft');
    }

    public function scopeOpenForRegistration($query)
    {
        return $query->where('status', 'registration');
    }

    public function isFull(): bool
    {
        if (!$this->max_teams) {
            return false;
        }
        return $this->approvedApplications()->count() >= $this->max_teams;
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === 'registration';
    }
}
```

- [ ] **Step 2: Verify migration exists**

Run: `php artisan migrate:status`
The `create_tournaments_table` migration should already exist.

- [ ] **Step 3: Commit**

```bash
git add app/Models/Tournament.php
git commit -m "feat: add Tournament model with relationships and scopes"
```

### Task 2: Create tournament_applications migration and model

**Files:**
- Create: `database/migrations/2024_01_01_000070_create_tournament_applications_table.php`
- Create: `database/migrations/2024_01_01_000071_create_tournament_application_players_table.php`
- Create: `app/Models/TournamentApplication.php`

- [ ] **Step 1: Create tournament_applications migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applied_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejected_reason')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_applications');
    }
};
```

- [ ] **Step 2: Create tournament_application_players migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_application_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('tournament_applications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['application_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_application_players');
    }
};
```

- [ ] **Step 3: Create TournamentApplication model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TournamentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id', 'team_id', 'applied_by', 'status', 'rejected_reason',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_application_players', 'application_id', 'user_id')
                     ->withTimestamps();
    }
}
```

- [ ] **Step 4: Run migrations**

Run: `php artisan migrate`
Expected: Both new tables created successfully.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2024_01_01_000070_create_tournament_applications_table.php database/migrations/2024_01_01_000071_create_tournament_application_players_table.php app/Models/TournamentApplication.php
git commit -m "feat: add tournament applications tables and model"
```

---

## Chunk 2: Routes & Admin Controller

### Task 3: Add tournament routes

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Add admin tournament routes**

In `routes/web.php`, inside the admin group (after teams routes), add:

```php
use App\Http\Controllers\Admin\TournamentController as AdminTournamentController;
use App\Http\Controllers\TournamentController;
```

Admin routes (inside `Route::prefix('admin')...` group):

```php
// Tournaments
Route::get('/tournaments', [AdminTournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/create', [AdminTournamentController::class, 'create'])->name('tournaments.create');
Route::post('/tournaments', [AdminTournamentController::class, 'store'])->name('tournaments.store');
Route::get('/tournaments/{tournament}', [AdminTournamentController::class, 'show'])->name('tournaments.show');
Route::get('/tournaments/{tournament}/edit', [AdminTournamentController::class, 'edit'])->name('tournaments.edit');
Route::put('/tournaments/{tournament}', [AdminTournamentController::class, 'update'])->name('tournaments.update');
Route::post('/tournaments/{tournament}/status', [AdminTournamentController::class, 'updateStatus'])->name('tournaments.status');
Route::post('/tournaments/{tournament}/applications/{application}/approve', [AdminTournamentController::class, 'approveApplication'])->name('tournaments.applications.approve');
Route::post('/tournaments/{tournament}/applications/{application}/reject', [AdminTournamentController::class, 'rejectApplication'])->name('tournaments.applications.reject');
```

- [ ] **Step 2: Replace user tournament placeholder with real routes**

Replace the existing placeholder route with:

```php
// Tournaments (user-facing)
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/tournaments/{tournament}/apply', [TournamentController::class, 'apply'])->name('tournaments.apply');
Route::post('/tournaments/{tournament}/apply', [TournamentController::class, 'submitApplication'])->name('tournaments.submitApplication');
```

These go inside the `verified.doctor` middleware group.

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat: add tournament routes for admin and user"
```

### Task 4: Create Admin TournamentController

**Files:**
- Create: `app/Http/Controllers/Admin/TournamentController.php`

- [ ] **Step 1: Create the controller**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::withCount(['applications', 'approvedApplications'])
            ->latest()
            ->paginate(20);

        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'max_teams' => 'nullable|integer|min:2|max:64',
        ], [
            'name.required' => 'Введите название турнира.',
            'start_date.required' => 'Укажите дату начала.',
            'end_date.after_or_equal' => 'Дата окончания должна быть не раньше даты начала.',
            'max_teams.min' => 'Минимум 2 команды.',
            'max_teams.max' => 'Максимум 64 команды.',
        ]);

        Tournament::create([
            ...$request->only('name', 'description', 'start_date', 'end_date', 'venue', 'max_teams'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.tournaments.index')->with('success', 'Турнир создан.');
    }

    public function show(Tournament $tournament)
    {
        $tournament->load(['applications' => function ($q) {
            $q->with(['team', 'appliedBy', 'players'])->latest();
        }]);

        return view('admin.tournaments.show', compact('tournament'));
    }

    public function edit(Tournament $tournament)
    {
        return view('admin.tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'max_teams' => 'nullable|integer|min:2|max:64',
        ], [
            'name.required' => 'Введите название турнира.',
            'start_date.required' => 'Укажите дату начала.',
            'end_date.after_or_equal' => 'Дата окончания должна быть не раньше даты начала.',
            'max_teams.min' => 'Минимум 2 команды.',
            'max_teams.max' => 'Максимум 64 команды.',
        ]);

        $tournament->update($request->only('name', 'description', 'start_date', 'end_date', 'venue', 'max_teams'));

        return back()->with('success', 'Турнир обновлён.');
    }

    public function updateStatus(Request $request, Tournament $tournament)
    {
        $request->validate([
            'status' => 'required|in:draft,registration,closed',
        ], [
            'status.required' => 'Выберите статус.',
            'status.in' => 'Недопустимый статус.',
        ]);

        $tournament->update(['status' => $request->status]);

        $statusLabels = ['draft' => 'Черновик', 'registration' => 'Регистрация открыта', 'closed' => 'Регистрация закрыта'];

        return back()->with('success', "Статус изменён: {$statusLabels[$request->status]}.");
    }

    public function approveApplication(Tournament $tournament, TournamentApplication $application)
    {
        abort_unless($application->tournament_id === $tournament->id, 404);

        if ($tournament->isFull()) {
            return back()->with('error', 'Достигнуто максимальное количество команд.');
        }

        $application->update(['status' => 'approved']);

        return back()->with('success', "Заявка команды «{$application->team->name}» одобрена.");
    }

    public function rejectApplication(Request $request, Tournament $tournament, TournamentApplication $application)
    {
        abort_unless($application->tournament_id === $tournament->id, 404);

        $application->update([
            'status' => 'rejected',
            'rejected_reason' => $request->input('reason'),
        ]);

        return back()->with('success', "Заявка команды «{$application->team->name}» отклонена.");
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/Admin/TournamentController.php
git commit -m "feat: add Admin TournamentController with CRUD and application management"
```

### Task 5: Create user TournamentController

**Files:**
- Create: `app/Http/Controllers/TournamentController.php` (replace placeholder if exists, but currently it's just a closure in routes)

Wait — there's already `app/Http/Controllers/TeamController.php`. The new file is `TournamentController.php`.

- [ ] **Step 1: Create the controller**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentApplication;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = $user->currentTeam();

        $tournaments = Tournament::visible()
            ->withCount('approvedApplications')
            ->latest()
            ->paginate(20);

        // Get user's team applications for status display
        $teamApplications = [];
        if ($team) {
            $teamApplications = TournamentApplication::where('team_id', $team->id)
                ->pluck('status', 'tournament_id')
                ->toArray();
        }

        return view('tournaments.index', compact('tournaments', 'teamApplications', 'team'));
    }

    public function show(Tournament $tournament)
    {
        abort_if($tournament->status === 'draft', 404);

        $tournament->load(['approvedApplications.team']);

        $user = auth()->user();
        $team = $user->currentTeam();
        $application = null;

        if ($team) {
            $application = TournamentApplication::where('tournament_id', $tournament->id)
                ->where('team_id', $team->id)
                ->first();
        }

        return view('tournaments.show', compact('tournament', 'team', 'application'));
    }

    public function apply(Tournament $tournament)
    {
        $user = auth()->user();

        abort_unless($user->isCaptain() || $user->isSuperAdmin(), 403, 'Только капитан может подать заявку.');
        abort_unless($tournament->isOpenForRegistration(), 403, 'Регистрация закрыта.');

        $team = $user->currentTeam();
        abort_unless($team, 403, 'У вас нет команды.');

        // Check if already applied
        $existing = TournamentApplication::where('tournament_id', $tournament->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existing) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Ваша команда уже подала заявку на этот турнир.');
        }

        // Get active team members (with subscription)
        $team->load(['members' => function ($q) {
            $q->where('status', 'approved')
              ->whereNull('left_at')
              ->with('user');
        }]);

        $members = $team->members->map(fn($m) => $m->user)->filter();

        return view('tournaments.apply', compact('tournament', 'team', 'members'));
    }

    public function submitApplication(Request $request, Tournament $tournament)
    {
        $user = auth()->user();

        abort_unless($user->isCaptain() || $user->isSuperAdmin(), 403);
        abort_unless($tournament->isOpenForRegistration(), 403, 'Регистрация закрыта.');

        $team = $user->currentTeam();
        abort_unless($team, 403);

        // Check duplicate
        $existing = TournamentApplication::where('tournament_id', $tournament->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Заявка уже подана.');
        }

        // Check tournament not full
        if ($tournament->isFull()) {
            return back()->with('error', 'Турнир уже заполнен.');
        }

        $request->validate([
            'players' => 'required|array|min:1',
            'players.*' => 'exists:users,id',
        ], [
            'players.required' => 'Выберите хотя бы одного игрока.',
            'players.min' => 'Выберите хотя бы одного игрока.',
        ]);

        // Verify all selected players have active subscription
        $playerIds = $request->input('players');
        $activePlayers = \App\Models\User::whereIn('id', $playerIds)
            ->where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->pluck('id')
            ->toArray();

        if (count($activePlayers) !== count($playerIds)) {
            return back()->with('error', 'Некоторые выбранные игроки не имеют активной подписки.');
        }

        // Create application
        $application = TournamentApplication::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'applied_by' => $user->id,
        ]);

        $application->players()->attach($playerIds);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Заявка подана! Ожидайте одобрения.');
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/TournamentController.php
git commit -m "feat: add user TournamentController with apply flow"
```

---

## Chunk 3: Admin Blade Views

### Task 6: Admin tournament list page

**Files:**
- Create: `resources/views/admin/tournaments/index.blade.php`

Reference design: `C:\projects\design\football\pages\admin\tournament\index.html`

- [ ] **Step 1: Create the view**

Follow the pattern from `admin/teams/index.blade.php`. Use `@extends('layouts.admin')`. Tournament cards with name, status badge, teams count, venue. "+ Создать турнир" button at top.

Badge mapping:
- `draft` → `badge-finished` text "Черновик"
- `registration` → `badge-active` text "Регистрация"
- `closed` → `badge-upcoming` text "Закрыта"

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/tournaments/index.blade.php
git commit -m "feat: add admin tournaments list view"
```

### Task 7: Admin tournament create page

**Files:**
- Create: `resources/views/admin/tournaments/create.blade.php`

Reference design: `C:\projects\design\football\pages\admin\tournament\create.html`

- [ ] **Step 1: Create the view**

Follow pattern from `admin/teams/create.blade.php`. Form fields:
- name (text, required)
- description (textarea)
- start_date, end_date (date inputs in a row)
- venue (text)
- max_teams (number, min=2, max=64)

All with `@error` blocks and `old()` values.

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/tournaments/create.blade.php
git commit -m "feat: add admin tournament create view"
```

### Task 8: Admin tournament edit page

**Files:**
- Create: `resources/views/admin/tournaments/edit.blade.php`

- [ ] **Step 1: Create the view**

Same form as create but pre-filled with `$tournament` values. Uses PUT method. Also includes status change section with buttons/dropdown.

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/tournaments/edit.blade.php
git commit -m "feat: add admin tournament edit view"
```

### Task 9: Admin tournament show page (with applications)

**Files:**
- Create: `resources/views/admin/tournaments/show.blade.php`

Reference design: `C:\projects\design\football\pages\admin\tournament\manage.html` — only the "Заявки" tab

- [ ] **Step 1: Create the view**

Shows tournament header (name + status badge + edit link). Then list of applications:
- Approved: team name + green "Одобрена" badge
- Pending: team name + yellow "На рассмотрении" badge + Approve/Reject buttons
- Rejected: team name + red "Отклонена" badge

Status change section: 3 buttons (Черновик, Открыть регистрацию, Закрыть регистрацию) — POST to updateStatus.

Expanding each application card shows: player list, captain who applied, application date.

- [ ] **Step 2: Commit**

```bash
git add resources/views/admin/tournaments/show.blade.php
git commit -m "feat: add admin tournament show view with applications"
```

---

## Chunk 4: User Blade Views

### Task 10: User tournaments list page

**Files:**
- Modify: `resources/views/tournaments/index.blade.php` (replace placeholder)

Reference design: `C:\projects\design\football\pages\tournaments\index.html`

- [ ] **Step 1: Replace placeholder with full view**

`@extends('layouts.app')`. Tournament cards showing:
- Name + status badge
- Meta: dates, teams count, venue
- Bottom: application status if captain's team applied (Одобрена / На рассмотрении / Отклонена)
- Or "Подать заявку" button if captain and no application yet

- [ ] **Step 2: Commit**

```bash
git add resources/views/tournaments/index.blade.php
git commit -m "feat: add user tournaments list view"
```

### Task 11: User tournament show page

**Files:**
- Create: `resources/views/tournaments/show.blade.php`

Reference design: `C:\projects\design\football\pages\tournaments\tournament.html` — only hero + teams tab (no groups/bracket/schedule/stats)

- [ ] **Step 1: Create the view**

Hero section: badge, name, description, meta (dates, venue, team count).
"Подать заявку" button if captain + registration open + no existing application.
Teams list: approved teams with name + city.
Application status banner if team has applied.

- [ ] **Step 2: Commit**

```bash
git add resources/views/tournaments/show.blade.php
git commit -m "feat: add user tournament show view"
```

### Task 12: Tournament application form (player selection)

**Files:**
- Create: `resources/views/tournaments/apply.blade.php`

- [ ] **Step 1: Create the view**

`@extends('layouts.app')`. Header with back link to tournament.

Form with checkboxes for each team member:
- Player name, city
- If `subscription_status === 'active'` and not expired → checkbox enabled
- If NOT active subscription → checkbox disabled + grey text "Взнос не оплачен"
- Captain is always included (auto-checked, disabled)

Submit button "Подать заявку".

Uses Alpine.js for select all / counter of selected players.

- [ ] **Step 2: Commit**

```bash
git add resources/views/tournaments/apply.blade.php
git commit -m "feat: add tournament application form with player selection"
```

---

## Chunk 5: Navigation & Integration

### Task 13: Add tournaments to admin navigation

**Files:**
- Modify: `resources/views/layouts/admin.blade.php`

- [ ] **Step 1: Add tournament link to desktop nav and bottom nav**

Add after "Команды" link in both desktop-nav and bottom-nav:

```blade
<a href="{{ route('admin.tournaments.index') }}" class="{{ request()->routeIs('admin.tournaments.*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 3 18 9"/><path d="M12 3v12"/><path d="M4 21h16"/><path d="M4 17h4v4"/><path d="M16 17h4v4"/></svg>
    Турниры
</a>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/layouts/admin.blade.php
git commit -m "feat: add tournaments link to admin navigation"
```

### Task 14: Run full migration and manual test

- [ ] **Step 1: Run migrations**

Run: `php artisan migrate`
Expected: Both tournament_applications and tournament_application_players tables created.

- [ ] **Step 2: Verify routes**

Run: `php artisan route:list --path=tournament`
Expected: All admin and user tournament routes listed.

- [ ] **Step 3: Manual smoke test**

1. Login as superadmin
2. Go to /admin/tournaments — see empty list
3. Create tournament — fill form, submit
4. See tournament in list
5. Edit tournament, change status to "registration"
6. Login as captain with active subscription
7. Go to /tournaments — see tournament
8. Click tournament → see details
9. Click "Подать заявку" → select players → submit
10. Login as superadmin → see application → approve

- [ ] **Step 4: Final commit**

```bash
git add -A
git commit -m "feat: complete tournaments MVP — create, apply, approve/reject"
```
