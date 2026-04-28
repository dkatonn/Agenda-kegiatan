<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_todays', function (Blueprint $table) {
            $table->id();
            $table->date('birthday_date');
            $table->string('display_name');
            $table->json('source_payload')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index('birthday_date');
            $table->unique(['birthday_date', 'display_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_todays');
    }
};
