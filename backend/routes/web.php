<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\LectureController;

Route::get('upload', [LectureController::class, 'showUploadForm'])->name('upload.form');
Route::post('upload-to-s3', [LectureController::class, 'uploadToS3'])->name('upload.to.s3');
Route::get('/', function () {
    return view('emails.welcome');
});
Route::post('login', [AuthController::class, 'login']);
