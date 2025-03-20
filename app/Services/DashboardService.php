<?php
namespace App\Services;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardService
{
    /**
     * Retrieve all dashboard data
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
    */

    public function getDashboardData()
    {
        return [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users' => User::count(),
            'user_list' => User::all(),
        ];
    }

    public function checkPermission($permission){
        $user = Auth::user();
        // Get all permissions of the user
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Check if the required permission exists in the user's permissions array
        if (!in_array($permission, $userPermissions)) {
            return ApiResponseService::error('Sorry! You are unauthorized for this operation.', 401);
        }else{
            return null;
        }
    }
}