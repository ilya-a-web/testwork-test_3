<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('external_id')->unique();
            $table->string('name');
            $table->date('air_date')->nullable();
            $table->unsignedTinyInteger('season');
            $table->unsignedTinyInteger('episode');
            $table->string('code');
            $table->timestamps();

            $table->index('season');
            $table->index('air_date');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
