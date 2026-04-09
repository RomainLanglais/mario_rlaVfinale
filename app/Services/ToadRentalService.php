<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadRentalService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    /**
     * Récupère tous les customers
     */
    public function getAllCustomers(): ?array
    {
        $url = $this->baseUrl . '/customers';

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Customers', ['url' => $url]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Customers API KO', ['status' => $response->status()]);
        } catch (\Throwable $e) {
            Log::error('Erreur API Customers', ['msg' => $e->getMessage()]);
        }

        // Fallback DB
        return DB::table('customer')
            ->where('active', 1)
            ->orderBy('last_name')
            ->get()
            ->map(fn($c) => [
                'customerId' => $c->customer_id,
                'firstName'  => $c->first_name,
                'lastName'   => $c->last_name,
                'email'      => $c->email,
            ])
            ->toArray();
    }

    /**
     * Récupère toutes les locations d'un customer (tous statuts confondus)
     */
    public function getRentalsByCustomerId(int $customerId): ?array
    {
        $url = $this->baseUrl . '/rentals/customer/' . $customerId;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Rentals by Customer', ['url' => $url]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Rentals API KO', ['status' => $response->status()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Rentals', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère le token JWT depuis la session utilisateur
     */
    private function getUserToken(): ?string
    {
        $userData = session('toad_user');
        return $userData['token'] ?? null;
    }
}
