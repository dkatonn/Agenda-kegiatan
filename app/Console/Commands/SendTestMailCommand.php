<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMailCommand extends Command
{
    protected $signature = 'mail:test {email : Alamat email penerima uji coba}';

    protected $description = 'Kirim email uji coba untuk memverifikasi konfigurasi mailer.';

    public function handle(): int
    {
        $recipient = (string) $this->argument('email');

        Mail::raw(
            "Email uji coba dari aplikasi TV Agenda berhasil dikirim.\n\n"
            . 'Mailer: ' . config('mail.default') . "\n"
            . 'Waktu: ' . now()->timezone(config('app.timezone'))->format('Y-m-d H:i:s T'),
            function ($message) use ($recipient) {
                $message
                    ->to($recipient)
                    ->subject('Uji Coba Email TV Agenda');
            }
        );

        $this->info("Email uji coba berhasil diproses untuk {$recipient}.");
        $this->line('Jika mailer masih `log`, periksa storage/logs/laravel.log.');

        return self::SUCCESS;
    }
}
