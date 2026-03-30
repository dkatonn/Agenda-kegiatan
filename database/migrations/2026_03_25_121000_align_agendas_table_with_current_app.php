<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('agendas')) {
            return;
        }

        Schema::table('agendas', function (Blueprint $table) {
            if (! Schema::hasColumn('agendas', 'date')) {
                $table->date('date')->nullable()->after('id');
            }

            if (! Schema::hasColumn('agendas', 'time')) {
                $table->string('time')->nullable()->after('date');
            }

            if (! Schema::hasColumn('agendas', 'name')) {
                $table->string('name')->nullable()->after('time');
            }
        });

        if (Schema::hasColumn('agendas', 'agenda_date')) {
            DB::table('agendas')
                ->whereNull('date')
                ->whereNotNull('agenda_date')
                ->update(['date' => DB::raw('agenda_date')]);
        }

        if (Schema::hasColumn('agendas', 'agenda_time')) {
            DB::table('agendas')
                ->whereNull('time')
                ->whereNotNull('agenda_time')
                ->update(['time' => DB::raw('agenda_time')]);
        }

        if (Schema::hasColumn('agendas', 'title')) {
            DB::table('agendas')
                ->whereNull('name')
                ->whereNotNull('title')
                ->update(['name' => DB::raw('title')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('agendas')) {
            return;
        }

        Schema::table('agendas', function (Blueprint $table) {
            if (Schema::hasColumn('agendas', 'date')) {
                $table->dropColumn('date');
            }

            if (Schema::hasColumn('agendas', 'time')) {
                $table->dropColumn('time');
            }

            if (Schema::hasColumn('agendas', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
