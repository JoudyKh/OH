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
        Schema::create('student_project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_project_id')
            ->constrained('student_projects')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->string('file');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_project_files');
    }
};
