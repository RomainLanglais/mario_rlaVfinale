<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadStockService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    public function getStocks(int $page = 0, ?string $search = null): ?array
    {
        $params = ['page' => $page, 'size' => 10];
        if ($search) {
            $params['title'] = $search;
        }

        $url = $this->baseUrl . '/inventories';

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Inventory', ['url' => $url, 'params' => $params]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Inventory API KO', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Erreur API Inventory', ['msg' => $e->getMessage()]);
        }

        // Fallback DB
        return $this->getStocksFromDb($page, $search);
    }

    private function getStocksFromDb(int $page = 0, ?string $search = null): array
    {
        $size = 10;

        $query = DB::table('inventory as i')
            ->join('film as f', 'f.film_id', '=', 'i.film_id')
            ->join('store as s', 's.store_id', '=', 'i.store_id')
            ->leftJoin('address as a', 'a.address_id', '=', 's.address_id')
            ->select(
                'f.film_id',
                'f.title',
                'i.store_id',
                'a.address',
                'a.district',
                DB::raw('COUNT(i.inventory_id) as total'),
                DB::raw('SUM(CASE WHEN r.return_date IS NULL AND r.rental_id IS NOT NULL THEN 1 ELSE 0 END) as loues'),
                DB::raw('COUNT(i.inventory_id) - SUM(CASE WHEN r.return_date IS NULL AND r.rental_id IS NOT NULL THEN 1 ELSE 0 END) as disponibles')
            )
            ->leftJoin('rental as r', function ($join) {
                $join->on('r.inventory_id', '=', 'i.inventory_id')
                     ->whereNull('r.return_date');
            })
            ->groupBy('f.film_id', 'f.title', 'i.store_id', 'a.address', 'a.district');

        if ($search) {
            $query->where('f.title', 'like', "%{$search}%");
        }

        $total = (clone $query)->get()->count();
        $rows  = $query->offset($page * $size)->limit($size)->get();

        $content = $rows->map(fn($r) => [
            'filmId'      => (int) $r->film_id,
            'title'       => $r->title,
            'storeId'     => $r->store_id,
            'address'     => $r->address,
            'district'    => $r->district,
            'total'       => (int) $r->total,
            'loues'       => (int) $r->loues,
            'disponibles' => (int) $r->disponibles,
        ])->values()->toArray();

        return [
            'content'       => $content,
            'totalPages'    => (int) ceil($total / $size) ?: 1,
            'totalElements' => $total,
            'number'        => $page,
        ];
    }

    public function getFilmStock(int $filmId): ?array
    {
        $film = DB::table('film')->where('film_id', $filmId)->first();
        if (!$film) return null;

        $stores = DB::table('store as s')
            ->leftJoin('address as a', 'a.address_id', '=', 's.address_id')
            ->select('s.store_id', 'a.address', 'a.district')
            ->get();

        $dvds = DB::table('inventory as i')
            ->leftJoin('rental as r', function ($join) {
                $join->on('r.inventory_id', '=', 'i.inventory_id')
                     ->whereNull('r.return_date');
            })
            ->where('i.film_id', $filmId)
            ->select(
                'i.inventory_id',
                'i.store_id',
                DB::raw('CASE WHEN r.rental_id IS NOT NULL THEN 1 ELSE 0 END as is_rented')
            )
            ->get();

        $byStore = $stores->map(function ($store) use ($dvds) {
            $storeDvds = $dvds->where('store_id', $store->store_id);
            return [
                'storeId'  => $store->store_id,
                'address'  => $store->address,
                'district' => $store->district,
                'dvds'     => $storeDvds->map(fn($d) => [
                    'inventoryId' => $d->inventory_id,
                    'isRented'    => (bool) $d->is_rented,
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        return [
            'filmId' => $filmId,
            'title'  => $film->title,
            'stores' => $byStore,
        ];
    }

    public function addDvd(int $filmId, int $storeId): bool
    {
        try {
            $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) $headers['Authorization'] = "Bearer {$token}";

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($this->baseUrl . '/inventories', ['filmId' => $filmId, 'storeId' => $storeId]);

            if ($response->successful()) return true;
        } catch (\Throwable $e) {
            Log::error('Erreur API addDvd', ['msg' => $e->getMessage()]);
        }

        DB::table('inventory')->insert([
            'film_id'     => $filmId,
            'store_id'    => $storeId,
            'last_update' => now(),
        ]);

        return true;
    }

    public function removeDvd(int $inventoryId): bool|string
    {
        $rented = DB::table('rental')
            ->where('inventory_id', $inventoryId)
            ->whereNull('return_date')
            ->exists();

        if ($rented) {
            return 'Ce DVD est actuellement loué, impossible de le supprimer.';
        }

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) $headers['Authorization'] = "Bearer {$token}";

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->delete($this->baseUrl . '/inventories/' . $inventoryId);

            if ($response->successful()) return true;
        } catch (\Throwable $e) {
            Log::error('Erreur API removeDvd', ['msg' => $e->getMessage()]);
        }

        DB::table('inventory')->where('inventory_id', $inventoryId)->delete();

        return true;
    }

    private function getUserToken(): ?string
    {
        $userData = session('toad_user');
        return $userData['token'] ?? null;
    }
}
