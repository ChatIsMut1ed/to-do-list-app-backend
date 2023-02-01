<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//auth
Route::post('api/login', [AuthController::class, 'login']);
Route::post('api/sign-up', [AuthController::class, 'signUp']);

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'api'], function () {

    //tasks
    Route::get('tasks', [TaskController::class, 'index']);
    Route::post('tasks', [TaskController::class, 'store']);
    Route::put('tasks/{id}', [TaskController::class, 'update']);
    Route::delete('tasks/{id}', [TaskController::class, 'destroy']);

    //Tasks List
    Route::get('task-lists', [TaskListController::class, 'index']);
    Route::get('task-lists/{id}', [TaskListController::class, 'getListById']);
    Route::post('task-lists', [TaskListController::class, 'store']);
    Route::get('task-lists/{id}/tasks', [TaskListController::class, 'show']);
    Route::delete('task-lists/{id}', [TaskListController::class, 'destroy']);

    //Users
    Route::get('users', [AuthController::class, 'index']);
    Route::put('users/{id}', [AuthController::class, 'update']);

    Route::put('update-profile/{id}', [AuthController::class, 'updateProfile']);
    //dashboard
    Route::get('dashboard', [TaskController::class, 'dashboard']);
});
Route::post('api/send-email', [AuthController::class, 'sendEmail']);
Route::post('api/rest-password', [AuthController::class, 'resetPassowrd']);