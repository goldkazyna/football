<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Http\Request;

class WhitelistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Whitelist::with('addedByUser');

        // Капитан видит только свои добавленные номера
        if (!$user->isSuperAdmin()) {
            $query->where('added_by', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('phone', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest('created_at')->paginate(20);

        // Check registration status for each phone
        $phones = $items->pluck('phone');
        $registeredPhones = User::whereIn('phone', $phones)->pluck('phone')->toArray();

        return view('admin.whitelist.index', compact('items', 'registeredPhones'));
    }

    public function store(Request $request)
    {
        $rules = [
            'phone' => 'required|string|unique:whitelist,phone',
        ];
        $messages = [
            'phone.required' => 'Введите номер телефона.',
            'phone.unique' => 'Этот номер уже в белом списке.',
        ];

        // Только суперадмин может выбирать роль
        if ($request->user()->isSuperAdmin()) {
            $rules['role'] = 'sometimes|in:player,captain';
        }

        $request->validate($rules, $messages);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);
        $role = $request->user()->isSuperAdmin() ? ($request->input('role', 'player')) : 'player';

        Whitelist::create([
            'phone' => $phone,
            'role' => $role,
            'added_by' => $request->user()->id,
        ]);

        return back()->with('success', "Номер {$phone} добавлен в белый список.");
    }

    public function destroy(Request $request, Whitelist $item)
    {
        $user = $request->user();

        // Капитан может удалять только свои номера
        if (!$user->isSuperAdmin() && $item->added_by !== $user->id) {
            abort(403, 'Вы можете удалять только свои номера.');
        }

        $item->delete();
        return back()->with('success', 'Номер удалён из белого списка.');
    }
}
