<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AdminPanelController extends Controller
{
    public function show(string $unit): View
    {
        abort_unless(in_array($unit, ['tu', 'data'], true), 404);

        $user = auth()->user();

        abort_unless(
            $user && ($user->role === 'superadmin' || $user->disposition === $unit),
            403
        );

        return view('admin.panel', [
            'unit' => $unit,
            'unitLabel' => $unit === 'tu' ? 'Admin TU' : 'Admin Data',
            'currentUser' => $user,
        ]);
    }
}
