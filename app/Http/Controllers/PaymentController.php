<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PlexyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        $plans = config('services.plexy.plans');

        return view('payment.create', compact('user', 'plans'));
    }

    public function initiate(Request $request, PlexyService $plexy)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly',
        ], [
            'plan.required' => 'Выберите тариф.',
            'plan.in' => 'Недопустимый тариф.',
        ]);

        $user = $request->user();
        $plan = $request->plan;
        $planConfig = config("services.plexy.plans.{$plan}");
        $amountTiyn = $planConfig['amount'];
        $amountTenge = $amountTiyn / 100;
        $months = $planConfig['months'];
        $orderReference = 'FD-' . $user->id . '-' . Str::random(12);

        $planLabel = $plan === 'yearly' ? '12 месяцев' : '1 месяц';

        // Создаём запись платежа
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amountTenge,
            'currency' => 'KZT',
            'status' => 'pending',
            'plan' => $plan,
            'payment_method' => 'card',
            'order_reference' => $orderReference,
        ]);

        // Создаём payment link в Plexy
        $result = $plexy->createPaymentLink([
            'amount' => $amountTiyn,
            'currency' => 'KZT',
            'description' => "Подписка Football Doctors ({$planLabel}) — " . $user->name,
            'orderReference' => $orderReference,
            'expiresAt' => now()->addHours(24)->toIso8601ZuluString(),
            'validation' => false,
            'autoClearing' => true,
            'metadata' => [
                'callbackUrl' => route('payment.callback'),
                'successUrl' => route('payment.success', ['order' => $orderReference]),
                'failureUrl' => route('payment.fail', ['order' => $orderReference]),
                'cancelUrl' => route('payment.fail', ['order' => $orderReference]),
            ],
        ]);

        if (!$result || empty($result['url'])) {
            $payment->update(['status' => 'failed']);

            return redirect()->route('payment.create')
                ->with('error', 'Не удалось создать платёж. Попробуйте позже.');
        }

        // Сохраняем данные от Plexy
        $payment->update([
            'plexy_link_id' => $result['id'],
            'plexy_checkout_url' => $result['url'],
        ]);

        // Редиректим на checkout Plexy
        return redirect()->away($result['url']);
    }

    public function callback(Request $request, PlexyService $plexy)
    {
        $payload = $request->all();
        $eventName = $payload['name'] ?? null;
        $data = $payload['data'] ?? [];
        $orderReference = $data['merchantReference'] ?? ($data['orderReference'] ?? null);

        Log::info('Plexy webhook received', [
            'event' => $eventName,
            'orderReference' => $orderReference,
            'payload' => $payload,
        ]);

        if (!$orderReference) {
            return response()->json(['status' => 'ok']);
        }

        $payment = Payment::where('order_reference', $orderReference)->first();
        if (!$payment) {
            Log::warning('Plexy webhook: payment not found', [
                'orderReference' => $orderReference,
            ]);
            return response()->json(['status' => 'ok']);
        }

        // Обрабатываем события
        match ($eventName) {
            'transaction.charged', 'transaction.authorized' => $this->handlePaymentSuccess($payment, $data),
            'transaction.rejected', 'transaction.failed' => $this->handlePaymentFailed($payment, $data),
            'transaction.cancelled' => $this->handlePaymentFailed($payment, $data),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    protected function handlePaymentSuccess(Payment $payment, array $data): void
    {
        if ($payment->status === 'confirmed') {
            return;
        }

        $months = $payment->plan === 'yearly' ? 12 : 1;

        $payment->update([
            'status' => 'confirmed',
            'transaction_id' => $data['id'] ?? null,
            'valid_from' => now(),
            'valid_until' => now()->addMonths($months),
        ]);

        $payment->user->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addMonths($months),
        ]);

        Log::info('Payment confirmed via Plexy', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'plan' => $payment->plan,
            'months' => $months,
        ]);
    }

    protected function handlePaymentFailed(Payment $payment, array $data): void
    {
        if ($payment->status === 'confirmed') {
            return;
        }

        $payment->update([
            'status' => 'failed',
            'transaction_id' => $data['id'] ?? null,
        ]);
    }

    public function success(Request $request, PlexyService $plexy)
    {
        $orderReference = $request->query('order');
        $payment = null;

        if ($orderReference) {
            $payment = Payment::where('order_reference', $orderReference)
                ->where('user_id', $request->user()->id)
                ->first();

            // Подстраховка: если webhook не дошёл, проверяем статус через API
            if ($payment && $payment->status === 'pending' && $payment->plexy_link_id) {
                $linkData = $plexy->getPaymentLink($payment->plexy_link_id);

                if ($linkData && in_array($linkData['status'] ?? '', ['charged', 'authorized', 'completed', 'paid'])) {
                    $this->handlePaymentSuccess($payment, $linkData);
                }
            }
        }

        return view('payment.success', compact('payment'));
    }

    public function fail(Request $request)
    {
        $orderReference = $request->query('order');

        return view('payment.fail', compact('orderReference'));
    }
}
