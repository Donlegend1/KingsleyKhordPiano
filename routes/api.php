<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseProgressController;
use App\Http\Controllers\LiveShowController;
use App\Http\Controllers\CourseVideoCommentsController;
use App\Http\Controllers\CourseVideoCommentRepliesController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\PostReplyController;
use App\Http\Controllers\UserController;


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

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/live-shows', [LiveShowController::class, 'list']);


    Route::prefix('admin')->group(function () {
        Route::get('users', [AdminController::class, 'usersList']);
        Route::put('users/{user}', [AdminController::class, 'editUser']);
        Route::delete('user/{user}', [AdminController::class, 'destroy']);
        Route::get('courses', [CourseController::class, 'coursesList']);
        Route::patch('courses/{course}', [CourseController::class, 'update']);
        Route::post('upload/{upload}', [UploadController::class, 'update']);
        Route::delete('courses/{course}', [CourseController::class, 'deleteCourse']);
        Route::post('course/store', [CourseController::class, 'store']);
        Route::patch('/live-shows/{liveshow}', [LiveShowController::class, 'update']);
        Route::delete('/live-show/{liveshow}/delete', [LiveShowController::class, 'destroy']);

    });

    Route::prefix('member')->middleware(['web', 'auth'])->group(function () {
        Route::get('courses', [CourseController::class, 'index']);
        Route::get('user/{community}', [UserController::class, 'getSingleUser']);
        Route::post('user/{community}/update', [UserController::class, 'updateUserCommunity']);
        Route::post('passport', [UserController::class, 'updateUserPassport']);
        Route::get('courses/{level}', [CourseController::class, 'membershowAPI']);  
        Route::get('course/{course}', [CourseController::class, 'show']);
        Route::get('course/{course}/lessons', [CourseController::class, 'lessons']);
        Route::get('course/{course}/lesson/{lesson}', [CourseController::class, 'lesson']);
        Route::get('course/{course}/exercise', [CourseController::class, 'exercise']);
        Route::post('course/{course}/exercise/submit', [CourseController::class, 'submitExercise']);
        Route::post('course/{course}/video-comment', [CourseVideoCommentsController::class, 'store']);
        Route::get('comments/course', [CourseVideoCommentsController::class, 'index']);
        Route::put('comment/{course_video_comments}', [CourseVideoCommentsController::class, 'update']);
        Route::delete('comment/{course_video_comments}', [CourseVideoCommentsController::class, 'destroy']);

        Route::post('comment/{comment}/reply', [CourseVideoCommentRepliesController::class, 'store']);
         
        Route::post('/post', [PostController::class, 'store'])->name('post.store');
        Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.delete');
        Route::get('/posts', [PostController::class, 'index'])->name('post.index');
        Route::get('/posts/member/{community}', [PostController::class, 'postByUser'])->name('post.user');

        Route::post('/comment', [PostCommentController::class, 'store'])->name('comment.store');
        Route::post('/like', [PostLikeController::class, 'store'])->name('like.toggle');
        Route::post('/community/{user}/status', [AdminController::class, 'updateUserStatus']);

        Route::post('/comment/reply/{postComment}', [PostReplyController::class, 'store'])->name('reply.post');

        
    });


    Route::get('exercise/{exercise}', [ExerciseController::class, 'show']);
    Route::post('exercise/{exercise}/submit', [ExerciseController::class, 'submit']);
    Route::get('ear-training', [EarTrainingController::class, 'index']);
    Route::get('ear-training/{level}', [EarTrainingController::class, 'show']);
    Route::get('live-sessions', [LiveSessionController::class, 'index']);
    Route::get('live-sessions/{session}', [LiveSessionController::class, 'show']);
    Route::get('course/{level}', [CourseController::class, 'membershow']);
   