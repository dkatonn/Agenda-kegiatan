<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;
use Carbon\Carbon;

class TvRevisionService
{
    public function current(): string
    {
        $agendaCount = Agenda::query()->count();
        $employeeCount = Employee::query()->count();
        $settingCount = Setting::query()->count();
        $tickerText = app(KemendagriPegawaiService::class)->buildTickerText(
            Setting::where('key', 'running_text')->value('value')
        );

        $agendaUpdatedAt = $this->normalizeTimestamp(Agenda::query()->latest('updated_at')->value('updated_at'));
        $employeeUpdatedAt = $this->normalizeTimestamp(Employee::query()->latest('updated_at')->value('updated_at'));
        $settingUpdatedAt = $this->normalizeTimestamp(Setting::query()->latest('updated_at')->value('updated_at'));

        return sha1(implode('|', [
            $agendaCount,
            $employeeCount,
            $settingCount,
            $agendaUpdatedAt,
            $employeeUpdatedAt,
            $settingUpdatedAt,
            sha1($tickerText),
        ]));
    }

    protected function normalizeTimestamp($value): string
    {
        return $value ? Carbon::parse($value)->toDateTimeString() : 'none';
    }
}
