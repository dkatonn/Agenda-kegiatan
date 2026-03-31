<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('videos') || Schema::hasColumn('videos', 'sort_order')) {
            return;
        }

        Schema::table('videos', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
        });

        $videoIds = DB::table('videos')->orderBy('id')->pluck('id');

        foreach ($videoIds as $index => $videoId) {
            DB::table('videos')
                ->where('id', $videoId)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('videos') || ! Schema::hasColumn('videos', 'sort_order')) {
            return;
        }

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
