<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('subscription_status')) {
            $query->where('subscription_status', $request->subscription_status);
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['payments', 'teamMemberships.team']);
        return view('admin.users.show', compact('user'));
    }

    public function verify(User $user)
    {
        $user->update(['verification_status' => 'approved']);
        return back()->with('success', "Пользователь {$user->name} верифицирован.");
    }

    public function reject(User $user)
    {
        $user->update(['verification_status' => 'rejected']);
        return back()->with('success', "Верификация {$user->name} отклонена.");
    }

    public function block(User $user)
    {
        $user->update(['is_blocked' => true]);
        return back()->with('success', "Пользователь {$user->name} заблокирован.");
    }

    public function unblock(User $user)
    {
        $user->update(['is_blocked' => false]);
        return back()->with('success', "Пользователь {$user->name} разблокирован.");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Пользователь удалён.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:superadmin,tournament_admin,captain,player',
        ], [
            'role.required' => 'Выберите роль.',
            'role.in' => 'Выбрана недопустимая роль.',
        ]);

        $user->update(['role' => $request->role]);
        return back()->with('success', "Роль {$user->name} изменена на {$request->role}.");
    }
}
