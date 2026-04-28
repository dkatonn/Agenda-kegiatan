<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Deployment Notes

### Sinkronisasi ulang tahun Kemendagri

Fitur ulang tahun sekarang memakai cache database lokal pada tabel `birthday_todays`.

- Jalankan migrasi saat deploy: `php artisan migrate --force`
- Pastikan `.env` server berisi `APP_TIMEZONE=Asia/Jakarta`, `KEMENDAGRI_API_BASE_URL`, `KEMENDAGRI_API_USER`, `KEMENDAGRI_API_PASS`, dan `KEMENDAGRI_API_TIMEOUT`
- Untuk sinkronisasi manual: `php artisan birthday:sync-today`
- Scheduler aplikasi sudah menjadwalkan sinkronisasi harian jam `00:05` sesuai `APP_TIMEZONE`

### Cron VPS

Tambahkan cron berikut di server agar Laravel scheduler berjalan:

```bash
* * * * * cd /path/to/agenda-kegiatan && php artisan schedule:run >> /dev/null 2>&1
```

### Session Admin

- Session admin dibatasi 2 menit tanpa aktivitas
- Session admin diset berakhir saat browser ditutup lewat `SESSION_EXPIRE_ON_CLOSE=true`
- Tab admin baru tidak boleh melanjutkan session lama setelah tab sebelumnya ditutup
- Login admin dibatasi 5 percobaan per menit untuk kombinasi NIP dan IP
- Scheduler juga menjalankan pembersihan session kedaluwarsa setiap jam

### Email Reset Password

Fitur lupa password sudah siap dipakai, tetapi pengiriman email bergantung pada mailer yang dipilih di `.env`.

Opsi lokal:

- `MAIL_MAILER=log`
  Email tidak dikirim ke inbox, tetapi isi email dicatat ke `storage/logs/laravel.log`
- `MAIL_MAILER=smtp` dengan Mailpit / Mailhog
  Cocok untuk tes lokal jika Anda menjalankan server SMTP lokal di port `1025`

Menjalankan Mailpit lokal:

```bash
docker compose -f docker-compose.mailpit.yml up -d
```

Lalu gunakan env lokal seperti ini:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="no-reply@agenda.test"
MAIL_FROM_NAME="TV Agenda Local"
```

Inbox Mailpit bisa dibuka di:

```text
http://127.0.0.1:8025
```

Untuk uji kirim email manual:

```bash
php artisan mail:test email-anda@example.com
```

Opsi production:

- gunakan `MAIL_MAILER=smtp`
- isi `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- pastikan `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, dan `APP_URL` sudah benar

Template production yang umum:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.example.go.id
MAIL_PORT=587
MAIL_USERNAME=mailer@example.go.id
MAIL_PASSWORD=isi-password-smtp
MAIL_FROM_ADDRESS="no-reply@example.go.id"
MAIL_FROM_NAME="TV Agenda"
```

Checklist deploy email:

- jalankan `php artisan config:clear`
- pastikan port SMTP outbound tidak diblokir server
- uji kirim dengan `php artisan mail:test email-tujuan@example.com`
- uji alur `forgot-password` dari browser

Pembatas keamanan:

- satu email hanya dapat meminta link reset password sekali dalam 24 jam
- durasi cooldown diatur melalui `PASSWORD_RESET_REQUEST_COOLDOWN` dan default-nya `86400` detik
