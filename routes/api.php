<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\FlashcardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Exercises
    Route::apiResource('exercises', ExerciseController::class);
    Route::post('/exercises/{exercise}/attempt', [ExerciseController::class, 'attempt']);
    
    // Flashcards
    Route::apiResource('flashcards', FlashcardController::class);
    Route::post('/flashcards/{flashcard}/review', [FlashcardController::class, 'review']);
    Route::get('/flashcards/due/today', [FlashcardController::class, 'dueToday']);
    
    // Calendar Events
    Route::apiResource('calendar-events', 'Api\CalendarEventController');
    
    // Mind Maps
    Route::apiResource('mind-maps', 'Api\MindMapController');
    
    // Groups
    Route::apiResource('groups', 'Api\GroupController');
    Route::post('/groups/{group}/join', 'Api\GroupController@join');
    Route::post('/groups/{group}/leave', 'Api\GroupController@leave');
    
    // Gamification
    Route::get('/gamification/badges', 'Api\GamificationController@badges');
    Route::get('/gamification/leaderboard', 'Api\GamificationController@leaderboard');
    Route::get('/gamification/my-progress', 'Api\GamificationController@myProgress');
});
