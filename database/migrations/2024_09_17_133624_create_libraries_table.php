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
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');
            $table->string('phone_number');
            $table->text('address');
            $table->boolean('is_special')->default(0);
            $table->foreignId('section_id')
            ->constrained('sections')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->integer('sort_order')->default(1);
            // $table->unique(['section_id', 'sort_order'], 'unique_section_id_order');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};
