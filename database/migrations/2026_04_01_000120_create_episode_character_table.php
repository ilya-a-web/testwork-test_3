<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episode_character', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('episode_id')->constrained()->cascadeOnDelete();
            $table->foreignId('character_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['episode_id', 'character_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episode_character');
    }
};
