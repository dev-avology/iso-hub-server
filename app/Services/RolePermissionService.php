<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionService
{
    public function getAllRole()
    {
        $roles = Role::all();
        if ($roles) {
            return $roles;
        } else {
            return null;
        }
    }

    public function createRole($request)
    {
        // Create role
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        // Assign permissions if provided
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        // Loop through permissions and create them if they don't exist
        $permissions = [];
        $allPermission = [];
        $responseArr['role'] = $role;

        if ($request->permission) {
            foreach ($request->permission as $perm) {
                $permission = Permission::firstOrCreate([
                    'name' => $perm['name'],
                    'guard_name' => $perm['guard_name'],
                    'group_name' => $perm['group_name']
                ]);

                $permissions[] = $permission->id;
                $allPermission[] = $permission;
            }
        }
        // Assign permissions to role
        $role->syncPermissions($permissions);
        $responseArr['permissions'] = $allPermission;

        if ($responseArr) {
            return $responseArr;
        } else {
            return null;
        }
    }

    public function updateRole($request)
    {
        // Find role by ID
        $role = Role::findOrFail($request->role_id);

        // Update role name
        $role->update(['name' => $request->name, 'guard_name' => 'web']);

        // Sync new permissions if provided
        $permissions = [];
        $allPermission = [];

        if ($request->has('permission')) {
            foreach ($request->permission as $perm) {
                // Create permission if not exists
                $permission = Permission::firstOrCreate([
                    'name' => $perm['name'],
                    'guard_name' => $perm['guard_name'],
                    'group_name' => $perm['group_name']
                ]);

                $permissions[] = $permission->id;
                $allPermission[] = $permission;
            }
        }

        // Assign new permissions
        $role->syncPermissions($permissions);

        return [
            'role' => $role,
            'permissions' => $allPermission
        ];
    }
    

    public function deleteRole($role_id){
         // Find the role
         $role = Role::findOrFail($role_id);
         // Detach all permissions assigned to the role
         $role->permissions()->detach();
         // Delete the role
         $role->delete();
         return true;
    }
}
