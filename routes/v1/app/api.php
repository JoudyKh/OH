<?php

use App\Constants\Constants;
use App\Http\Controllers\Api\App\GraduationProject\GraduationProjectController;
use App\Http\Controllers\Api\App\Home\HomeController;
use App\Http\Controllers\Api\App\InterviewRequest\InterviewRequestController;
use App\Http\Controllers\Api\App\StudentProject\StudentProjectController;
use App\Http\Controllers\Api\General\Classification\ClassificationController;
use App\Http\Controllers\Api\General\HomeSlider\HomeSliderController;
use App\Http\Controllers\Api\General\Interview\InterviewController;
use App\Http\Controllers\Api\General\Lecture\LectureController;
use App\Http\Controllers\Api\General\Library\LibraryController;
use App\Http\Controllers\Api\General\LibraryFile\LibraryFileController;
use App\Http\Controllers\Api\General\University\UniversityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\General\Auth\AuthController;
use App\Http\Controllers\Api\General\Info\InfoController;
use App\Http\Controllers\Api\General\Section\SectionController;
use App\Http\Controllers\Api\App\Auth\AuthController as AppAuthController;
use App\Http\Controllers\Api\App\ContactMessage\ContactMessageController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('user.login');//
Route::post('register', [AppAuthController::class, 'register'])->name('user.register');//
Route::post('reset-password', [AuthController::class, 'resetPassword']);//
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);//
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);//
Route::get('/get-phone_number', [AppAuthController::class, 'getPhoneNumber']);

Route::group(['middleware' => ['auth:api', 'last.active']], function () {
    /** @Auth */
    Route::post('logout', [AuthController::class, 'logout']);//
    Route::get('/check/auth', [AuthController::class, 'authCheck']);//
    Route::get('profile', [AuthController::class, 'profile']);//
    Route::put('change-password', [AuthController::class, 'changePassword']);//
    Route::put('profile/update', [AuthController::class, 'updateProfile']);//


    Route::group(['middleware' => ['ability:' . Constants::STUDENT_ROLE]], function () {
        
        Route::prefix('/students-projects')->group(function () {
            Route::get('/', [StudentProjectController::class, 'index']);
            Route::post('/', [StudentProjectController::class, 'store']);
        });
        
        
    });
    
});
Route::post('interviews/{interview}/requests/{type}', [InterviewRequestController::class, 'store']);

Route::prefix('/libraries/{section}')->group(function () {
    Route::get('/', [LibraryController::class, 'index']);
    Route::get('/{library}', [LibraryController::class, 'show']);
});
/**@Guest */

Route::prefix('sections')->group(function () {
    /**
     * parent_id options :
     * empty for all section at the top layer .
     * section id to get its sub sections .
     */
    Route::get('/{type}/{parentSection?}', [SectionController::class, 'index']);//
    Route::get('/detail/{section}/show/one-section"', [SectionController::class, 'show']);//
    Route::prefix('/{section}/files')->group(function(){
            Route::get('/all', [LibraryFileController::class, 'index']);
            Route::get('/{file}', [LibraryFileController::class, 'show']);
            Route::get('/{file}/download', [LibraryFileController::class, 'download']);

    });

});
Route::prefix('/interviews')->group(function () {
    Route::get('/', [InterviewController::class, 'index']);
    Route::prefix('/{interview}')->group(function () {
        Route::get('/', [InterviewController::class, 'show']);
    });
});
Route::prefix('infos')->group(function () {
    Route::get('/', [InfoController::class, 'index']);//
});
Route::prefix('/sliders')->group(function(){
    Route::get('/', [HomeSliderController::class, 'index']);
    Route::get('/{slider}', [HomeSliderController::class, 'show']);
});
Route::prefix('sub-sections/{section}/{type}')->group(function(){
    Route::get('/', [LectureController::class, 'index']);
    Route::get('/{lecture}', [LectureController::class, 'show']);
    Route::get('{lecture}/{file}/download', [LectureController::class, 'download']);
});
Route::get('/home', [HomeController::class, 'index']);
Route::prefix('graduation-projects')->group(function(){
    Route::post('/', [GraduationProjectController::class, 'store']);
});
Route::prefix('/classifications')->group(function(){
    Route::get('/', [ClassificationController::class, 'index']);
});
Route::get('/lectures', [LectureController::class, 'search']);

Route::get('/universities', [UniversityController::class, 'index']);