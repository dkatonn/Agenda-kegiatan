<?php

namespace Tests\Feature;

use App\Models\BirthdayToday;
use App\Services\KemendagriPegawaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncBirthdayTodayCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kemendagri_pegawai.base_url', 'https://apimanager-ropeg.kemendagri.go.id');
        config()->set('services.kemendagri_pegawai.username', 'AGENDA_PIMPINAN');
        config()->set('services.kemendagri_pegawai.password', 'secret');
        config()->set('services.kemendagri_pegawai.timeout', 10);
    }

    public function test_command_syncs_birthday_data_into_database(): void
    {
        Http::fake([
            'https://apimanager-ropeg.kemendagri.go.id/api/token' => Http::response([
                'access_token' => 'token-123',
            ]),
            'https://apimanager-ropeg.kemendagri.go.id/api/get_tgl_lahir_pegawai' => Http::response([
                'data' => [
                    ['nama_dengan_gelar' => 'Dr. Siti Aminah, M.Si'],
                    ['gelar_depan' => 'Ir.', 'nama' => 'Budi Santoso', 'gelar_belakang' => 'M.T.'],
                ],
            ]),
        ]);

        $this->artisan('birthday:sync-today')
            ->expectsOutputToContain('Total data tersimpan: 2')
            ->assertSuccessful();

        $this->assertDatabaseHas('birthday_todays', [
            'display_name' => 'Dr. Siti Aminah, M.Si',
        ]);

        $this->assertDatabaseHas('birthday_todays', [
            'display_name' => 'Ir. Budi Santoso, M.T.',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://apimanager-ropeg.kemendagri.go.id/api/get_tgl_lahir_pegawai'
                && $request->hasHeader('Authorization', 'Bearer token-123')
                && $request->hasHeader('auth', 'token-123')
                && $request['hari_ini'] === 1;
        });
    }

    public function test_ticker_text_is_built_from_database_cache(): void
    {
        BirthdayToday::query()->create([
            'birthday_date' => now()->toDateString(),
            'display_name' => 'Dr. Siti Aminah, M.Si',
            'fetched_at' => now(),
        ]);

        $text = app(KemendagriPegawaiService::class)->buildTickerText('Agenda pimpinan dimulai pukul 09.00');

        $this->assertSame(
            "Selamat berulang tahun : Dr. Siti Aminah, M.Si \u{1F389} | Agenda pimpinan dimulai pukul 09.00",
            $text
        );
    }

    public function test_ticker_text_attempts_sync_when_today_cache_is_empty(): void
    {
        Http::fake([
            'https://apimanager-ropeg.kemendagri.go.id/api/token' => Http::response([
                'access_token' => 'token-123',
            ]),
            'https://apimanager-ropeg.kemendagri.go.id/api/get_tgl_lahir_pegawai' => Http::response([
                'data' => [
                    ['nama_dengan_gelar' => 'Dr. Siti Aminah, M.Si'],
                ],
            ]),
        ]);

        $text = app(KemendagriPegawaiService::class)->buildTickerText('Agenda pimpinan dimulai pukul 09.00');

        $this->assertSame(
            "Selamat berulang tahun : Dr. Siti Aminah, M.Si \u{1F389} | Agenda pimpinan dimulai pukul 09.00",
            $text
        );

        $this->assertDatabaseHas('birthday_todays', [
            'birthday_date' => now()->toDateString(),
            'display_name' => 'Dr. Siti Aminah, M.Si',
        ]);
    }
}
