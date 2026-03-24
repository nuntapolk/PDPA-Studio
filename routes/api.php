<?php

use App\Http\Controllers\Api\V1\ConsentApiController;
use App\Http\Controllers\Api\V1\RightsApiController;
use App\Http\Controllers\Api\V1\BreachApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PDPA Studio API v1
|--------------------------------------------------------------------------
| Authentication: Bearer Token (Laravel Sanctum) หรือ API Key
|
| Headers:
|   Authorization: Bearer {token}    ← สำหรับ User Token
|   X-Api-Key: {api_key}             ← สำหรับ API Key
|
| Rate Limit: 60 requests/minute per organization
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Health Check (Public)
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'PDPA Studio API',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('health');

    // ── Authenticated API Routes ──────────────────────────────
    Route::middleware(['api.auth', 'api.rate-limit'])->group(function () {

        // MODULE 1: Consents
        Route::prefix('consents')->name('consents.')->group(function () {
            Route::post('/', [ConsentApiController::class, 'store'])->name('store');
            Route::get('/subject/{email}', [ConsentApiController::class, 'getBySubject'])->name('by-subject');
            Route::delete('/{id}', [ConsentApiController::class, 'withdraw'])->name('withdraw');
        });

        // MODULE 2: Rights Requests
        Route::prefix('rights')->name('rights.')->group(function () {
            Route::post('/requests', [RightsApiController::class, 'store'])->name('store');
            Route::get('/requests/{ticket}', [RightsApiController::class, 'status'])->name('status');
        });

        // MODULE 4: Breach
        Route::prefix('breach')->name('breach.')->group(function () {
            Route::get('/', [BreachApiController::class, 'index'])->name('index');
            Route::post('/report', [BreachApiController::class, 'report'])->name('report');
        });

        // Data Subjects
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\DataSubjectApiController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\V1\DataSubjectApiController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Api\V1\DataSubjectApiController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Api\V1\DataSubjectApiController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Api\V1\DataSubjectApiController::class, 'destroy'])->name('destroy');
        });

        // Cookie Consents
        Route::prefix('cookie-consents')->name('cookie.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Api\V1\CookieConsentApiController::class, 'store'])->name('store');
            Route::get('/{visitorId}', [\App\Http\Controllers\Api\V1\CookieConsentApiController::class, 'show'])->name('show');
        });

        // ROPA (Read-only via API)
        Route::get('/ropa', [\App\Http\Controllers\Api\V1\RopaApiController::class, 'index'])->name('ropa.index');
        Route::get('/ropa/{id}', [\App\Http\Controllers\Api\V1\RopaApiController::class, 'show'])->name('ropa.show');

        // Vendors (Read-only via API)
        Route::get('/vendors', [\App\Http\Controllers\Api\V1\VendorApiController::class, 'index'])->name('vendors.index');

        // Webhooks
        Route::prefix('webhooks')->name('webhooks.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\WebhookApiController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\V1\WebhookApiController::class, 'store'])->name('store');
            Route::delete('/{id}', [\App\Http\Controllers\Api\V1\WebhookApiController::class, 'destroy'])->name('destroy');
        });

        // Organization Info (Read-only)
        Route::get('/organization', function (\Illuminate\Http\Request $request) {
            $org = $request->user()->organization;
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $org->id,
                    'name' => $org->name,
                    'compliance_score' => $org->getComplianceScore(),
                    'plan' => $org->plan,
                ]
            ]);
        })->name('organization');
    });
});

// API Documentation (Swagger UI)
Route::get('/docs', function () {
    return view('api.docs');
})->name('api.docs');
