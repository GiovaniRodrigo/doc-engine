<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_activity_log', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('event', 64)->index();
            $table->string('slug', 512)->nullable()->index();
            $table->string('actor_type', 32)->nullable();
            $table->string('actor_id', 255)->nullable();
            $table->string('actor_name', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_activity_log');
    }
};
