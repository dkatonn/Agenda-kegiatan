<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->whereNull('nip')->delete();

        User::query()->updateOrCreate(
            ['email' => 'superadmin@agenda.test'],
            [
                'name' => 'Super Admin',
                'nip' => '100000000000000001',
                'role' => 'superadmin',
                'unit' => 'tu',
                'disposition' => 'tu',
                'password' => 'password123',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin-tu@agenda.test'],
            [
                'name' => 'Admin TU',
                'nip' => '100000000000000002',
                'role' => 'admin',
                'unit' => 'tu',
                'disposition' => 'tu',
                'password' => 'password123',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin-data@agenda.test'],
            [
                'name' => 'Admin Data',
                'nip' => '100000000000000003',
                'role' => 'admin',
                'unit' => 'data',
                'disposition' => 'data',
                'password' => 'password123',
            ]
        );

        $this->call(ContentSeeder::class);
    }
}
