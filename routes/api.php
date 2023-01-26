<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//tasks
Route::get('tasks', [TaskController::class, 'index']);
Route::post('tasks', [TaskController::class, 'store']);
Route::put('tasks/{id}', [TaskController::class, 'update']);
Route::delete('tasks/{id}', [TaskController::class, 'destroy']);

//Tasks List
Route::get('task-lists', [TaskListController::class, 'index']);
Route::post('task-lists', [TaskListController::class, 'store']);
Route::get('task-lists/{id}/tasks', [TaskListController::class, 'show']);
Route::delete('task-lists/{id}', [TaskListController::class, 'destroy']);

//Users
Route::get('users', [AuthController::class, 'index']);
Route::put('users/{id}', [AuthController::class, 'update']);

Route::put('update-profile/{id}', [AuthController::class, 'updateProfile']);

//auth
Route::post('login', [AuthController::class, 'login']);