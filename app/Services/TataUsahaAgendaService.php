<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TataUsahaAgendaService
{
    public function fetchAgenda(int $limit = 6): Collection
    {
        $baseUrl = rtrim((string) config('services.tata_usaha_agenda.base_url'), '/');
        $token = (string) config('services.tata_usaha_agenda.token');

        if ($baseUrl === '' || $token === '') {
            return collect();
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout((int) config('services.tata_usaha_agenda.timeout', 5))
                ->get($baseUrl . '/api/agenda', [
                    'include_past' => 1,
                    'sort' => 'date_desc',
                    'per_page' => $limit,
                ])
                ->throw();

            $payload = $response->json('data', []);

            return collect($payload)
                ->map(fn (array $item) => (object) [
                    'id' => $item['id'] ?? null,
                    'date' => $item['tanggal_kegiatan'] ?? now()->toDateString(),
                    'time' => $item['jam'] ?? '',
                    'name' => $item['nama_kegiatan'] ?? '-',
                    'location' => $item['tempat'] ?? '-',
                    'disposition' => $item['disposisi'] ?? '-',
                    'source' => 'api_tata_usaha',
                ])
                ->take($limit)
                ->values();
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengambil agenda Tata Usaha dari API.', [
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }
    }
}
