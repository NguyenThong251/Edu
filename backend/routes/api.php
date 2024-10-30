<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CourseLevelController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Api\GoogleController;
use App\Http\Controllers\VoucherController;

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
    Route::get('check-token-reset-password/{token}', [AuthController::class, 'checkTokenResetPassword']);

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
            Route::get('categories/restore/{id}', [CategoryController::class, 'restore'])->name('categories.restore');
            Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

            Route::post('course-levels', [CourseLevelController::class, 'store'])->name('courselevels.store');
            Route::put('course-levels/{id}', [CourseLevelController::class, 'update'])->name('courselevels.update');
            Route::get('course-levels/restore/{id}', [CourseLevelController::class, 'restore'])->name('courselevels.restore');
            Route::delete('course-levels/{id}', [CourseLevelController::class, 'destroy'])->name('courselevels.destroy');

            Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
            Route::put('languages/{id}', [LanguageController::class, 'update'])->name('languages.update');
            Route::get('languages/restore/{id}', [LanguageController::class, 'restore'])->name('languages.restore');
            Route::delete('languages/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');

            // Voucher
            Route::prefix('vouchers')->group(function () {
                Route::post('/create', [VoucherController::class, 'create']);
                Route::post('/validate', [VoucherController::class, 'validateVoucher']);
                Route::post('/apply', [VoucherController::class, 'applyVoucher']);
                Route::post('/cancel', [VoucherController::class, 'cancelVoucher']);
            });
        });

        // Routes cho instructor
        Route::middleware(['role:instructor'])->group(function () {
            Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
            Route::post('courses/{id}', [CourseController::class, 'update'])->name('courses.update');
            Route::get('courses/restore/{id}', [CourseController::class, 'restore'])->name('courses.restore');
            Route::delete('courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
        });

        // Routes cho student
        Route::middleware(['role:student'])->group(function () {
            // Các route dành cho student có thể thêm tại đây

            // ...

            // Cart
            Route::prefix('cart')->group(function () {
                Route::get('/', [CartController::class, 'index']);
                Route::post('/', [CartController::class, 'store']);
                Route::delete('/all', [CartController::class, 'destroyAll']);
                Route::delete('/{course_id}', [CartController::class, 'destroy']);
            });

            // Order
            Route::prefix('orders')->group(function () {
                Route::get('/', [OrderController::class, 'index']);
                Route::post('/', [OrderController::class, 'store']);
                Route::get('/{id}', [OrderController::class, 'show']);
                Route::patch('/{id}', [OrderController::class, 'cancel']);
                Route::patch('/{id}/restore', [OrderController::class, 'restore']);
            });
        });
    });
});
// Order webhook
Route::get('/orders/verify-payment', [OrderController::class, 'verifyPayment']);
Route::post('/stripe/webhook', [OrderController::class, 'handleWebhook']);

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('course-levels', [CourseLevelController::class, 'index'])->name('courselevels.index');
Route::get('course-levels/{id}', [CourseLevelController::class, 'show'])->name('courselevels.show');

Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
Route::get('languages/{id}', [LanguageController::class, 'show'])->name('languages.show');

Route::get('courses', [CourseController::class, 'search'])->name('courses.search');
Route::get('courses/{id}', [CourseController::class, 'detail'])->name('courses.detail');
Route::get('get-popular-courses', [CourseController::class, 'getPopularCourses'])->name('courses.getPopularCourses');
Route::get('get-new-courses', [CourseController::class, 'getNewCourses'])->name('courses.getNewCourses');
Route::get('get-top-rated-courses', [CourseController::class, 'getTopRatedCourses'])->name('courses.getTopRatedCourses');
Route::get('get-favourite-courses', [CourseController::class, 'getFavouriteCourses'])->name('courses.getFavouriteCourses');
