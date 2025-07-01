<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\CompletionController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CourseMemberController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']); 
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{course}', [CourseController::class, 'show']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);

    Route::post('/courses/{course}/enroll-students', [CourseController::class, 'batchEnrollStudents']);

    Route::prefix('courses/{course}/members')->group(function () {
        Route::get('/', [CourseMemberController::class, 'index']);
        Route::put('/{member}', [CourseMemberController::class, 'update']);
        Route::delete('/{member}', [CourseMemberController::class, 'destroy']);
    });

    Route::prefix('courses/{course}/contents')->group(function () {
        Route::post('/', [CourseContentController::class, 'store']);
        Route::put('/{content}', [CourseContentController::class, 'update']);
        Route::delete('/{content}', [CourseContentController::class, 'destroy']);
    });
    Route::get('/contents/{content}', [CourseContentController::class, 'show']);
    Route::post('/contents/{content}/comments', [CourseContentController::class, 'addComment']);

    Route::post('/comments/{comment}/moderate', [CommentController::class, 'moderate']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::prefix('courses/{course}/announcements')->group(function () {
        Route::post('/', [AnnouncementController::class, 'store']);
        Route::get('/', [AnnouncementController::class, 'index']);
        Route::put('/{announcement}', [AnnouncementController::class, 'update']);
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy']);
    });

    Route::post('/contents/{content}/complete', [CompletionController::class, 'store']);
    Route::get('/members/{member}/completions', [CompletionController::class, 'index']);
    Route::delete('/completions/{completion}', [CompletionController::class, 'destroy']);

    Route::post('/contents/{content}/bookmark', [BookmarkController::class, 'store']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy']);

});


