<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@agenda.test'],
            ['name' => 'Admin Agenda', 'password' => bcrypt('password')]
        );

        foreach ([
            'background' => null,
            'running_text' => 'Selamat datang di TV Agenda. Silakan kelola pegawai, agenda, dan video melalui panel admin.',
            'video' => null,
        ] as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        foreach ([
            ['name' => 'Dimas Pratama', 'role' => 'Kepala Biro SDM', 'image_path' => null],
            ['name' => 'Rani Wulandari', 'role' => 'Analis Kepegawaian', 'image_path' => null],
            ['name' => 'Fajar Nugroho', 'role' => 'Staff Administrasi', 'image_path' => null],
        ] as $employee) {
            Employee::query()->firstOrCreate(
                ['name' => $employee['name']],
                $employee
            );
        }

        foreach ([
            [
                'date' => now()->toDateString(),
                'time' => '08:00',
                'name' => 'Rapat Koordinasi Mingguan',
                'location' => 'Ruang Rapat Utama',
                'disposition' => 'Kepala Biro',
            ],
            [
                'date' => now()->addDay()->toDateString(),
                'time' => '10:00',
                'name' => 'Monitoring Agenda Unit',
                'location' => 'Ruang Data dan Informasi',
                'disposition' => 'Sekretariat',
            ],
            [
                'date' => now()->addDays(2)->toDateString(),
                'time' => '13:30',
                'name' => 'Evaluasi Kinerja Bulanan',
                'location' => 'Aula Lantai 2',
                'disposition' => 'Tim SDM',
            ],
        ] as $agenda) {
            $lookup = [
                'date' => $agenda['date'],
                'time' => $agenda['time'],
                'name' => $agenda['name'],
            ];

            $payload = $agenda;

            if (Schema::hasColumn('agendas', 'title')) {
                $lookup = ['title' => $agenda['name']];
                $payload['title'] = $agenda['name'];
            }

            if (Schema::hasColumn('agendas', 'agenda_date')) {
                $payload['agenda_date'] = $agenda['date'];
            }

            if (Schema::hasColumn('agendas', 'agenda_time')) {
                $payload['agenda_time'] = $agenda['time'];
            }

            if (Schema::hasColumn('agendas', 'description')) {
                $payload['description'] = $agenda['disposition'];
            }

            if (Schema::hasColumn('agendas', 'unit')) {
                $payload['unit'] = 'data';
            }

            if (Schema::hasColumn('agendas', 'is_active')) {
                $payload['is_active'] = true;
            }

            Agenda::query()->firstOrCreate($lookup, $payload);
        }
    }
}
