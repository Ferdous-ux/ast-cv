<?php

use App\Http\Controllers\Admin\StaffController;
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
use App\Http\Controllers\Api\ATSController;
use App\Http\Controllers\Api\MatchingController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\TargetJobController;
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
    *//*
|--------------------------------------------------------------------------
| Target Jobs
|--------------------------------------------------------------------------
*/

Route::prefix('target-jobs')
    ->name('target-jobs.')
    ->group(function () {

        Route::get(
            '/',
            [TargetJobController::class, 'index']
        )->name('index');

        Route::post(
            '/',
            [TargetJobController::class, 'store']
        )->name('store');

        Route::get(
            '/{job}',
            [TargetJobController::class, 'show']
        )->name('show');

        Route::put(
            '/{job}',
            [TargetJobController::class, 'update']
        )->name('update');

        Route::delete(
            '/{job}',
            [TargetJobController::class, 'destroy']
        )->name('destroy');

        Route::post(
            '/{job}/analyze',
            [TargetJobController::class, 'analyze']
        )
        ->middleware('throttle:10,1')
        ->name('analyze');
    });

    Route::apiResource(
        'resumes.links',
        ResumeLinkController::class
    )->except(['create', 'edit']);
/*
|--------------------------------------------------------------------------
| Reference Data
|--------------------------------------------------------------------------
*/

Route::prefix('reference')
    ->name('reference.')
    ->group(function () {

        Route::get(
            '/templates',
            [ReferenceController::class, 'templates']
        )->name('templates');

        Route::get(
            '/skills',
            [ReferenceController::class, 'skills']
        )->name('skills');

        Route::get(
            '/languages',
            [ReferenceController::class, 'languages']
        )->name('languages');
    });


/*
|--------------------------------------------------------------------------
| ATS
|--------------------------------------------------------------------------
*/

Route::prefix('ats')
    ->name('ats.')
    ->group(function () {

        Route::get(
            '/analyses',
            [ATSController::class, 'index']
        )->name('analyses.index');

        Route::post(
            '/analyze',
            [ATSController::class, 'analyze']
        )
        ->middleware('throttle:10,1')
        ->name('analyze');

        Route::get(
            '/analyses/{analysis}',
            [ATSController::class, 'show']
        )->name('analyses.show');
    });


/*
|--------------------------------------------------------------------------
| Matching
|--------------------------------------------------------------------------
*/

Route::prefix('matching')
    ->name('matching.')
    ->group(function () {

        Route::post(
            '/resumes/{version}/jobs',
            [MatchingController::class, 'jobsForResume']
        )
        ->middleware('throttle:5,1')
        ->name('resume.jobs');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Staff Management
    |--------------------------------------------------------------------------
    |
    | These endpoints are protected by Sanctum authentication.
    | Fine-grained authorization is enforced inside the
    | Form Requests and StaffPolicy.
    |
    */

    Route::prefix('admin')->group(function () {

        Route::get(
            '/staff',
            [StaffController::class, 'index']
        );

        Route::get(
            '/staff/{staff}',
            [StaffController::class, 'show']
        );

        Route::post(
            '/staff',
            [StaffController::class, 'store']
        );

        Route::put(
            '/staff/{staff}',
            [StaffController::class, 'update']
        );

        Route::delete(
            '/staff/{staff}',
            [StaffController::class, 'destroy']
        );

        Route::put(
            '/staff/{staff}/roles',
            [StaffController::class, 'updateRoles']
        );
    });
});