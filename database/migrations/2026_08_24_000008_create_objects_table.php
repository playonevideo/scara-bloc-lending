<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('condition')->default('good');
            $table->string('status')->default('available')->index();
            $table->unsignedSmallInteger('max_borrow_days')->default(7);
            $table->boolean('requires_personal_handover')->default(false);
            $table->boolean('can_leave_at_door')->default(false);
            $table->text('special_conditions')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_published']);
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objects');
    }
};
