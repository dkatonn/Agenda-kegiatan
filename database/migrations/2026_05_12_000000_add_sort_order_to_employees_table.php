<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees') || Schema::hasColumn('employees', 'sort_order')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('image_path');
        });

        DB::table('employees')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->pluck('id')
            ->each(function ($employeeId, $index) {
                DB::table('employees')
                    ->where('id', $employeeId)
                    ->update(['sort_order' => $index + 1]);
            });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employees') || ! Schema::hasColumn('employees', 'sort_order')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
