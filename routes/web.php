<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ConsentController;
use App\Http\Controllers\Web\BreachController;
use App\Http\Controllers\Web\RightsRequestController;
use App\Http\Controllers\Web\RopaController;
use App\Http\Controllers\Web\AssessmentController;
use App\Http\Controllers\Web\PrivacyNoticeController;
use App\Http\Controllers\Web\DpoTaskController;
use App\Http\Controllers\Web\TrainingController;
use App\Http\Controllers\Web\LogController;
use App\Http\Controllers\Web\ExternalPartyController;
use App\Http\Controllers\Web\DataMapController;
use App\Http\Controllers\Web\AccountController;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Privacy Notice
Route::get('/notice/{token}', [PrivacyNoticeController::class, 'publicView'])->name('privacy.public');

// Public Rights Portal
Route::get('/portal/{slug}', [RightsRequestController::class, 'portal'])->name('rights.portal');
Route::post('/portal/{slug}/submit', [RightsRequestController::class, 'submitPublic'])->name('rights.submit-public');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('consent')->name('consent.')->group(function () {
        Route::get('/', [ConsentController::class, 'index'])->name('index');
        Route::get('/create', [ConsentController::class, 'create'])->name('create');
        Route::post('/', [ConsentController::class, 'store'])->name('store');
        Route::get('/{template}', [ConsentController::class, 'show'])->name('show');
        Route::patch('/{consent}/withdraw', [ConsentController::class, 'withdraw'])->name('withdraw');
    });
    Route::prefix('rights')->name('rights.')->group(function () {
        Route::get('/', [RightsRequestController::class, 'index'])->name('index');
        Route::get('/{rightsRequest}', [RightsRequestController::class, 'show'])->name('show');
        Route::patch('/{rightsRequest}/status', [RightsRequestController::class, 'updateStatus'])->name('update-status');
        Route::post('/{rightsRequest}/notes', [RightsRequestController::class, 'addNote'])->name('add-note');
    });
    Route::prefix('ropa')->name('ropa.')->group(function () {
        Route::get('/', [RopaController::class, 'index'])->name('index');
        Route::get('/create', [RopaController::class, 'create'])->name('create');
        Route::post('/', [RopaController::class, 'store'])->name('store');
        Route::get('/export', [RopaController::class, 'export'])->name('export');
        Route::get('/{ropa}', [RopaController::class, 'show'])->name('show');
        Route::get('/{ropa}/edit', [RopaController::class, 'edit'])->name('edit');
        Route::put('/{ropa}', [RopaController::class, 'update'])->name('update');
        Route::post('/{ropa}/review', [RopaController::class, 'markReviewed'])->name('mark-reviewed');
    });
    Route::prefix('assessment')->name('assessment.')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('index');
        Route::get('/create', [AssessmentController::class, 'create'])->name('create');
        Route::post('/', [AssessmentController::class, 'store'])->name('store');
        Route::get('/{assessment}', [AssessmentController::class, 'show'])->name('show');
        Route::get('/{assessment}/edit', [AssessmentController::class, 'edit'])->name('edit');
        Route::put('/{assessment}', [AssessmentController::class, 'update'])->name('update');
        Route::post('/{assessment}/approve', [AssessmentController::class, 'approve'])->name('approve');
        Route::post('/{assessment}/answers', [AssessmentController::class, 'saveAnswers'])->name('save-answers');
        Route::get('/{assessment}/export', [AssessmentController::class, 'export'])->name('export');
    });
    Route::prefix('privacy')->name('privacy.')->group(function () {
        Route::get('/',                             [PrivacyNoticeController::class, 'index'])->name('index');
        Route::get('/create',                       [PrivacyNoticeController::class, 'create'])->name('create');
        Route::post('/',                            [PrivacyNoticeController::class, 'store'])->name('store');
        Route::get('/{notice}',                     [PrivacyNoticeController::class, 'show'])->name('show');
        Route::get('/{notice}/edit',                [PrivacyNoticeController::class, 'edit'])->name('edit');
        Route::put('/{notice}',                     [PrivacyNoticeController::class, 'update'])->name('update');
        Route::post('/{notice}/publish',            [PrivacyNoticeController::class, 'publish'])->name('publish');
        Route::post('/{notice}/unpublish',          [PrivacyNoticeController::class, 'unpublish'])->name('unpublish');
        Route::post('/{notice}/new-version',        [PrivacyNoticeController::class, 'newVersion'])->name('new-version');
        Route::delete('/{notice}',                  [PrivacyNoticeController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('dpo')->name('dpo.')->group(function () {
        Route::get('/',                              [DpoTaskController::class, 'index'])->name('index');
        Route::get('/create',                        [DpoTaskController::class, 'create'])->name('create');
        Route::post('/',                             [DpoTaskController::class, 'store'])->name('store');
        Route::get('/checklist',                     [DpoTaskController::class, 'checklist'])->name('checklist');
        Route::patch('/checklist/{item}',            [DpoTaskController::class, 'updateChecklistItem'])->name('checklist.update');
        Route::get('/{dpo}',                         [DpoTaskController::class, 'show'])->name('show');
        Route::get('/{dpo}/edit',                    [DpoTaskController::class, 'edit'])->name('edit');
        Route::put('/{dpo}',                         [DpoTaskController::class, 'update'])->name('update');
        Route::patch('/{dpo}/status',                [DpoTaskController::class, 'updateStatus'])->name('status');
        Route::delete('/{dpo}',                      [DpoTaskController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('training')->name('training.')->group(function () {
        Route::get('/',                               [TrainingController::class, 'index'])->name('index');
        Route::get('/create',                         [TrainingController::class, 'create'])->name('create');
        Route::post('/',                              [TrainingController::class, 'store'])->name('store');
        Route::get('/report',                         [TrainingController::class, 'report'])->name('report');
        Route::get('/{course}',                       [TrainingController::class, 'show'])->name('show');
        Route::get('/{course}/edit',                  [TrainingController::class, 'edit'])->name('edit');
        Route::put('/{course}',                       [TrainingController::class, 'update'])->name('update');
        Route::post('/{course}/quiz',                 [TrainingController::class, 'submitQuiz'])->name('quiz.submit');
        Route::get('/{course}/result/{completion}',   [TrainingController::class, 'result'])->name('result');
        Route::post('/{course}/toggle',               [TrainingController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{course}',                    [TrainingController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('breach')->name('breach.')->group(function () {
        Route::get('/', [BreachController::class, 'index'])->name('index');
        Route::get('/create', [BreachController::class, 'create'])->name('create');
        Route::post('/', [BreachController::class, 'store'])->name('store');
        Route::get('/{breach}', [BreachController::class, 'show'])->name('show');
        Route::post('/{breach}/timeline', [BreachController::class, 'addTimeline'])->name('timeline');
        Route::post('/{breach}/notify-pdpc', [BreachController::class, 'notifyPdpc'])->name('notify-pdpc');
    });

    // External Parties
    Route::prefix('parties')->name('parties.')->group(function () {
        Route::get('/',                                          [ExternalPartyController::class, 'index'])->name('index');
        Route::get('/create',                                    [ExternalPartyController::class, 'create'])->name('create');
        Route::post('/',                                         [ExternalPartyController::class, 'store'])->name('store');
        Route::get('/{party}',                                   [ExternalPartyController::class, 'show'])->name('show');
        Route::get('/{party}/edit',                              [ExternalPartyController::class, 'edit'])->name('edit');
        Route::put('/{party}',                                   [ExternalPartyController::class, 'update'])->name('update');
        Route::delete('/{party}',                                [ExternalPartyController::class, 'destroy'])->name('destroy');
        Route::post('/{party}/dpa',                              [ExternalPartyController::class, 'storeDpa'])->name('dpa.store');
        Route::put('/{party}/dpa/{dpa}',                         [ExternalPartyController::class, 'updateDpa'])->name('dpa.update');
        Route::post('/{party}/assessment',                       [ExternalPartyController::class, 'storeAssessment'])->name('assessment.store');
    });

    // Data Map
    Route::get('/data-map', [DataMapController::class, 'index'])->name('data-map.index');

    // Settings — Account Setup (admin only)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/',                              [AccountController::class, 'index'])->name('index');
            Route::get('/create',                        [AccountController::class, 'create'])->name('create');
            Route::post('/',                             [AccountController::class, 'store'])->name('store');
            Route::get('/{user}/edit',                   [AccountController::class, 'edit'])->name('edit');
            Route::put('/{user}',                        [AccountController::class, 'update'])->name('update');
            Route::patch('/{user}/password',             [AccountController::class, 'resetPassword'])->name('password');
            Route::patch('/{user}/toggle',               [AccountController::class, 'toggleStatus'])->name('toggle');
            Route::delete('/{user}',                     [AccountController::class, 'destroy'])->name('destroy');
        });
    });

    // System Logs (admin only)
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/',              [LogController::class, 'index'])->name('index');
        Route::get('/operation',     [LogController::class, 'operation'])->name('operation');
        Route::get('/security',      [LogController::class, 'security'])->name('security');
        Route::get('/data-access',   [LogController::class, 'dataAccess'])->name('data-access');
        Route::get('/consent-events',[LogController::class, 'consentEvents'])->name('consent-events');
        Route::get('/errors',        [LogController::class, 'errors'])->name('errors');
        Route::post('/security/{log}/resolve', [LogController::class, 'resolveSecurityLog'])->name('security.resolve');
        Route::post('/errors/{log}/resolve',   [LogController::class, 'resolveError'])->name('errors.resolve');
    });
});
