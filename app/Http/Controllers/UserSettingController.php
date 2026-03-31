<?php

namespace App\Http\Controllers;

class UserSettingController extends Controller
{
    public function index()
    {
        $admins = collect([
            (object) [
                'id' => 1,
                'name' => 'Admin Utama',
                'username' => 'adminutama',
                'nip' => '197812312006041001',
                'email' => 'adminutama@example.com',
                'role' => 'Super Admin',
                'status' => 'Aktif',
            ],
            (object) [
                'id' => 2,
                'name' => 'Operator TV',
                'username' => 'operatortv',
                'nip' => '198905142010121002',
                'email' => 'operator@example.com',
                'role' => 'Operator',
                'status' => 'Aktif',
            ],
        ]);

        return view('admin.usersetting', compact('admins'));
    }
}
