<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lecture_paragraphs', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('lecture_id')
            ->constrained('lectures')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_paragraphs');
    }
};
