<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $employee = Employee::all();
        return view('admin.employee', compact('employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'digits:18', 'unique:employees,nip'],
            'role' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ], [
            'nip.digits' => 'NIP pegawai harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP pegawai ini sudah digunakan.',
        ]);

        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('employee', 'public');
        }

        Employee::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role'],
            'image_path' => $path
        ]);

        return back();
    }

    public function update(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'digits:18', 'unique:employees,nip,' . $emp->id],
            'role' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image'],
        ], [
            'nip.digits' => 'NIP pegawai harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP pegawai ini sudah digunakan.',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('employee', 'public');
            $emp->image_path = $path;
        }

        $emp->update([
            'name' => $validated['name'],
            'nip' => $validated['nip'] ?? null,
            'role' => $validated['role']
        ]);

        return back();
    }

    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();
        return back();
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
