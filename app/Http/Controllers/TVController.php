<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;
use App\Services\TataUsahaAgendaService;

class TVController extends Controller
{
    public function index(TataUsahaAgendaService $tataUsahaAgendaService)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $employees = Employee::latest()->get();
        $localAgendas = Agenda::query()
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $agendaTu = $tataUsahaAgendaService->fetchAgenda(6);

        if ($agendaTu->isEmpty()) {
            $agendaTu = $localAgendas->take(6)->values();
        }

        $agendaData = $localAgendas->take(6)->values();

        return view('tv', compact('settings', 'employees', 'agendaTu', 'agendaData'));
    }
}
