<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RolePermissionController;

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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::group(['prefix' => 'role'], function () {
        Route::get('/view', [RolePermissionController::class, 'index']);
        Route::post('/create', [RolePermissionController::class, 'create']);
        Route::put('/update', [RolePermissionController::class, 'update']);
        Route::get('/delete/{role_id}', [RolePermissionController::class, 'destroy']);
    });

    Route::post('/assign-role', [RolePermissionController::class, 'assignRole']);
    Route::post('/assign-permission', [RolePermissionController::class, 'assignPermission']);
    

});
