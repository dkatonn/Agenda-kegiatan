<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;

class AgendaController extends Controller
{
    public function index()
    {
        $agenda = Agenda::latest()->get();
        return view('admin.agenda', compact('agenda'));
    }

    public function store(Request $request)
    {
        Agenda::create([
            'date' => $request->date,
            'time' => $request->time,
            'name' => $request->name,
            'location' => $request->location,
            'disposition' => $request->disposition
        ]);

        return back();
    }

    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $agenda->update([
            'date' => $request->date,
            'time' => $request->time,
            'name' => $request->name,
            'location' => $request->location,
            'disposition' => $request->disposition
        ]);

        return back();
    }

    public function destroy($id)
    {
        Agenda::findOrFail($id)->delete();
        return back();
    }
}
