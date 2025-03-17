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
use Illuminate\Support\Facades\Mail;
use App\Models\Vendor;
use App\Mail\ProspectMail;

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

    public function createUser(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required',
            'role_id' => 'required',
            'password' => 'required',
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

        $user = $this->UserService->addUser($request);

        return ApiResponseService::success('User added successfully', $user);
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

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $request->id,
            'phone' => 'required',
            'role_id' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = $this->UserService->updateUser($request);
        return ApiResponseService::success('User updated successfully', $user);
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

    public function destroyUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponseService::error('User not found', 404);
        }
        $user = $this->UserService->destroyUser($id);
        return ApiResponseService::success('User deleted successfully');
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

    public function getUserPermission($user_id)
    {
        // Find user by email
        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return ApiResponseService::error('User not found', 404);
        }
        $roles = $user->getRoleNames(); // Returns a collection of role names
        $permissions = $user->getAllPermissions()->pluck('name'); // Get all permissions assigned
        $data = [
            'user' => $user,
            'roles' => $roles, // List of roles
            'permissions' => $permissions, // List of permissions
        ];
        return ApiResponseService::success('User permissions fetched successfully', $data);
    }

    public function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->user_id) {
            $query->where('id', $request->user_id);
        }

        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }
        $users = $query->get();
        return ApiResponseService::success('User lists fetched successfully', $users);
    }

    public function getTeamMembersList(Request $request)
    {
        $query = TeamMember::query();

        if ($request->id) {
            $query->where('id', $request->id);
        }
        $team_members = $query->get();
        return ApiResponseService::success('Team member lists fetched successfully', $team_members);
    }

    public function getVendorsList(Request $request)
    {
        $query = Vendor::query();

        if ($request->id) {
            $query->where('id', $request->id);
        }
        $vendors = $query->get();
        return ApiResponseService::success('Vendor lists fetched successfully', $vendors);
    }

    public function sendEmailToProspect(Request $request)
    {
        // Use Validator for detailed error handling
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email' => 'required|email',
            'name' => 'required',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->user_id;
        $emailId = $request->email;
        $name = $request->name;

        $data = [
            'user_id' => $userId,
            'email' => $emailId,
            'name' => $name
        ];

        Mail::to($emailId)->send(new ProspectMail($userId, $emailId, $name));

        return ApiResponseService::success('Email sent successfully', $data);
    }
}
