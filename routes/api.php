<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseProgressController;
use App\Http\Controllers\LiveShowController;

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
        Route::delete('courses/{course}', [CourseController::class, 'deleteCourse']);
        Route::post('course/store', [CourseController::class, 'store']);
        Route::patch('/live-shows/{liveshow}', [LiveShowController::class, 'update']);
        Route::delete('/live-show/{liveshow}/delete', [LiveShowController::class, 'destroy']);

    });

    Route::prefix('member')->group(function () {
        Route::get('courses', [CourseController::class, 'index']);

        Route::get('courses/{level}', [CourseController::class, 'membershowAPI']);  
        Route::get('course/{course}', [CourseController::class, 'show']);
        Route::get('course/{course}/lessons', [CourseController::class, 'lessons']);
        Route::get('course/{course}/lesson/{lesson}', [CourseController::class, 'lesson']);
        Route::get('course/{course}/exercise', [CourseController::class, 'exercise']);
        Route::post('course/{course}/exercise/submit', [CourseController::class, 'submitExercise']);
    });

    Route::get('exercise/{exercise}', [ExerciseController::class, 'show']);
    Route::post('exercise/{exercise}/submit', [ExerciseController::class, 'submit']);
    Route::get('ear-training', [EarTrainingController::class, 'index']);
    Route::get('ear-training/{level}', [EarTrainingController::class, 'show']);
    Route::get('live-sessions', [LiveSessionController::class, 'index']);
    Route::get('live-sessions/{session}', [LiveSessionController::class, 'show']);
    Route::get('course/{level}', [CourseController::class, 'membershow']);