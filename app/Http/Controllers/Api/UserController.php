<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Services\DashboardService;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use App\Models\UploadFiles;

class UserController extends Controller
{
    protected $UserService;
    protected $DashboardService;

    public function __construct(UserService $UserService, DashboardService $DashboardService)
    {
        $this->UserService = $UserService;
        $this->DashboardService = $DashboardService;
    }

    public function create(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required',
            'role_id' => 'required|exists:roles,id', // Ensure role_id exists in roles table
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission = 'user.add';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $user = $this->UserService->addUser($request);

        return ApiResponseService::success('New user added successfully', $user);
    }

    public function update(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $request->user_id, // Allow updating own email
            'phone' => 'required',
            'role_id' => 'required|exists:roles,id', // Ensure role_id exists in roles table
            'user_id' => 'required|exists:users,id'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission = 'user.edit';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }

        $user = $this->UserService->updateUser($request);

        return ApiResponseService::success('User updated successfully', $user);
    }

    public function destroy($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return ApiResponseService::error('User not found', 404);
        }
        $permission = 'user.delete';
        $userPermission = $this->DashboardService->checkPermission($permission);

        if (!empty($userPermission)) {
            return $userPermission;
        }
        $user = $this->UserService->destroyUser($user_id);
        return ApiResponseService::success('User deleted successfully');
    }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files' => 'required|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt|max:5120', // Max 5MB
            'user_id' => 'required'
        ]);

        // Store the uploaded file
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
              $path = $request->file('files')->store('uploads', 'public'); // Store in `storage/app/public/uploads`
            }

            return response()->json([
                'message' => 'Files uploaded successfully!',
                'file_path' => asset('storage/' . $path)
            ], 200);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
