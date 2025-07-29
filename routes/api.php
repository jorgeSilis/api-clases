<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Routes Users

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::put('/users/{id}', [UserController::class, 'update']);


//Routes modules

Route::get('/modules', [ModuleController::class, 'index']);
Route::post('/modules', [ModuleController::class, 'store']);
Route::get('/modules/{id}', [ModuleController::class, 'show']);
Route::put('/modules/{id}', [ModuleController::class, 'update']);
Route::delete('/modules/{id}', [ModuleController::class, 'destroy']);

// Routes modules-user(student)
Route::get('/modules/{id}/students', [UserModuleController::class, 'index']);
Route::post('/modules/{id}/students', [UserModuleController::class, 'store']);
Route::delete('/modules/{id}/students/{student_id}', [UserModuleController::class, 'destroy']);

//Routes sessions 
Route::get('/sessions', [SessionController::class, 'index']);
Route::get('/modules/{id}/sessions', [SessionController::class, 'getByModule']);
Route::get('/sessions/{id}', [SessionController::class, 'show']);
Route::post('/modules/{id}/sessions', [SessionController::class, 'store']);
Route::put('/sessions/{id}', [SessionController::class, 'update']);
Route::delete('/sessions/{id}', [SessionController::class, 'destroy']);

// Routes attendances

Route::get('/attendances', [AttendanceController::class, 'index']);
Route::post('/sessions/{id}/attendances', [AttendanceController::class, 'store']);
Route::get('/sessions/{id}/attendances', [AttendanceController::class, 'getBySession']);
Route::get('/students/{id}/modules/attendances-summary', [AttendanceController::class, 'getSummaryByStudent']); //module also


