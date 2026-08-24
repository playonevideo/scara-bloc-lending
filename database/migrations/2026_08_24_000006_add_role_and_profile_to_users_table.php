<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('resident')->index()->after('email');
            $table->string('phone')->nullable()->index()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->unsignedBigInteger('apartment_id')->nullable()->index()->after('phone_verified_at');
            $table->boolean('is_blocked')->default(false)->after('apartment_id');
            $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            $table->boolean('show_apartment')->default(true)->after('blocked_at');
            $table->boolean('show_floor')->default(true)->after('show_apartment');
            $table->boolean('show_phone')->default(false)->after('show_floor');
            $table->boolean('show_email')->default(false)->after('show_phone');
            $table->timestamp('last_seen_at')->nullable()->after('show_email');
            $table->softDeletes()->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'phone_verified_at', 'apartment_id', 'is_blocked',
                'blocked_at', 'show_apartment', 'show_floor', 'show_phone',
                'show_email', 'last_seen_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
