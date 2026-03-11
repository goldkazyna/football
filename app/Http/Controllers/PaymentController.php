<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        return view('payment.create', compact('user'));
    }

    public function initiate(Request $request)
    {
        $user = $request->user();

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => config('app.membership_fee', 50000),
            'currency' => 'KZT',
            'status' => 'pending',
            'payment_method' => 'card',
        ]);

        // TODO: integrate with payment gateway (Kaspi / CloudPayments / Freedom Pay)
        // For now, redirect to success with pending status
        return redirect()->route('payment.success', ['payment' => $payment->id]);
    }

    public function callback(Request $request)
    {
        // TODO: handle payment gateway webhook
        return response()->json(['status' => 'ok']);
    }

    public function success(Request $request)
    {
        return view('payment.success');
    }

    public function fail()
    {
        return view('payment.fail');
    }
}
