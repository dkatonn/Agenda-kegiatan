<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('agendas')) {
            Schema::table('agendas', function (Blueprint $table) {
                if (! Schema::hasColumn('agendas', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendas', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendas', 'locked_by')) {
                    $table->foreignId('locked_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendas', 'locked_at')) {
                    $table->timestamp('locked_at')->nullable()->after('locked_by');
                }
            });
        }

        if (Schema::hasTable('videos')) {
            Schema::table('videos', function (Blueprint $table) {
                if (! Schema::hasColumn('videos', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('videos', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('videos', 'locked_by')) {
                    $table->foreignId('locked_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('videos', 'locked_at')) {
                    $table->timestamp('locked_at')->nullable()->after('locked_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('agendas')) {
            Schema::table('agendas', function (Blueprint $table) {
                if (Schema::hasColumn('agendas', 'locked_at')) {
                    $table->dropColumn('locked_at');
                }

                foreach (['locked_by', 'updated_by', 'created_by'] as $column) {
                    if (Schema::hasColumn('agendas', $column)) {
                        $table->dropConstrainedForeignId($column);
                    }
                }
            });
        }

        if (Schema::hasTable('videos')) {
            Schema::table('videos', function (Blueprint $table) {
                if (Schema::hasColumn('videos', 'locked_at')) {
                    $table->dropColumn('locked_at');
                }

                foreach (['locked_by', 'updated_by', 'created_by'] as $column) {
                    if (Schema::hasColumn('videos', $column)) {
                        $table->dropConstrainedForeignId($column);
                    }
                }
            });
        }
    }
};
