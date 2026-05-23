<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [
    AuthController::class,
    'register'
]);

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::post('/logout', [
    AuthController::class,
    'logout'
]);

Route::middleware('auth:sanctum')->group(function () {

    // me api
    Route::post('/me', [
        AuthController::class,
        'me'
    ]);


    Route::get('/dashboard/stats', [
        MeetingController::class,
        'dashboardStats'
    ]);



    Route::post('/generate/mom', [
        MeetingController::class,
        'store'
    ])->name('generate.mom');

    Route::get('/meetings', [
        MeetingController::class,
        'index'
    ]);

    Route::get('/meetings/{meeting}', [
        MeetingController::class,
        'show'
    ]);

    Route::get('/my-tasks', [
        TaskController::class,
        'myTasks'
    ]);

    Route::get('/my-meeting-tasks', [
        TaskController::class,
        'myMeetingTasks'
    ]);

    Route::get('/tasks/stats', [
        TaskController::class,
        'stats'
    ]);

    Route::get('/tasks/{task}', [
        TaskController::class,
        'show'
    ]);

    Route::patch('/tasks/{task}/status', [
        TaskController::class,
        'updateStatus'
    ]);


    Route::post('/profile/update', [
        ProfileController::class,
        'update'
    ]);

    Route::post('/regenerate/mom/{meeting}', [
        MeetingController::class,
        'regenerate'
    ]);
    Route::post('/approve/mom/{meeting}', [
        MeetingController::class,
        'approve'
    ]);
});
