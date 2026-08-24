<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->timestamps();

            $table->unique(['floor_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
