<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ResumeAwardController;
use App\Http\Controllers\Api\ResumeCertificateController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\Api\ResumeEducationController;
use App\Http\Controllers\Api\ResumeExperienceController;
use App\Http\Controllers\Api\ResumeLanguageController;
use App\Http\Controllers\Api\ResumeLinkController;
use App\Http\Controllers\Api\ResumeProjectController;
use App\Http\Controllers\Api\ResumePublicationController;
use App\Http\Controllers\Api\ResumeSkillController;
use App\Http\Controllers\Api\ResumeVersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Routes that do not require authentication.
|
*/

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
});


/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
|
| Routes that require a valid Laravel Sanctum token.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::post('/', [ProfileController::class, 'store']);
        Route::put('/', [ProfileController::class, 'update']);
    });


    /*
    |--------------------------------------------------------------------------
    | Resumes
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes',
        ResumeController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Resume Versions
    |--------------------------------------------------------------------------
    */

    Route::get(
        'resumes/{resume}/versions',
        [ResumeVersionController::class, 'index']
    );

    Route::post(
        'resumes/{resume}/versions',
        [ResumeVersionController::class, 'store']
    );

    Route::get(
        'resumes/{resume}/versions/{version}',
        [ResumeVersionController::class, 'show']
    );

    Route::put(
        'resumes/{resume}/versions/{version}',
        [ResumeVersionController::class, 'update']
    );

    Route::post(
        'resumes/{resume}/versions/{version}/activate',
        [ResumeVersionController::class, 'activate']
    );

    Route::delete(
        'resumes/{resume}/versions/{version}',
        [ResumeVersionController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | Resume Experiences
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.experiences',
        ResumeExperienceController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Educations
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.educations',
        ResumeEducationController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Projects
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.projects',
        ResumeProjectController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Certificates
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.certificates',
        ResumeCertificateController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Awards
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.awards',
        ResumeAwardController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Skills
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.skills',
        ResumeSkillController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Languages
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.languages',
        ResumeLanguageController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Publications
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.publications',
        ResumePublicationController::class
    )->except(['create', 'edit']);


    /*
    |--------------------------------------------------------------------------
    | Resume Links
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'resumes.links',
        ResumeLinkController::class
    )->except(['create', 'edit']);
});