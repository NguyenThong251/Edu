<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseLevelController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\Api\GoogleController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\QuizController;

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
    //callback connect stripe
    Route::get('/payment-methods/stripe/callback', [PaymentMethodController::class, 'handleStripeCallback'])->name('payment_methods.stripe.callback');
    Route::get('/payment-methods/stripe/test', [PaymentMethodController::class, 'test'])->name('payment_methods.stripe.callback');


    //Logged in
    Route::middleware(['jwt'])->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('upload-image', [AuthController::class, 'uploadImage']);
        //wishlist
        Route::post('wishlist', [ManageController::class, 'addToWishlist']);
        Route::get('wishlist', [ManageController::class, 'getWishlist']);
        Route::post('delete-wishlist', [ManageController::class, 'deletWishlist']);

        // Routes cho admin
        Route::middleware(['role:admin'])->group(function () {
            // Quản lý, ManageController
            Route::get('courses', [CourseController::class, 'getListAdmin'])->name('courses.getListAdmin');


            Route::get('categories', [CategoryController::class, 'getListAdmin'])->name('categories.getListAdmin');
            Route::get('getAdmin', [ManageController::class, 'getAdmin'])->name('users.admins');
            Route::get('getInstructor', [ManageController::class, 'getInstructor'])->name('users.instructors');
            Route::get('getStudent', [ManageController::class, 'getStudent'])->name('users.students');
            Route::put('updateUser/{id}', [ManageController::class, 'updateUserAccount']);
            Route::put('updateFoundation/{id}', [ManageController::class, 'updateFoundationAccount']);
            Route::put('contact-info/{id}', [ManageController::class, 'updateContactInfo']);
            Route::delete('delUserAdmin/{id}', [ManageController::class, 'delUser']);
            Route::get('getAdminRp', [ManageController::class, 'getAdminRpPayment']);
            Route::get('getInstructorRp', [ManageController::class, 'getInstructorRp']);
            Route::delete('delReport/{id}', [ManageController::class, 'deleteReportPayment']);
            Route::get('order-history', [ManageController::class, 'getOrderHistory']);
            Route::get('order-detail/{orderId}', [ManageController::class, 'getOrderDetail']);

            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
            Route::post('categories/{id}/children', [CategoryController::class, 'addChildren'])->name('categories.addChildren');
            Route::patch('categories/{id}/status', [CategoryController::class, 'updateStatus'])->name('categories.updateStatus');
            Route::get('categories/restore/{id}', [CategoryController::class, 'restore'])->name('categories.restore');
            Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

            Route::get('list-course-levels-admin', [CourseLevelController::class, 'getListAdmin'])->name('courselevels.getListAdmin');
            Route::post('course-levels', [CourseLevelController::class, 'store'])->name('courselevels.store');
            Route::put('course-levels/{id}', [CourseLevelController::class, 'update'])->name('courselevels.update');
            Route::get('course-levels', [CourseLevelController::class, 'index'])->name('courselevels.index');
            Route::get('course-levels/restore/{id}', [CourseLevelController::class, 'restore'])->name('courselevels.restore');
            Route::delete('course-levels/{id}', [CourseLevelController::class, 'destroy'])->name('courselevels.destroy');

            Route::get('list-languages-admin', [LanguageController::class, 'getListAdmin'])->name('languages.getListAdmin');
            Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
            Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
            Route::put('languages/{id}', [LanguageController::class, 'update'])->name('languages.update');
            Route::get('languages/restore/{id}', [LanguageController::class, 'restore'])->name('languages.restore');
            Route::delete('languages/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');

            // Voucher
            Route::prefix('vouchers')->group(function () {
                // Get all vouchers
                Route::get('/', [VoucherController::class, 'index']);
                Route::get('list-vouchers-admin', [VoucherController::class, 'getListAdmin']);
                // Get voucher by id or code
                Route::get('/{idOrCode}', [VoucherController::class, 'show']);
                Route::post('/create', [VoucherController::class, 'store']);
                Route::put('/{id}', [VoucherController::class, 'update']);
                Route::post('/delete', [VoucherController::class, 'destroy']);
                Route::get('/filter', [VoucherController::class, 'filter']);
                // Get all deleted vouchers
                Route::get('/deleted', [VoucherController::class, 'getDeletedVouchers']);
                Route::post('/restore', [VoucherController::class, 'restoreVoucher']);
            });

            // Review in Admin
            // Xem tất cả các review (bao gồm cả bị xóa)
            Route::get('reviews/{courseId}/deleted', [ReviewController::class, 'getDeletedReviews']);
            Route::post('reviews/{id}/restore', [ReviewController::class, 'restore']);

            // admin Xử lý rút tiền stripe
            Route::post('/payout/process/{id}', [PayoutController::class, 'processPayout']);
            // Liệt kê các yêu cầu rút tiền
            Route::get('/payout/requests', [PayoutController::class, 'listPayoutRequests']);
//            Route::get('/payout/success/{id}', [PayoutController::class, 'payoutSuccess'])->name('payout.success');
//            Route::get('/payout/cancel/{id}', [PayoutController::class, 'payoutCancel'])->name('payout.cancel');
            Route::post('/payout/rejected/{id}', [PayoutController::class, 'rejectRequest']);


            //get all user
            Route::get('/get-all-user', [AuthController::class, 'getAllUser']);
            Route::post('/create-user', [AuthController::class, 'adminCreateUser']);
            Route::get('/get-detail-user/{id}', [AuthController::class, 'getDetailUser']);
            Route::post('admin-update-user', [AuthController::class, 'adminUpdateUser']);
            Route::delete('delete-user/{id}', [AuthController::class, 'deleteUser']);
            Route::post('restore-user/{id}', [AuthController::class, 'restoreUser']);
            Route::post('force-delete-user/{id}', [AuthController::class, 'forceDeleteUser']);
            Route::post('block/unblock-user/{id}', [AuthController::class, 'blockOrUnlockUser']);

            //admin report
            Route::get('admin/', [AdminController::class, 'index']);
            Route::get('admin/get-report', [AdminController::class, 'getAdminReport']);
            Route::get('admin/get-line-chart/revenue', [AdminController::class, 'getAdminLineChartData']);
            Route::get('admin/get-line-chart/user', [AdminController::class, 'getUserRegistrationLineChart']);
            Route::get('admin/get-line-chart/order', [AdminController::class, 'getOrderLineChartData']);
        });

        // Routes cho instructor
        Route::middleware(['role:instructor'])->group(function () {
            Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
            Route::get('courses/{id}', [CourseController::class, 'showOne'])->name('courses.showOne');
            Route::put('courses/{id}', [CourseController::class, 'update'])->name('courses.update');
            Route::get('courses/restore/{id}', [CourseController::class, 'restore'])->name('courses.restore');
            Route::delete('courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');

            // // Route Chương
            Route::controller(SectionController::class)->group(function () {
                Route::get('section/{id}', 'showOne')->name('courses.showOne');
                Route::post('section', 'store')->name('section.store');
                Route::post('section/{id}', 'update')->name('section.update');
                Route::delete('section/{id}', 'delete')->name('section.delete');
                Route::post('section/sort', 'sort')->name('section.sort');
            });

            // // Lesson route
            // Route::controller(LectureController::class)->group(function () {
            //     Route::post('lesson/store', 'store')->name('lesson.store');
            //     Route::post('lesson/update', 'update')->name('lesson.update');
            //     Route::get('lesson/delete/{id}', 'delete')->name('lesson.delete');
            //     Route::post('lesson/sort', 'sort')->name('lesson.sort');
            // });
            
            Route::put('sort-content-of-section', [LectureController::class, 'updateOrder'])->name('lectures.updateOrder');
            Route::post('lectures', [LectureController::class, 'store'])->name('lectures.store');
            Route::get('lectures/{id}', [LectureController::class, 'show'])->name('lectures.show');
            Route::post('lectures/{id}', [LectureController::class, 'update'])->name('lectures.update');
            Route::get('lectures/restore/{id}', [LectureController::class, 'restore'])->name('lectures.restore');
            Route::delete('lectures/{id}', [LectureController::class, 'delete'])->name('lectures.delete');
            Route::patch('lectures/{id}/status', [LectureController::class, 'updateLectureStatus'])->name('lectures.updateStatus');
            Route::patch('lectures/{id}/section', [LectureController::class, 'updateLectureSection'])->name('lectures.updateSection');
            Route::get('show-content-of-section/{id}', [LectureController::class, 'showContentBySection'])->name('lectures.showContentBySection');
            Route::get('lectures', [LectureController::class, 'getListAdmin'])->name('lectures.getListAdmin');
            Route::get('lectures/edit-form/{id}', [LectureController::class, 'editForm'])->name('lectures.editForm');


            Route::post('quizzes', [QuizController::class, 'store'])->name('quizzes.store');
            Route::get('quizzes/{id}', [QuizController::class, 'showOne'])->name('quizzes.showOne');
            Route::post('quizzes/{id}', [QuizController::class, 'update'])->name('quizzes.update');
            Route::get('quizzes/restore/{id}', [QuizController::class, 'restore'])->name('quizzes.restore');
            Route::delete('quizzes/{id}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
            Route::delete('quizzes/permanent-delete/{id}', [QuizController::class, 'forceDelete'])->name('quizzes.destroy');
            Route::patch('quizzes/{id}/status', [QuizController::class, 'updateQuizStatus'])->name('quizzes.updateStatus');
            Route::patch('quizzes/{id}/section', [QuizController::class, 'updateQuizSection'])->name('quizzes.updateSection');
            Route::get('quizzes', [QuizController::class, 'getListAdmin'])->name('quizzes.getListAdmin');
            Route::get('quizzes/edit-form/{id}', [QuizController::class, 'editForm'])->name('quizzes.editForm');

            Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
            Route::get('questions/{id}', [QuestionController::class, 'show'])->name('questions.show');
            Route::put('questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
            Route::get('questions/restore/{id}', [QuestionController::class, 'restore'])->name('questions.restore');
            Route::delete('questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');
            Route::delete('questions/permanent-delete/{id}', [QuestionController::class, 'forceDelete'])->name('questions.destroy');
            Route::patch('questions/{id}/status', [QuestionController::class, 'updateQuestionAttributes'])->name('questions.updateStatus');
            Route::patch('questions/{id}/quiz', [QuestionController::class, 'updateQuestionAttributes'])->name('questions.updateQuestion');
            Route::get('questions', [QuestionController::class, 'getListAdmin'])->name('questions.getListAdmin');
            Route::get('questions/edit-form/{id}', [QuestionController::class, 'editForm'])->name('questions.editForm');
            Route::get('show-question-of-quiz/{id}', [QuestionController::class, 'showQuestionsByQuiz'])->name('questions.showQuestionOfQuiz');
            Route::put('sort-question-of-quiz', [QuestionController::class, 'updateQuestionOrder'])->name('questions.updateOrder');



            Route::get('instructor/course', [InstructorController::class, 'getListCourses']);
            Route::get('instructor/report', [InstructorController::class, 'getReport']);
            Route::get('instructor/line-chart', [InstructorController::class, 'getLineChartData']);
            Route::get('instructor/course-statistics', [InstructorController::class, 'getCourseStatistics']);

            // Liên kết Stripe
            Route::get('/payout/', [PayoutController::class, 'index']);
            Route::get('/payout/report-payment', [PayoutController::class, 'report']);


            Route::get('/payment-methods/stripe/link', [PaymentMethodController::class, 'linkStripe'])->name('payment_methods.stripe.link');
            // Liệt kê các phương thức thanh toán
            Route::get('/payment-methods', [PaymentMethodController::class, 'listPaymentMethods']);
            // Thêm phương thức thanh toán khác
            Route::post('/add-payment-methods', [PaymentMethodController::class, 'addPaymentMethod']);
            // Xóa phương thức thanh toán
            Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'deletePaymentMethod']);
            // Yêu cầu rút tiền
            Route::post('/payout/request', [PayoutController::class, 'requestPayout']);
        });

        // Routes cho student
        Route::middleware(['role:student'])->group(function () {
            //chat message
            Route::get('/get-user-id/{id}', [ChatController::class, 'getUserId']);
            Route::get('/message/private/{receiverId}', [ChatController::class, 'index']);
            Route::get('/chat/users', [ChatController::class, 'getUsers']);
            Route::post('/messages/{receiverId}', [ChatController::class, 'store']);
            Route::post('/upload-chat-image', [ChatController::class, 'uploadChatImage']);
            Route::delete('/delete-chat-image', [ChatController::class, 'deleteChatImage']);

            Route::get('/search-content', [StudyController::class, 'searchContent']);
            Route::get('/study-course', [StudyController::class, 'studyCourse']);
            Route::get('/change-content', [StudyController::class, 'changeContent']);
            Route::get('/get-user-courses', [StudyController::class, 'getUserCourses']);

            //notes course
            Route::get('/notes/course/{course_id}', [NoteController::class, 'index']); // Lấy ghi chú cho một khóa học cụ thể
            Route::get('/notes/{id}', [NoteController::class, 'show']); // Lấy một ghi chú cụ thể
            Route::post('/notes', [NoteController::class, 'store']); // Tạo mới ghi chú
            Route::post('/notes/update/{id}', [NoteController::class, 'update']); // Cập nhật ghi chú
            Route::post('/notes/delete/{id}', [NoteController::class, 'destroy']); // Xóa ghi chú

            // Cart
            Route::prefix('cart')->group(function () {
                Route::get('/', [CartController::class, 'index']);
                Route::post('/', [CartController::class, 'store']);
                Route::delete('/all', [CartController::class, 'destroyAll']);
                Route::delete('/{course_id}', [CartController::class, 'destroy']);
                Route::post('apply-voucher', [CartController::class, 'applyVoucher']);
            });

            // Order
            Route::prefix('orders')->group(function () {
                Route::get('/', [OrderController::class, 'index']);
                Route::post('/', [OrderController::class, 'store']);
                Route::get('/{id}', [OrderController::class, 'show']);
                Route::patch('/{id}', [OrderController::class, 'cancel']);
                Route::patch('/{id}/restore', [OrderController::class, 'restore']);
            });

            // Review
            Route::post('reviews', [ReviewController::class, 'store']);
            Route::put('reviews/{id}', [ReviewController::class, 'update']);
            Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
        });
    });
});

// All Review
Route::get('courses/{courseId}/reviews', [ReviewController::class, 'index']);
Route::get('courses/{courseId}/reviews/filter', [ReviewController::class, 'filter']);

// Order webhook
Route::get('/orders/verify-payment', [OrderController::class, 'verifyPayment']);
Route::post('/stripe/webhook', [OrderController::class, 'handleWebhook']);

Route::post('/payout/stripe/webhook', [PayoutController::class, 'handleWebhook']);

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
