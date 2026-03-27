<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Employee;
use App\Models\Setting;

class TVController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $employees = Employee::latest()->get();
        $agendas = Agenda::query()
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $agendaChunks = $agendas->chunk(6);
        $agendaTu = $agendaChunks->get(0, collect());
        $agendaData = $agendaChunks->get(1, collect());

        return view('tv', compact('settings', 'employees', 'agendaTu', 'agendaData'));
    }
}
