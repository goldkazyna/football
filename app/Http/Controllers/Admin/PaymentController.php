<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('iin', 'like', '%' . $request->search . '%');
            });
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function confirm(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => 'confirmed',
            'confirmed_by' => $request->user()->id,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
        ]);

        $payment->user->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addMonth(),
        ]);

        return back()->with('success', "Оплата для {$payment->user->name} подтверждена.");
    }

    public function settings()
    {
        $settings = [
            'membership_fee' => config('app.membership_fee', 15000),
        ];

        return view('admin.payments.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'membership_fee' => 'required|numeric|min:0',
        ], [
            'membership_fee.required' => 'Укажите сумму взноса.',
            'membership_fee.numeric' => 'Сумма должна быть числом.',
            'membership_fee.min' => 'Сумма не может быть отрицательной.',
        ]);

        // For MVP, store in a simple JSON file
        $settings = ['membership_fee' => $request->membership_fee];
        file_put_contents(storage_path('app/settings.json'), json_encode($settings));

        return back()->with('success', 'Настройки сохранены.');
    }
}
