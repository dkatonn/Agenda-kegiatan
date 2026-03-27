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
        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('employee', 'public');
        }

        Employee::create([
            'name' => $request->name,
            'role' => $request->role,
            'image_path' => $path
        ]);

        return back();
    }

    public function update(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('employee', 'public');
            $emp->image_path = $path;
        }

        $emp->update([
            'name' => $request->name,
            'role' => $request->role
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
