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
use App\Http\Controllers\Api\DropboxController;
use App\Http\Controllers\Api\OneDriveController;
use App\Http\Controllers\Api\MarketingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VendorTemplateController;

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

// Google Drive routes
Route::get('auth/google/callback', [NewGoogleDriveController::class, 'handleGoogleCallback']);

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
        Route::post('/clear-signature-mail', [UserController::class, 'clearSignatureSendMail']);
        Route::post('/update-user-info', [UserController::class, 'updateUserInfo']);
        Route::post('/get-user-details', [UserController::class, 'getUserDetails']);
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
        Route::get('/download-zip/{id}', [FileController::class, 'downloadZipFile']);
    });

    Route::group(['prefix' => 'reps'], function () {
        Route::post('/create', [UserController::class, 'createRep']);
        Route::put('/update', [UserController::class, 'updateRep']);
        Route::post('/lists', [UserController::class, 'getRepsList']);
        Route::post('/destroy', [UserController::class, 'destroyRep']);
        Route::get('/get-rep-user', [UserController::class, 'getUserForRep']);
        Route::post('/get-rep-list', [UserController::class, 'getRepsListUsingUserId']);
        
    });

    Route::group(['prefix' => 'marketing'], function () {
        Route::post('/create-category', [MarketingController::class, 'createCategory']);
        Route::post('/create-item', [MarketingController::class, 'createItem']);
        Route::post('/update-item', [MarketingController::class, 'updateItem']);
        Route::post('/lists', [MarketingController::class, 'getCatWithItem']);
        Route::get('/get-item-details/{id}', [MarketingController::class, 'getItemDetails']);
        Route::get('/remove-item/{id}', [MarketingController::class, 'removeItem']);
        Route::get('/remove-category/{id}', [MarketingController::class, 'removeCategory']);
        Route::post('/update-category', [MarketingController::class, 'updateCategory']);
        Route::get('/get-category-details/{id}', [MarketingController::class, 'getCatDetails']);
    });

    Route::group(['prefix' => 'notification'], function () {
        Route::get('/count/{user_id}', [NotificationController::class, 'getUserNoticationCount']);
        Route::post('/remove-notification', [NotificationController::class, 'removeNotification']);
        Route::post('/delete-notification', [NotificationController::class, 'deleteNotification']);
        Route::post('/delete-all-notification', [NotificationController::class, 'deleteAllNotification']);
    });

    Route::group(['prefix' => 'vendor'], function () {
        Route::post('/create-vendor-template', [VendorTemplateController::class, 'storeVendorTemplates']);
        Route::post('/show-vendor-template', [VendorTemplateController::class, 'showVendorTemplate']);
        Route::post('/get-admin-vendor', [VendorTemplateController::class, 'getAdminVendorDropdownData']);
        Route::post('/get-all-vendors-list', [VendorTemplateController::class, 'getAllVendorsList']);
        Route::post('/update-vendor', [VendorTemplateController::class, 'updateVendor']);
        Route::post('/edit-vendor', [VendorTemplateController::class, 'editVendorDetails']);
        Route::post('/delete-vendor', [VendorTemplateController::class, 'deleteVendor']);
        Route::post('/update-card-order', [VendorTemplateController::class, 'updateCardOrder']);
    });


    Route::post('jotform/lists', [JotFromController::class, 'getFormsList']);
    Route::get('jotform/{id}', [JotFromController::class, 'getFromDetails']);
    Route::post('duplicate-form-send-mail', [JotFromController::class, 'sendFormDuplicateMail']);
    Route::get('destroy-jotform/{id}', [JotFromController::class, 'destroyJotForm']);
    Route::post('generate-form-token', [JotFromController::class, 'generateFormToken']);
    Route::post('chat-hash', [JotFromController::class, 'getChatHash']);


    // Google Drive Routes
    Route::get('/google/redirect', [NewGoogleDriveController::class, 'redirectToGoogle']);
    Route::post('/google/callback', [NewGoogleDriveController::class, 'handleCallback']);
    Route::get('/google/drive/list', [NewGoogleDriveController::class, 'listFiles']);
    Route::post('/google/disconnect', [NewGoogleDriveController::class, 'disconnect']);

    // Dropbox routes
    Route::get('/dropbox/redirect', [DropboxController::class, 'redirectToDropbox']);
    Route::post('/dropbox/callback', [DropboxController::class, 'handleCallback']);
    Route::get('/dropbox/list', [DropboxController::class, 'listFiles']);
    Route::post('/dropbox/disconnect', [DropboxController::class, 'disconnect']);

    // OneDrive routes
    Route::get('/onedrive/redirect', [OneDriveController::class, 'redirectToOneDrive']);
    Route::post('/onedrive/callback', [OneDriveController::class, 'handleCallback']);
    Route::get('/onedrive/list', [OneDriveController::class, 'listFiles']);
    Route::post('/onedrive/disconnect', [OneDriveController::class, 'disconnect']);
});