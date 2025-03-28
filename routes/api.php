<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GoogleDriveController;
use App\Http\Controllers\Api\JotFromController;
use App\Http\Controllers\Api\NewGoogleDriveController;

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
Route::post('/upload-files', [FileController::class, 'uploadFiles']);
Route::post('/jot-forms', [JotFromController::class, 'createForm']);
Route::get('/file/check-unique-string/{string}', [FileController::class, 'checkUniqueString']);
Route::get('/jotform-check-unique-string/{string}', [JotFromController::class, 'jotFormcheckUniqueString']);

// Get Google Auth URL
// Route::get('google/auth-url', [GoogleDriveController::class, 'getAuthUrl']);

// // Handle Google Callback
// Route::get('google/callback', [GoogleDriveController::class, 'handleCallback']);

// // List Google Drive Files
// Route::get('google/files', [GoogleDriveController::class, 'listFiles']);

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

    Route::group(['prefix' => 'user'], function () {
        Route::get('/get-user-permission/{user_id}', [UserController::class, 'getUserPermission']);
        Route::post('/create', [UserController::class, 'createUser']);
        Route::put('/update', [UserController::class, 'updateUser']);
        Route::get('/destroy/{id}', [UserController::class, 'destroyUser']);
        Route::post('/lists', [UserController::class, 'getUsers']);
        Route::post('/send-mail', [UserController::class, 'sendEmailToProspect']);
    });

    Route::group(['prefix' => 'team-member'], function () {
        Route::post('/create', [UserController::class, 'createTeamMember']);
        Route::put('/update', [UserController::class, 'updateTeamMember']);
        Route::get('/destroy/{id}', [UserController::class, 'destroyTeamMember']);
        Route::post('/lists', [UserController::class, 'getTeamMembersList']);
    });

    Route::group(['prefix' => 'vendor'], function () {
        Route::post('/create', [UserController::class, 'createVendor']);
        Route::put('/update', [UserController::class, 'updateVendor']);
        Route::get('/destroy/{id}', [UserController::class, 'destroyVendor']);
        Route::post('/lists', [UserController::class, 'getVendorsList']);
    });

    Route::group(['prefix' => 'file'], function () {
        Route::get('/lists/{id}', [FileController::class, 'getProspectFiles']);
        Route::get('/delete/{id}', [FileController::class, 'destroyFile']);
        Route::get('/download/{id}', [FileController::class, 'downloadFile']);
    });

    Route::get('google/auth', [NewGoogleDriveController::class, 'redirectToGoogle']);
    Route::post('jotform/lists', [JotFromController::class, 'getFormsList']);
    Route::get('jotform/{id}', [JotFromController::class, 'getFromDetails']);
    
    
    // Route::group(['prefix' => 'google'], function () {
        //     // Get Google Auth URL
        //     // Route::get('/auth-url', [GoogleDriveController::class, 'getAuthUrl']);
        //     // // Handle Google Callback
        //     // Route::get('/callback', [GoogleDriveController::class, 'handleCallback']);
        //     // // List Google Drive Files
        //     // Route::get('/files', [GoogleDriveController::class, 'listFiles']);
        
        // });
    });
    Route::get('google/drive/list', [NewGoogleDriveController::class, 'listFiles']);
    Route::get('auth/google/callback', [NewGoogleDriveController::class, 'handleGoogleCallback']);