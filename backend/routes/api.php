<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CartController;
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
Route::group(['middleware' => 'api', 'prefix' => 'course'], function ($router) {
    Route::get('index', [CourseController::class, 'filterCourses']);
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    //course

    //user
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
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('upload-image', [AuthController::class, 'uploadImage']);
        //admin
        Route::post('admin-update-user', [AuthController::class, 'adminUpdateUser']);
        Route::delete('delete-user/{id}', [AuthController::class, 'deleteUser']);
        Route::post('restore-user/{id}', [AuthController::class, 'restoreUser']);
        Route::post('force-delete-user/{id}', [AuthController::class, 'forceDeleteUser']);

        // Routes cho admin
        Route::middleware(['role:admin'])->group(function () {
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        // Routes cho instructor
        Route::middleware(['role:instructor'])->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
        });

        // Routes cho student
        Route::middleware(['role:student'])->group(function () {
            // Các route dành cho student có thể thêm tại đây
            // Cart
            Route::get('/cart/courses', [CartController::class, 'getCoursesFromCart']);
            Route::post('/cart/courses', [CartController::class, 'addCourseToCart']);
            Route::delete('/cart/courses/{course_id}', [CartController::class, 'removeCourseFromCart']);
            Route::delete('/cart/courses', [CartController::class, 'clearCart']);
        });
    });
});
Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
