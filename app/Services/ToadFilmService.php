<?php


namespace App\Services;

use App\Models\Film;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadFilmService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    public function getAllFilms(): ?array
    {
        $url = $this->baseUrl . '/films';

        try {
            $headers = ['Accept' => 'application/json'];
            
            // Récupère le token JWT depuis la session
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Films', ['url' => $url, 'has_token' => !empty($token)]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Films API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Films', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getFilmById(int $id): ?array
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::error('Erreur API Film', ['msg' => $e->getMessage()]);
        }

        // Fallback DB
        $film = Film::find($id);
        if (!$film) return null;
        return $this->filmToArray($film);
    }

    public function createFilm(array $data): ?array
    {
        $url = $this->baseUrl . '/films';

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $response = Http::withHeaders($headers)
                ->asJson()
                ->timeout(10)
                ->post($url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Création film API KO', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Erreur création film', ['msg' => $e->getMessage()]);
        }

        // Fallback DB
        $film = Film::create([
            'title'               => $data['title'],
            'description'         => $data['description'] ?? null,
            'release_year'        => $data['releaseYear'] ?? null,
            'original_language_id'=> $data['originalLanguageId'],
            'rental_duration'     => $data['rentalDuration'],
            'rental_rate'         => $data['rentalRate'],
            'length'              => $data['length'] ?? null,
            'replacement_cost'    => $data['replacementCost'] ?? 0.00,
            'rating'              => $data['rating'] ?? null,
            'last_update'         => now(),
        ]);
        return $this->filmToArray($film);
    }

    public function updateFilm(int $id, array $data): ?array
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $response = Http::withHeaders($headers)
                ->asJson()
                ->timeout(10)
                ->put($url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Modification film API KO', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Erreur modification film', ['msg' => $e->getMessage()]);
        }

        // Fallback DB
        $film = Film::find($id);
        if (!$film) return null;
        $film->update([
            'title'               => $data['title'],
            'description'         => $data['description'] ?? null,
            'release_year'        => $data['releaseYear'] ?? null,
            'original_language_id'=> $data['originalLanguageId'],
            'rental_duration'     => $data['rentalDuration'],
            'rental_rate'         => $data['rentalRate'],
            'length'              => $data['length'] ?? null,
            'replacement_cost'    => $data['replacementCost'] ?? null,
            'rating'              => $data['rating'] ?? null,
            'last_update'         => now(),
        ]);
        return $this->filmToArray($film->fresh());
    }

    public function deleteFilm(int $id): bool
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->delete($url);

            if ($response->successful()) {
                return true;
            }
        } catch (\Throwable $e) {
            Log::error('Erreur suppression film', ['msg' => $e->getMessage()]);
        }

        // Fallback DB
        $film = Film::find($id);
        if (!$film) return false;
        return (bool) $film->delete();
    }

    /**
     * Récupère le token JWT depuis la session utilisateur
     */
    private function getUserToken(): ?string
    {
        $userData = session('toad_user');
        Log::info('Récupération token utilisateur', ['userData' => $userData]);

        return $userData['token'] ?? null;
    }

    private function filmToArray(Film $film): array
    {
        return [
            'filmId'             => $film->film_id,
            'title'              => $film->title,
            'description'        => $film->description,
            'releaseYear'        => $film->release_year,
            'originalLanguageId' => $film->original_language_id,
            'rentalDuration'     => $film->rental_duration,
            'rentalRate'         => $film->rental_rate,
            'length'             => $film->length,
            'replacementCost'    => $film->replacement_cost,
            'rating'             => $film->rating,
        ];
    }
}