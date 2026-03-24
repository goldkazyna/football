<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlexyService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.plexy.base_url');
        $this->apiKey = config('services.plexy.api_key');
    }

    public function createPaymentLink(array $params): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/payment-links", $params);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Plexy: failed to create payment link', [
            'status' => $response->status(),
            'body' => $response->body(),
            'params' => array_diff_key($params, ['metadata' => 1]),
        ]);

        return null;
    }

    public function getPaymentLink(string $id): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get("{$this->baseUrl}/payment-links/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function verifyWebhook(string $authHeader): bool
    {
        $secret = config('services.plexy.webhook_secret');

        return $authHeader === "Bearer {$secret}";
    }
}
