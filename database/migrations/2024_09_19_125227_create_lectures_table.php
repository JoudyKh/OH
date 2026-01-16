<?php

use App\Constants\Constants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', array_keys(Constants::SUB_SECTION_TYPES));
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->string('requirements_image')->nullable();
            $table->string('city')->nullable();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('classification_id')->nullable();
            $table->foreign('classification_id')->references('id')->on('classifications')->onDelete('set null');
            $table->foreignId('sub_section_id')
            ->constrained('sections')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->integer('sort_order')->default(value: 1);
            // $table->unique(['type', 'sort_order', 'sub_section_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
