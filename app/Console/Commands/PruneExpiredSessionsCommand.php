<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneExpiredSessionsCommand extends Command
{
    protected $signature = 'sessions:prune-expired';

    protected $description = 'Hapus session yang sudah kedaluwarsa untuk menjaga tabel sessions tetap ringan';

    public function handle(): int
    {
        $expiredBefore = now()->subMinutes((int) config('session.lifetime', 10))->getTimestamp();

        $deleted = DB::table(config('session.table', 'sessions'))
            ->where('last_activity', '<', $expiredBefore)
            ->delete();

        $this->info("Pembersihan session selesai. Total session terhapus: {$deleted}");

        return self::SUCCESS;
    }
}
