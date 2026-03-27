<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Facades\Schema;

class AgendaController extends Controller
{
    public function index()
    {
        $agenda = Agenda::latest()->get();
        return view('admin.agenda', compact('agenda'));
    }

    public function store(Request $request)
    {
        Agenda::create($this->buildAgendaPayload($request));

        return back();
    }

    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $agenda->update($this->buildAgendaPayload($request));

        return back();
    }

    public function destroy($id)
    {
        Agenda::findOrFail($id)->delete();
        return back();
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    protected function buildAgendaPayload(Request $request): array
    {
        $payload = [
            'date' => $request->date,
            'time' => $request->time,
            'name' => $request->name,
            'location' => $request->location,
            'disposition' => $request->disposition,
        ];

        if (Schema::hasColumn('agendas', 'title')) {
            $payload['title'] = $request->name;
        }

        if (Schema::hasColumn('agendas', 'agenda_date')) {
            $payload['agenda_date'] = $request->date;
        }

        if (Schema::hasColumn('agendas', 'agenda_time')) {
            $payload['agenda_time'] = $request->time;
        }

        if (Schema::hasColumn('agendas', 'description')) {
            $payload['description'] = $request->disposition ?: '-';
        }

        if (Schema::hasColumn('agendas', 'unit')) {
            $payload['unit'] = 'data';
        }

        if (Schema::hasColumn('agendas', 'is_active')) {
            $payload['is_active'] = true;
        }

        return $payload;
    }
}
