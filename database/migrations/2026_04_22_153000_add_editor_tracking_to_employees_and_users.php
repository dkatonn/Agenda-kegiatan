<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('employees', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('employees', 'locked_by')) {
                    $table->foreignId('locked_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('employees', 'locked_at')) {
                    $table->timestamp('locked_at')->nullable()->after('locked_by');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('users', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('users', 'locked_by')) {
                    $table->foreignId('locked_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('users', 'locked_at')) {
                    $table->timestamp('locked_at')->nullable()->after('locked_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'locked_at')) {
                    $table->dropColumn('locked_at');
                }

                foreach (['locked_by', 'updated_by', 'created_by'] as $column) {
                    if (Schema::hasColumn('employees', $column)) {
                        $table->dropConstrainedForeignId($column);
                    }
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'locked_at')) {
                    $table->dropColumn('locked_at');
                }

                foreach (['locked_by', 'updated_by', 'created_by'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropConstrainedForeignId($column);
                    }
                }
            });
        }
    }
};
