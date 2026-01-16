<?php

use App\Constants\Constants;
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
        Schema::create('ad_views', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('country');
            $table->text('finger_print');
            $table->text('geo_info');
            $table->nullableMorphs('model');
            $table->enum('view',[Constants::HOME_VIEW,Constants::LIBRARIES_VIEW,Constants::SUPER_SECTIONS_VIEW,Constants::ONE_SECTION_VIEW,Constants::INTERVIEWS_VIEW,Constants::LECTURES_VIEW,Constants::ONE_LECTURE_VIEW,Constants::TERMS_VIEW,Constants::PRIVACY_VIEW,Constants::CONTACT_US_VIEW,Constants::ONE_LIBRARY_VIEW,Constants::LIBRARY_SECTIONS_VIEW, Constants::ONE_INTERVIEW_VIEW, Constants::LECTURES_SECTIONS_VIEW, Constants::PROJECTS_VIEW, Constants::SUPER_LIBRARY_SECTION_VIEW, Constants::PROJECTS_SECTIONS_VIEW, Constants::ONE_PROJECT_VIEW, Constants::SUB_LIBRARY_FILES_VIEW]);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_views');
    }
};
