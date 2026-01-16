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
        Schema::create('interview_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('type', Constants::INTERVIEW_REQUESTS_TYPES);
            // participation
            $table->string('name')->nullable();
            $table->string('academic_achievement')->nullable();
            $table->unsignedBigInteger('university_id')->nullable();
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('set null');
            $table->string('phone_number')->nullable();
            // cartoon and electronic
            $table->string('first_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('national_id')->nullable();
            $table->string('registration_place')->nullable();
            $table->string('central_secretariat')->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();

            $table->text('address')->nullable();
            // cartoon
            $table->text('delivery_address')->nullable();
            // $table->foreignId('student_id')
            // ->constrained('users')
            // ->cascadeOnDelete()
            // ->cascadeOnUpdate();
            $table->foreignId('interview_id')
            ->constrained('interviews')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_requests');
    }
};
