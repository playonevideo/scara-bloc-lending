<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staircase_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->timestamps();

            $table->unique(['staircase_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
