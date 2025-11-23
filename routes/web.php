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
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LiveShowController;
use App\Http\Controllers\DocumentMailController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CommunityIndexController;
// use App\Http\Controllers\StripeWebhookController;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Controllers\PremiumChatController;
use App\Http\Controllers\WebsiteVideoController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BookMarkController;
use App\Http\Controllers\MidiFileController;

use Illuminate\Support\Facades\Artisan;


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

// Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);
Route::post(
    'stripe/webhook',
    [WebhookController::class, 'handleWebhook']
)->name('cashier.webhook');

Route::get('/', [IndexController::class, 'index']);
Route::get('/about', function () {
    return view('about', ['pageTitle' => 'About Us']);
});

Route::get('/shop', function () {
    return view('shop', ['pageTitle' => 'About Us']);
});

Route::get('/contact', function () {
    return view('contact', ['pageTitle' => 'Contact']);
});

Route::get('/privacy-policy', function () {
    return view('privacy', ['pageTitle' => 'Privacy Policy']);
});

Route::get('/terms-of-service', function () {
    return view('/terms-of-service', ['pageTitle' => 'Terms of Service']);
});

Route::get('/clear', function () {
    Artisan::call('optimize');
    return 'Application optimized!';
});

Route::post('/notifications/{id}/mark-read', function ($id) {
    $notification = auth()->user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return back();
})->name('notifications.markRead');

Route::post('/send-document', [DocumentMailController::class, 'send'])->name('subscribe');

Route::get('/plans', [SubscriptionController::class, 'index']);
Route::get('/member/plan', [SubscriptionController::class, 'memberplans'])->middleware(['auth', 'verified'])->name('subscription.page');

Auth::routes(
    ['verify' => true]
);

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['check.payment', 'verified']);
Route::get('/admin/dashboard', [HomeController::class, 'admin'])->name('admin')->middleware(['check.payment', 'verified']);

Route::post('/paystack', [PaymentController::class, 'initialize'])->name('paystack.redirect');
Route::get('/paystack/callback', [PaymentController::class, 'handlePaystackCallback'])->name('payment.verify');

Route::post('/stripe/create', [StripeController::class, 'checkout'])->name('stripe.create');
Route::get('/stripe/success', [StripeController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('/stripe/cancel',[StripeController::class, 'checkoutCancel'])->name('checkout.cancel');
Route::post('stripe/sub/cancel',[StripeController::class, 'cancelSubscription'])->name('subscription.cancel');



Route::post('paypal/create-order', [PayPalController::class, 'pay']);
Route::get('paypal/success', [PayPalController::class, 'success']);
Route::get('paypal/cancel', [PayPalController::class, 'error']);
Route::post('/zoom-meeting-booking', [ZoomMeetingController::class, 'createZoomMeeting']);
Route::get('/zoom/authorize', [ZoomMeetingController::class, 'redirectToZoom']);
Route::get('/zoom/callback', [ZoomMeetingController::class, 'handleZoomCallback']);

Route::put('profile/update', [HomeController::class, 'update']);
Route::delete('profile/delete', [HomeController::class, 'destroy']);
Route::post('/support/send', [ContactController::class, 'store'])->name('support.send');
Route::post('/contact/send', [ContactController::class, 'create']);

Route::prefix('member')->middleware(['auth', 'check.payment', 'verified'])->group(function () {
    Route::get('roadmap', [GetstartedController::class, 'roadmap']);
    Route::get('premium-chat', [PremiumChatController::class, 'index']);
    Route::post('getstarted/updated', [GetstartedController::class, 'updateGetStarted']);
    Route::get('getstarted', [GetstartedController::class, 'index']);
    Route::get('piano-exercise', [ExerciseController::class, 'pianoExercise'])->name('piano.exercise');
    Route::get('extra-courses', [CoursesController::class, 'extraCourses'])->name('extra.courses');
    Route::get('lesson/{id}', [CoursesController::class, 'singleCourse']);
    Route::get('ear-training', [EarTrainingController::class, 'earTraining'])->name('ear.training');
    Route::get('ear-training/{id}', [EarTrainingController::class, 'showmember']);
    Route::get('quick-lessons', [LessonController::class, 'quicklession'])->name('quick.lession');
    Route::get('learn-songs', [LessonController::class, 'learnSongs'])->name('learn.songs');
    Route::get('live-session', [LiveSessionController::class, 'liveSession']);
    Route::get('course/{level}', [CourseController::class, 'membershow']);
    Route::post('/course/{course}/complete', [CourseProgressController::class, 'store']);
});

Route::prefix('member')->middleware(['auth', 'verified'])->group(function () {
    Route::get('profile', [HomeController::class, 'profile']);
    Route::get('/shop', [ShopController::class, 'index']);
    Route::get('support', [HomeController::class, 'support']);
    Route::get('/community', [CommunityIndexController::class, 'index'])->name('community.index');
    Route::get('/community/members', [CommunityIndexController::class, 'members'])->name('community.members');
    Route::get('/post/{post}', [CommunityIndexController::class, 'singlePost'])->name('singlePost');
    Route::get('/community/space/pdf-downloads', [CommunityIndexController::class, 'pdfDownloads'])->name('community.pdf-downloads');
    Route::get('/community/space/audio-downloads', [CommunityIndexController::class, 'audioDownloads'])->name('community.audio-downloads');
    Route::get('/community/space/midi-downloads', [CommunityIndexController::class, 'midiDownloads'])->name('community.midi-downloads');
    Route::get('/community/space/{subcategory}', [CommunityIndexController::class, 'subcategory'])->name('community.subcategory');
    Route::get('/community/space', [CommunityIndexController::class, 'space'])->name('community.space');
    Route::get('/community/single/{single}', [CommunityIndexController::class, 'single'])->name('community.single');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
    ->name('notifications.markAllAsRead');
    Route::get('/community/user/{community}', [CommunityController::class, 'show']);
    Route::get('/community/u/{community}/update', [CommunityController::class, 'edit']);
    Route::get('bookmark', [BookMarkController::class, 'bookmark']);
    Route::post('/bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('users', [AdminController::class, 'users']);
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('ear-training', [EarTrainingController::class, 'index']);
    Route::get('ear-training/list', [EarTrainingController::class, 'list']);
    Route::get('ear-training/create', [EarTrainingController::class, 'create']);
    Route::get('ear-training/{id}', [EarTrainingController::class, 'show']);
    Route::post('/ear-training', [EarTrainingController::class, 'store']);
    Route::get('ear-training/show', [EarTrainingController::class, 'showadmin']);
    Route::post('ear-training/update/{quiz}', [EarTrainingController::class, 'update']);
    Route::delete('ear-training/delete/{quiz}', [EarTrainingController::class, 'destroy']);
    Route::delete('ear-training/question/{question}', [EarTrainingController::class, 'deleteQuestion']);
    Route::post('/ear-training/{quiz}/questions', [EarTrainingController::class, 'storeQuestions']);
    Route::get('course/create', [CourseController::class, 'create']);
    Route::post('course', [CourseController::class, 'store']);
    Route::get('uploads/piano-exercise', [UploadController::class, 'pianoExercise']);
    Route::get('uploads/extra-courses', [UploadController::class, 'extraCourses']);
    Route::get('uploads/quick-lessons', [UploadController::class, 'quickLessons']);
    Route::get('uploads/learn-songs', [UploadController::class, 'learnSongs']);
    Route::get('uploads/create', [UploadController::class, 'create']);
    Route::post('upload/store', [UploadController::class, 'store']);
    Route::delete('upload/{upload}', [UploadController::class, 'destroy']);
    Route::get('uploads/{id}/edit', [UploadController::class, 'edit']); 
    Route::get('upload-list', [UploadController::class, 'uploadList'])->name('upload.list');
    Route::get('live-shows', [LiveShowController::class, 'index']);
    Route::get('live-show/create', [LiveShowController::class, 'create']);
    Route::post('live-shows', [LiveShowController::class, 'store']);
    Route::get('website-video', [WebsiteVideoController::class, 'index']);
    Route::post('website-video/{video?}', [WebsiteVideoController::class, 'saveVideo']);
    Route::post('website-video/delete/{video}', [WebsiteVideoController::class, 'destroy']);
    Route::get('midi-file', [MidiFileController::class, 'index']);
    
});
