<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Profile;
use App\Models\RunningText;
use App\Models\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TvController extends Controller
{
    public function __invoke(): View
    {
        $data = $this->fallbackData();

        try {
            if (! $this->contentTablesExist()) {
                return view('tv', $data);
            }

            $profile = Profile::query()
                ->active()
                ->orderBy('display_order')
                ->first();

            $video = Video::query()
                ->active()
                ->orderBy('display_order')
                ->first();

            $runningText = RunningText::query()
                ->active()
                ->where(function ($query): void {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query): void {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                })
                ->orderBy('priority')
                ->latest()
                ->first();

            $tuAgendas = $this->agendaQuery('tu')->get();
            $dataAgendas = $this->agendaQuery('data')->get();

            return view('tv', [
                'profile' => $profile ?? $data['profile'],
                'video' => $video ?? $data['video'],
                'runningText' => $runningText?->message ?? $data['runningText'],
                'tuAgendas' => $tuAgendas->isNotEmpty() ? $tuAgendas : $data['tuAgendas'],
                'dataAgendas' => $dataAgendas->isNotEmpty() ? $dataAgendas : $data['dataAgendas'],
            ]);
        } catch (Throwable) {
            return view('tv', $data);
        }
    }

    private function agendaQuery(string $unit)
    {
        return Agenda::query()
            ->active()
            ->where(function ($query) use ($unit): void {
                $query->where('unit', $unit);

                if ($unit === 'data') {
                    $query->orWhere('unit', 'tu');
                }
            })
            ->orderBy('agenda_date')
            ->limit(7);
    }

    private function contentTablesExist(): bool
    {
        return Schema::hasTable('profiles')
            && Schema::hasTable('videos')
            && Schema::hasTable('agendas')
            && Schema::hasTable('running_texts');
    }

    private function fallbackData(): array
    {
        return [
            'profile' => (object) [
                'name' => 'Shaun Patrick Hendra',
                'position' => 'Magang Biro SDM',
                'photo_path' => null,
            ],
            'video' => (object) [
                'title' => 'Video Profil',
                'source_type' => 'url',
                'source_path' => '',
                'thumbnail_path' => null,
            ],
            'runningText' => 'Peringatan Hari Lahir Pancasila akan dilaksanakan di Lapangan Monas pukul 08:00 WIB.',
            'tuAgendas' => collect([
                $this->makeAgenda('Koordinasi Internal', '2026-03-10', '08:15', 'Ruang Rapat', 'Kasubag TU'),
                $this->makeAgenda('Briefing Pegawai', '2026-03-10', '09:00', 'Aula', 'Staff TU'),
                $this->makeAgenda('Validasi Dokumen', '2026-03-10', '10:15', 'Lt.2', 'Admin TU'),
                $this->makeAgenda('Pemeriksaan Berkas', '2026-03-11', '08:30', 'Ruang Arsip', 'Arsiparis'),
                $this->makeAgenda('Sinkronisasi Data', '2026-03-11', '13:00', 'Zoom', 'Operator'),
                $this->makeAgenda('Evaluasi Mingguan', '2026-03-12', '09:30', 'Aula', 'Kasubag TU'),
                $this->makeAgenda('Rencana Program', '2026-03-12', '14:45', 'Ruang Rapat', 'Tim TU'),
            ]),
            'dataAgendas' => collect([
                $this->makeAgenda('Monitoring Dashboard', '2026-03-10', '08:00', 'Command Center', 'Analis Data'),
                $this->makeAgenda('Update Integrasi API', '2026-03-10', '10:00', 'Ruang Server', 'Programmer'),
                $this->makeAgenda('Uji Validitas Dataset', '2026-03-10', '13:30', 'Lab Data', 'Data Engineer'),
                $this->makeAgenda('Rapat Lintas Unit', '2026-03-11', '09:15', 'Zoom', 'Koordinator'),
                $this->makeAgenda('Pemetaan KPI', '2026-03-11', '11:00', 'Lt.3', 'Analis Sistem'),
                $this->makeAgenda('Review SLA Layanan', '2026-03-12', '15:00', 'Ruang Rapat', 'Supervisor'),
                $this->makeAgenda('Pelaporan Mingguan', '2026-03-12', '16:15', 'Command Center', 'Tim Data'),
            ]),
        ];
    }

    private function makeAgenda(string $title, string $date, string $time, string $location, string $disposition): object
    {
        return (object) [
            'title' => $title,
            'agenda_date' => $date,
            'agenda_time' => $time,
            'location' => $location,
            'disposition' => $disposition,
        ];
    }
}
