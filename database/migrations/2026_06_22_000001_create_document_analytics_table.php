<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_analytics', function (Blueprint $table) {
            $table->string('slug', 512)->primary();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('edits_count')->default(0);
            $table->unsignedBigInteger('publishes_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_edited_at')->nullable();
            $table->timestamp('last_published_at')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_analytics');
    }
};
