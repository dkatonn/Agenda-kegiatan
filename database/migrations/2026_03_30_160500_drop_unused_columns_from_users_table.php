<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];

            foreach (['role', 'unit', 'disposition'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'unit')) {
                $table->string('unit')->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'disposition')) {
                $table->string('disposition')->nullable()->after('unit');
            }
        });
    }
};
