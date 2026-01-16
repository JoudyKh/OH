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
        Schema::create('library_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('file');
            $table->foreignId('sub_library_id')
            ->constrained('sections')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->integer('sort_order')->default(1);
            // $table->unique(['sub_library_id', 'sort_order']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_files');
    }
};
