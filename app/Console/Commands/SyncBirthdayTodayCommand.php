<?php

namespace App\Console\Commands;

use App\Services\KemendagriPegawaiService;
use Illuminate\Console\Command;

class SyncBirthdayTodayCommand extends Command
{
    protected $signature = 'birthday:sync-today';

    protected $description = 'Ambil data ulang tahun hari ini dari API Kemendagri dan simpan ke database lokal';

    public function handle(KemendagriPegawaiService $kemendagriPegawaiService): int
    {
        $count = $kemendagriPegawaiService->syncBirthdayToday();

        $this->info("Sinkronisasi ulang tahun selesai. Total data tersimpan: {$count}");

        return self::SUCCESS;
    }
}
