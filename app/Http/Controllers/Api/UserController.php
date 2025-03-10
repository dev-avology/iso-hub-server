<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Services\DashboardService;
use App\Services\ApiResponseService; // Import API response service
use App\Models\User;
use App\Models\UploadFiles;
use App\Models\Vendor;

class UserController extends Controller
{
    protected $UserService;
    protected $DashboardService;

    public function __construct(UserService $UserService, DashboardService $DashboardService)
    {
        $this->UserService = $UserService;
        $this->DashboardService = $DashboardService;
    }

    public function createTeamMember(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:team_members,email',
            'phone' => 'required',
            'address' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $permission = 'user.add';
        // $userPermission = $this->DashboardService->checkPermission($permission);

        // if (!empty($userPermission)) {
        //     return $userPermission;
        // }

        $user = $this->UserService->addTeamMember($request);

        return ApiResponseService::success('Team member added successfully', $user);
    }

    public function createVendor(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:vendors,email',
            'phone' => 'required',
            'address' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = $this->UserService->addVendor($request);
        return ApiResponseService::success('Vendor added successfully', $user);
    }

    public function updateTeamMember(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:team_members,email,' . $request->id,
            'phone' => 'required',
            'id' => 'required|exists:team_members,id',
            'address' => 'required'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = $this->UserService->updateTeamMember($request);
        return ApiResponseService::success('Team member updated successfully', $user);
    }

    public function updateVendor(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:vendors,email,' . $request->id,
            'phone' => 'required',
            'id' => 'required|exists:vendors,id',
            'address' => 'required'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = $this->UserService->updateVendor($request);
        return ApiResponseService::success('Vendor updated successfully', $user);
    }


    public function destroyTeamMember($id)
    {
        $user = TeamMember::find($id);
        if (!$user) {
            return ApiResponseService::error('Team member not found', 404);
        }
        $user = $this->UserService->destroyTeamMember($id);
        return ApiResponseService::success('Team member deleted successfully');
    }

    public function destroyVendor($id)
    {
        $user = Vendor::find($id);
        if (!$user) {
            return ApiResponseService::error('Vendor not found', 404);
        }
        $user = $this->UserService->destroyVendor($id);
        return ApiResponseService::success('Vendor deleted successfully');
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
                $path = $request->file('files')->store('uploads', 'public');
            }

            return response()->json([
                'message' => 'Files uploaded successfully!',
                'file_path' => asset('storage/' . $path)
            ], 200);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function getUserPermission($user_id)
    {
        // Find user by email
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return ApiResponseService::error('User not found', 404);
        }

        $roles = $user->getRoleNames(); // Returns a collection of role names
        $permissions = $user->getAllPermissions()->pluck('name'); // Get all permissions assigned

        return response()->json([
            'message' => 'User permissions fetched successfully',
            'user' => $user,
            'roles' => $roles, // List of roles
            'permissions' => $permissions, // List of permissions
        ]);
    }
}
