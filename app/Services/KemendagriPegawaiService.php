<?php

namespace App\Services;

use App\Models\BirthdayToday;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KemendagriPegawaiService
{
    protected bool $hasAttemptedTodaySync = false;

    protected function httpClient()
    {
        return Http::withOptions([
            'verify' => config('services.kemendagri_pegawai.verify_ssl', true),
        ]);
    }

    public function buildTickerText(?string $runningText): string
    {
        $segments = $this->birthdaySegments()
            ->push($this->sanitizeSegment($runningText))
            ->filter(fn (?string $segment) => filled($segment))
            ->values();

        return $segments->implode(' | ');
    }

    public function birthdaySegments(?CarbonInterface $date = null): Collection
    {
        return $this->getBirthdayEmployees($date)
            ->map(fn (array $employee) => $this->formatBirthdayMessage($employee))
            ->filter(fn (?string $message) => filled($message))
            ->values();
    }

    public function getBirthdayEmployees(?CarbonInterface $date = null): Collection
    {
        $date ??= now();

        $employees = BirthdayToday::query()
            ->whereDate('birthday_date', $date->toDateString())
            ->orderBy('display_name')
            ->get()
            ->map(fn (BirthdayToday $birthday) => [
                'display_name' => $birthday->display_name,
            ])
            ->values();

        if ($employees->isNotEmpty() || ! $this->shouldAttemptSync($date)) {
            return $employees;
        }

        $this->hasAttemptedTodaySync = true;
        $this->syncBirthdayToday($date);

        return BirthdayToday::query()
            ->whereDate('birthday_date', $date->toDateString())
            ->orderBy('display_name')
            ->get()
            ->map(fn (BirthdayToday $birthday) => [
                'display_name' => $birthday->display_name,
            ])
            ->values();
    }

    public function syncBirthdayToday(?CarbonInterface $date = null): int
    {
        $date ??= now();
        $timestamp = now();
        $birthdayDate = $date->toDateString();
        $employees = $this->fetchBirthdayTodayFromApi();

        DB::transaction(function () use ($birthdayDate, $employees, $timestamp) {
            BirthdayToday::query()
                ->whereDate('birthday_date', $birthdayDate)
                ->delete();

            if ($employees->isEmpty()) {
                return;
            }

            BirthdayToday::query()->insert(
                $employees
                    ->map(fn (array $employee) => [
                        'birthday_date' => $birthdayDate,
                        'display_name' => $employee['display_name'],
                        'source_payload' => isset($employee['source_payload']) ? json_encode($employee['source_payload'], JSON_UNESCAPED_UNICODE) : null,
                        'fetched_at' => $timestamp,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ])
                    ->all()
            );
        });

        return $employees->count();
    }

    public function fetchBirthdayTodayFromApi(): Collection
    {
        $baseUrl = rtrim((string) config('services.kemendagri_pegawai.base_url'), '/');
        $username = (string) config('services.kemendagri_pegawai.username');
        $password = (string) config('services.kemendagri_pegawai.password');

        if ($baseUrl === '' || $username === '' || $password === '') {
            return collect();
        }

        try {
            $accessToken = $this->requestAccessToken($baseUrl, $username, $password);

            if (! filled($accessToken)) {
                return collect();
            }

            $response = $this->httpClient()
                ->asForm()
                ->acceptJson()
                ->timeout((int) config('services.kemendagri_pegawai.timeout', 10))
                ->retry(1, 250)
                ->withToken($accessToken)
                ->withHeaders([
                    'auth' => $accessToken,
                ])
                ->post($baseUrl . '/api/get_tgl_lahir_pegawai', [
                    'hari_ini' => 1,
                ])
                ->throw();

            return $this->extractEmployees($response->json());
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengambil data ulang tahun pegawai dari API Kemendagri.', [
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    protected function requestAccessToken(string $baseUrl, string $username, string $password): ?string
    {
        $response = $this->httpClient()
            ->asForm()
            ->acceptJson()
            ->timeout((int) config('services.kemendagri_pegawai.timeout', 10))
            ->retry(1, 250)
            ->post($baseUrl . '/api/token', [
                'username' => $username,
                'password' => $password,
            ])
            ->throw();

        $payload = $response->json();

        if (! is_array($payload)) {
            return null;
        }

        foreach (['access_token', 'token', 'data.access_token'] as $key) {
            $token = data_get($payload, $key);

            if (is_string($token) && trim($token) !== '') {
                return trim($token);
            }
        }

        return null;
    }

    protected function extractEmployees(mixed $payload): Collection
    {
        if (! is_array($payload)) {
            return collect();
        }

        $records = $this->extractRecordList($payload);

        return collect($records)
            ->filter(fn ($record) => is_array($record))
            ->map(fn (array $record) => [
                'display_name' => $this->resolveDisplayName($record),
                'source_payload' => $record,
            ])
            ->filter(fn (array $record) => filled($record['display_name']))
            ->unique('display_name')
            ->values();
    }

    protected function extractRecordList(array $payload): array
    {
        foreach (['data', 'result', 'results', 'pegawai', 'items'] as $key) {
            $value = data_get($payload, $key);

            if ($this->isRecordList($value)) {
                return $value;
            }
        }

        if ($this->isRecordList($payload)) {
            return $payload;
        }

        foreach ($payload as $value) {
            if (is_array($value)) {
                $records = $this->extractRecordList($value);

                if ($records !== []) {
                    return $records;
                }
            }
        }

        return [];
    }

    protected function isRecordList(mixed $value): bool
    {
        if (! is_array($value) || $value === [] || ! array_is_list($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (is_array($item)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveDisplayName(array $record): string
    {
        foreach ([
            'nama_dengan_gelar',
            'nama_dan_gelar',
            'nama_lengkap_gelar',
            'nama_pegawai_gelar',
        ] as $key) {
            $value = $this->sanitizeSegment(data_get($record, $key));

            if ($value !== '') {
                return $value;
            }
        }

        $frontTitle = $this->sanitizeSegment(data_get($record, 'gelar_depan'));
        $name = $this->sanitizeSegment(data_get($record, 'nama_pegawai') ?? data_get($record, 'nama') ?? data_get($record, 'nama_lengkap'));
        $backTitle = $this->sanitizeSegment(data_get($record, 'gelar_belakang'));

        $composedName = collect([$frontTitle, $name])
            ->filter(fn (string $value) => $value !== '')
            ->implode(' ');

        if ($composedName !== '' && $backTitle !== '') {
            return $composedName . ', ' . $backTitle;
        }

        if ($composedName !== '') {
            return $composedName;
        }

        return collect([
            $this->sanitizeSegment(data_get($record, 'nama_pegawai')),
            $this->sanitizeSegment(data_get($record, 'nama')),
            $this->sanitizeSegment(data_get($record, 'nama_lengkap')),
        ])->first(fn (string $value) => $value !== '', '');
    }

    protected function formatBirthdayMessage(array $employee): ?string
    {
        $name = $this->sanitizeSegment($employee['display_name'] ?? null);

        if ($name === '') {
            return null;
        }

        return "Selamat berulang tahun : {$name} \u{1F389}";
    }

    protected function sanitizeSegment(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    protected function shouldAttemptSync(CarbonInterface $date): bool
    {
        if ($this->hasAttemptedTodaySync) {
            return false;
        }

        return $date->isSameDay(now());
    }
}
