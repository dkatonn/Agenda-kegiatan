<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees') || Schema::hasColumn('employees', 'nip')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->unique()->after('name');
        });

        DB::table('employees')
            ->whereNull('nip')
            ->orderBy('id')
            ->get()
            ->each(function ($employee) {
                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update([
                        'nip' => str_pad((string) $employee->id, 18, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employees') || ! Schema::hasColumn('employees', 'nip')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropColumn('nip');
        });
    }
};
