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
            $query->where('iin', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest('created_at')->paginate(20);

        // Check registration status for each iin
        $iins = $items->pluck('iin');
        $registeredIins = User::whereIn('iin', $iins)->where('del', false)->pluck('iin')->toArray();

        return view('admin.whitelist.index', compact('items', 'registeredIins'));
    }

    public function store(Request $request)
    {
        $rules = [
            'iin' => 'required|string|digits:12|unique:whitelist,iin',
        ];
        $messages = [
            'iin.required' => 'Введите ИИН.',
            'iin.digits' => 'ИИН должен содержать 12 цифр.',
            'iin.unique' => 'Этот ИИН уже в белом списке.',
        ];

        // Только суперадмин может выбирать роль
        if ($request->user()->isSuperAdmin()) {
            $rules['role'] = 'sometimes|in:player,captain';
        }

        $request->validate($rules, $messages);

        $iin = $request->iin;
        $role = $request->user()->isSuperAdmin() ? ($request->input('role', 'player')) : 'player';

        Whitelist::create([
            'iin' => $iin,
            'role' => $role,
            'added_by' => $request->user()->id,
        ]);

        return back()->with('success', "ИИН {$iin} добавлен в белый список.");
    }

    public function destroy(Request $request, Whitelist $item)
    {
        $user = $request->user();

        // Капитан может удалять только свои номера
        if (!$user->isSuperAdmin() && $item->added_by !== $user->id) {
            abort(403, 'Вы можете удалять только свои записи.');
        }

        $item->delete();
        return back()->with('success', 'ИИН удалён из белого списка.');
    }
}
