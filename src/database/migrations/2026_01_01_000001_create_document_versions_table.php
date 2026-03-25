<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {

            $table->id();

            $table->uuid('document_id');

            $table->string('version');

            $table->text('content');

            $table->string('checksum');

            $table->string('git_commit')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};