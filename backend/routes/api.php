<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\GoogleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');
    Route::post('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset.password');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    //google
    Route::post('/get-google-sign-in-url', [AuthController::class, 'getGoogleSignInUrl']);
    Route::get('/google/call-back', [AuthController::class, 'loginGoogleCallback']);
    //Logged in
    Route::middleware(['jwt'])->group(function () {
        // Routes cho admin
        Route::middleware(['role:admin'])->group(function () {
            // Các route dành cho admin có thể thêm tại đây
        });

        // Routes cho instructor
        Route::middleware(['role:instructor'])->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
        });

        // Routes cho student
        Route::middleware(['role:student'])->group(function () {
            // Các route dành cho student có thể thêm tại đây
        });
    });
});
