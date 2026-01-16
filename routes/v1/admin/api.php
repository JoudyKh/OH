<?php

use App\Constants\Constants;
use App\Http\Controllers\Api\Admin\Admins\AdminsController;
use App\Http\Controllers\Api\Admin\Classification\ClassificationController as AdminClassificationController;
use App\Http\Controllers\Api\Admin\GraduationProject\GraduationProjectController;
use App\Http\Controllers\Api\Admin\HomeSlider\HomeSliderController as AdminHomeSliderController;
use App\Http\Controllers\Api\Admin\Interview\InterviewController as AdminInterviewController;
use App\Http\Controllers\Api\Admin\InterviewRequest\InterviewRequestController;
use App\Http\Controllers\Api\Admin\Lecture\LectureController as AdminLectureController;
use App\Http\Controllers\Api\Admin\Library\LibraryController as AdminLibraryController;
use App\Http\Controllers\Api\Admin\LibraryFile\LibraryFileController as AdminLibraryFileController;
use App\Http\Controllers\Api\Admin\University\UniversityController as AdminUniversityController;
use App\Http\Controllers\Api\General\Classification\ClassificationController;
use App\Http\Controllers\Api\General\LibraryFile\LibraryFileController;
use App\Http\Controllers\Api\Admin\Notification\NotificationController;
use App\Http\Controllers\Api\Admin\Student\StudentController as AdminStudentController;
use App\Http\Controllers\Api\Admin\StudentProject\StudentProjectController;
use App\Http\Controllers\Api\General\HomeSlider\HomeSliderController;
use App\Http\Controllers\Api\General\Interview\InterviewController;
use App\Http\Controllers\Api\General\Lecture\LectureController;
use App\Http\Controllers\Api\General\Library\LibraryController;
use App\Http\Controllers\Api\General\University\UniversityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\General\Info\InfoController;
use App\Http\Controllers\Api\General\Auth\AuthController;
use App\Http\Controllers\Api\General\Section\SectionController;
use App\Http\Controllers\Api\Admin\Info\InfoController as AdminInfoController;
use App\Http\Controllers\Api\Admin\Section\SectionController as AdminSectionController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('admin.login');//
Route::post('reset-password', [AuthController::class, 'resetPassword']);//
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);//
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);//

Route::group(['middleware' => ['auth:api', 'last.active', 'ability:superadmin.admin.content_manager.project_manager']], function () {

    /** @Auth */
    Route::post('logout', [AuthController::class, 'logout']);//
    Route::get('/check/auth', [AuthController::class, 'authCheck']);//
    Route::get('profile', [AuthController::class, 'profile']);//
    Route::put('change-password', [AuthController::class, 'changePassword']);//
    Route::put('profile/update', [AuthController::class, 'updateProfile']);//


    Route::prefix('/students-projects')->group(function () {
        Route::get('/', [StudentProjectController::class, 'index']);
        Route::put('/{project}', [StudentProjectController::class, 'update']);
        Route::get('/{project}', [StudentProjectController::class, 'show']);
        Route::get('/{file}/download', [StudentProjectController::class, 'download']);
        Route::delete('/{id}/{force?}', [StudentProjectController::class, 'destroy']);
        Route::get('/{id}/restore/project', [StudentProjectController::class, 'restore']);
        Route::get('/export/excel', [StudentProjectController::class, 'exportExcel']);
        Route::get('/export/pdf', [StudentProjectController::class, 'exportPdf']);
    });
    Route::prefix('sections')->group(function () {
        /**
         * parent_id options :
         * empty for all section at the top layer
         * section id to get its sub sections .
         */
        Route::get('/{type}/{parentSection?}', [SectionController::class, 'index']);
        Route::get('/detail/{section}/show/one-section', [SectionController::class, 'show']);
        Route::post('/{type}/{parentSection?}', [AdminSectionController::class, 'store']);
        Route::prefix('/{section}')->group(function () {
            Route::put('/', [AdminSectionController::class, 'update']);
            Route::prefix('/files')->group(function () {
                Route::get('/all', [LibraryFileController::class, 'index']);
                Route::get('/{file}', [LibraryFileController::class, 'show']);
                Route::post('/store', [AdminLibraryFileController::class, 'store']);
                Route::put('/{file}', [AdminLibraryFileController::class, 'update']);
                Route::delete('/{file}', [AdminLibraryFileController::class, 'destroy']);
            });
        });
        Route::delete('/{id}/{force?}', [AdminSectionController::class, 'delete']);
        Route::get('/{id}/restore/section', [AdminSectionController::class, 'restore']);

    });
    Route::prefix('/libraries/{section}')->group(function () {
        Route::get('/', [LibraryController::class, 'index']);
        Route::get('/{library}', [LibraryController::class, 'show']);
        Route::post('/', [AdminLibraryController::class, 'store']);
        Route::put('/{library}', [AdminLibraryController::class, 'update']);
        Route::delete('/{id}/{force?}', [AdminLibraryController::class, 'destroy']);
        Route::get('/{id}/restore', [AdminLibraryController::class, 'restore']);

    });
    Route::prefix('/interviews')->group(function () {
        Route::get('/', [InterviewController::class, 'index']);
        Route::prefix('/{interview}')->group(function () {
            Route::get('/', [InterviewController::class, 'show']);
            Route::get('/requests', [InterviewRequestController::class, 'index']);
            Route::get('/requests/{request}', [InterviewRequestController::class, 'show']);
            Route::delete('/requests/{request}/{force?}', [InterviewRequestController::class, 'destroy']);
            Route::get('/requests/{request}/restore', [InterviewRequestController::class, 'restore']);
        });
        Route::post('/', [AdminInterviewController::class, 'store']);
        Route::put('/{interview}', [AdminInterviewController::class, 'update']);
        Route::delete('/{id}/{force?}', [AdminInterviewController::class, 'destroy']);
        Route::get('/{id}/restore', [AdminInterviewController::class, 'restore']);

    });

    Route::prefix('/sliders')->group(function () {
        Route::get('/', [HomeSliderController::class, 'index']);
        Route::get('/index/cities', [HomeSliderController::class, 'indexCities']);
        Route::get('/{slider}', [HomeSliderController::class, 'show']);
        Route::post('/', [AdminHomeSliderController::class, 'store']);
        Route::put('/{slider}', [AdminHomeSliderController::class, 'update']);
        Route::delete('/{slider}', [AdminHomeSliderController::class, 'destroy']);

    });
    Route::prefix('/classifications')->group(function () {
        Route::get('/', [ClassificationController::class, 'index']);
        Route::post('/', [AdminClassificationController::class, 'store']);
        Route::put('/{classification}', [AdminClassificationController::class, 'update']);
        Route::delete('/{id}', [AdminClassificationController::class, 'destroy']);

    });
    Route::prefix('/sub-sections/{section}/{type}')->group(function () {
        Route::get('/', [LectureController::class, 'index']);
        Route::get('/{lecture}', [LectureController::class, 'show']);
        Route::post('/', [AdminLectureController::class, 'store']);
        Route::put('/{lecture}', [AdminLectureController::class, 'update']);
        Route::delete('/{id}/{force?}', [AdminLectureController::class, 'destroy']);
        Route::get('/{id}/restore', [AdminLectureController::class, 'restore']);

    });

    Route::prefix('infos')->group(function () {
        Route::get('/', [InfoController::class, 'index']);
        Route::post('/update', [AdminInfoController::class, 'update']);
    });
    Route::prefix('/notifications')->group(function () {
        Route::get('/{read}', [NotificationController::class, 'getAllNotifications']);
    });
    Route::prefix('graduation-projects')->group(function () {
        Route::get('/', [GraduationProjectController::class, 'index']);
        Route::get('/{project}', [GraduationProjectController::class, 'show']);
        Route::delete('/{id}/{force?}', [GraduationProjectController::class, 'destroy']);
        Route::get('/{id}/restore', [GraduationProjectController::class, 'restore']);
    });
    Route::prefix('/universities')->group(function(){
        Route::get('/', [UniversityController::class, 'index']);
        Route::post('/', [AdminUniversityController::class, 'store']);
        Route::put('/{university}', [AdminUniversityController::class, 'update']);
        Route::delete('/{university}', [AdminUniversityController::class, 'destroy']);
    });
});

Route::group(['middleware' => ['auth:api', 'last.active', 'ability:admin.superadmin']], function () {
    Route::prefix('/admins')->group(function () {
        Route::get('/', [AdminsController::class, 'index']);
        Route::get('/{admin}', [AdminsController::class, 'show']);
        Route::post('/', [AdminsController::class, 'store']);
        Route::put('/{admin}', [AdminsController::class, 'update']);
        Route::delete('/{id}/{force?}', [AdminsController::class, 'destroy']);
        Route::get('/{id}/restore', [AdminsController::class, 'restore']);
    });

    Route::prefix('/students')->group(function () {
        Route::get('/', [AdminStudentController::class, 'index']);
        Route::get('/{student}', [AdminStudentController::class, 'show']);
        Route::post('/', [AdminStudentController::class, 'store']);
        Route::put('/{student}', [AdminStudentController::class, 'update']);
        Route::delete('/{id}/{force?}', [AdminStudentController::class, 'destroy']);
        Route::get('/{id}/restore', [AdminStudentController::class, 'restore']);
    });
});
