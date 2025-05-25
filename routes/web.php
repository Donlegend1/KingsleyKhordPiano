<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\GetstartedController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\EarTrainingController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseProgressController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\UploadController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});


Route::get('/plans', [SubscriptionController::class, 'index']);
Route::get('/member/plan', [SubscriptionController::class, 'memberplans'])->middleware('auth');

Auth::routes(
    ['verify' => true]
);


Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('check.payment');

Route::post('/paystack', [PaymentController::class, 'initialize'])->name('paystack.redirect');
Route::get('/paystack/callback', [PaymentController::class, 'handlePaystackCallback'])->name('payment.verify');

Route::post('/stripe/create', [StripeController::class, 'createPaymentIntent'])->name('stripe.create');
Route::get('/stripe/success', [StripeController::class, 'retrievePaymentIntent'])->name('stripe.verify');
Route::get('/stripe/cancel', [StripeController::class, 'paymentCanceled'])->name('stripe.cancel');

Route::post('paypal/create-order', [PayPalController::class, 'pay']);
Route::get('paypal/success', [PayPalController::class, 'success']);
Route::get('paypal/cancel', [PayPalController::class, 'error']);
Route::post('/zoom-meeting-booking', [ZoomMeetingController::class, 'createZoomMeeting']);
Route::get('/zoom/authorize', [ZoomMeetingController::class, 'redirectToZoom']);
Route::get('/zoom/callback', [ZoomMeetingController::class, 'handleZoomCallback']);

Route::prefix('member')->middleware(['auth', 'check.payment'])->group(function () {
    Route::get('roadmap', [GetstartedController::class, 'roadmap']);
    Route::post('getstarted/updated', [GetstartedController::class, 'updateGetStarted']);
    Route::get('getstarted', [GetstartedController::class, 'index']);
    Route::get('profile', [HomeController::class, 'profile']);
    Route::get('piano-exercise', [ExerciseController::class, 'pianoExercise']);
    Route::get('extra-courses', [CoursesController::class, 'extraCourses']);
    Route::get('extra-courses/{id}', [CoursesController::class, 'singleCourse']);
    Route::get('ear-training', [EarTrainingController::class, 'earTraining']);
    Route::get('quick-lessons', [LessonController::class, 'quicklession']);
    Route::get('learn-songs', [LessonController::class, 'learnSongs']);
    Route::get('live-session', [LiveSessionController::class, 'liveSession']);
    Route::get('course/{level}', [CourseController::class, 'membershow']);
    Route::post('/course/{course}/complete', [CourseProgressController::class, 'store']);
    
});


Route::prefix('admin')->group(function () {
    Route::get('users', [AdminController::class, 'users']);
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('course/create', [CourseController::class, 'create']);
    Route::post('course', [CourseController::class, 'store']);
    Route::get('uploads/list', [UploadController::class, 'index']);
    Route::get('uploads/create', [UploadController::class, 'create']);
    Route::post('upload/store', [UploadController::class, 'store']);
    Route::get('uploads/{id}/edit', [UploadController::class, 'edit']); 
    Route::get('upload-list', [UploadController::class, 'uploadList'])->name('upload.list');

});