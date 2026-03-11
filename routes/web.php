<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Captain\TeamManageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\Admin\TournamentController as AdminTournamentController;
use Illuminate\Support\Facades\Route;

// --- Public ---
Route::get('/', function () {
    return redirect()->route('login');
});

// --- Auth (guest) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login/phone', [LoginController::class, 'loginByPhone'])->name('login.phone');

    Route::get('/register/{step}', [RegisterController::class, 'showStep'])->name('register.step');
    Route::post('/register/{step}', [RegisterController::class, 'processStep'])->name('register.process');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/register/pending', [RegisterController::class, 'pending'])->name('register.pending');

// --- Authenticated ---
Route::middleware('auth')->group(function () {

    // Dashboard + profile — доступны всегда (даже без верификации)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/payments', [ProfileController::class, 'payments'])->name('profile.payments');
    Route::post('/profile/reupload', [ProfileController::class, 'reuploadDocument'])->name('profile.reupload');

    // Всё остальное — только после верификации
    Route::middleware('verified.doctor')->group(function () {

        // My Team
        Route::get('/my-team', [TeamMemberController::class, 'myTeam'])->name('my-team');
        Route::post('/my-team/leave', [TeamMemberController::class, 'leaveTeam'])->name('my-team.leave');
        Route::post('/teams/{team}/apply', [TeamMemberController::class, 'applyToTeam'])->name('teams.apply');

        // Teams
        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
        Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');

        // Captain — team management + whitelist
        Route::middleware('role:captain,tournament_admin,superadmin')->group(function () {
            Route::get('/my-team/applications', [TeamManageController::class, 'applications'])->name('my-team.applications');
            Route::post('/my-team/applications/{member}/approve', [TeamManageController::class, 'approveApplication'])->name('my-team.applications.approve');
            Route::post('/my-team/applications/{member}/reject', [TeamManageController::class, 'rejectApplication'])->name('my-team.applications.reject');
            Route::delete('/my-team/members/{member}', [TeamManageController::class, 'removeMember'])->name('my-team.members.remove');
            Route::post('/my-team/verify/{user}', [TeamManageController::class, 'verifyMember'])->name('my-team.verify');
            Route::post('/my-team/reject-verification/{user}', [TeamManageController::class, 'rejectMember'])->name('my-team.reject-verification');

            // Whitelist
            Route::get('/whitelist', [Admin\WhitelistController::class, 'index'])->name('whitelist.index');
            Route::post('/whitelist', [Admin\WhitelistController::class, 'store'])->name('whitelist.store');
            Route::delete('/whitelist/{item}', [Admin\WhitelistController::class, 'destroy'])->name('whitelist.destroy');
        });

        // Payment
        Route::get('/payment', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('/payment', [PaymentController::class, 'initiate'])->name('payment.initiate');
        Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');

        // Tournaments
        Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
        Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');
        Route::get('/tournaments/{tournament}/apply', [TournamentController::class, 'apply'])->name('tournaments.apply');
        Route::post('/tournaments/{tournament}/apply', [TournamentController::class, 'submitApplication'])->name('tournaments.submitApplication');

    }); // end verified.doctor
});

// Payment callback (no auth)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// --- Admin ---
Route::prefix('admin')->middleware(['auth', 'role:superadmin'])->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/verify', [Admin\UserController::class, 'verify'])->name('users.verify');
    Route::post('/users/{user}/reject', [Admin\UserController::class, 'reject'])->name('users.reject');
    Route::post('/users/{user}/block', [Admin\UserController::class, 'block'])->name('users.block');
    Route::post('/users/{user}/unblock', [Admin\UserController::class, 'unblock'])->name('users.unblock');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.role');

    // Payments
    Route::get('/payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/confirm', [Admin\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::get('/settings', [Admin\PaymentController::class, 'settings'])->name('settings');
    Route::put('/settings', [Admin\PaymentController::class, 'updateSettings'])->name('settings.update');

    // Teams
    Route::get('/teams', [Admin\TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/create', [Admin\TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [Admin\TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}', [Admin\TeamController::class, 'show'])->name('teams.show');
    Route::put('/teams/{team}', [Admin\TeamController::class, 'update'])->name('teams.update');
    Route::post('/teams/{team}/captain', [Admin\TeamController::class, 'changeCaptain'])->name('teams.captain');

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
});
