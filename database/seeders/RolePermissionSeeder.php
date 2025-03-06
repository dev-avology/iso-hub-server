<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            [
                'group_name' => 'team_member',
                'permissions' => [
                    'team_member.add',
                    'team_member.edit',
                    'team_member.delete',
                ]
            ],
            [
                'group_name' => 'dashboard',
                'permissions' => [
                    'dashboard.view',
                    'dashboard.edit',
                ]
            ],
        ];

         // Do same for the admin guard for tutorial purposes.
         $admin = User::where('name', 'superadmin')->first();
         $roleSuperAdmin = $this->maybeCreateSuperAdminRole($admin);
 
         // Create and Assign Permissions
         for ($i = 0; $i < count($permissions); $i++) {
             $permissionGroup = $permissions[$i]['group_name'];
             for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
                 $permissionExist = Permission::where('name', $permissions[$i]['permissions'][$j])->first();
                 if (is_null($permissionExist)) {
                     $permission = Permission::create(
                         [
                             'name' => $permissions[$i]['permissions'][$j],
                             'group_name' => $permissionGroup,
                             'guard_name' => 'api'
                         ]
                     );
                     $roleSuperAdmin->givePermissionTo($permission);
                     $permission->assignRole($roleSuperAdmin);
                 }
             }
         }
 
         // Assign super admin role permission to superadmin user

         if ($admin) {
             $admin->assignRole($roleSuperAdmin);
         }
    }

    private function maybeCreateSuperAdminRole($admin): Role
    {
        if (is_null($admin)) {
            $roleSuperAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'api']);
        } else {
            $roleSuperAdmin = Role::where('name', 'superadmin')->where('guard_name', 'api')->first();
        }

        if (is_null($roleSuperAdmin)) {
            $roleSuperAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'api']);
        }
        return $roleSuperAdmin;
    }
}
