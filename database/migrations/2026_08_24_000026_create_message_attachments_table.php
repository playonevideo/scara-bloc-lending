<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('name');
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'attachment_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_attachments');

        Schema::table('messages', function (Blueprint $table) {
            $table->string('attachment')->nullable();
            $table->string('attachment_name')->nullable();
        });
    }
};
