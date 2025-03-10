<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                    'team_member.view',
                ]
            ]
        ];

         // Do same for the admin guard for tutorial purposes.
         $admin = User::where('role_id', '1')->first();
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
                             'guard_name' => 'web'
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
            $roleSuperAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        } else {
            $roleSuperAdmin = Role::where('name', 'superadmin')->where('guard_name', 'web')->first();
        }

        if (is_null($roleSuperAdmin)) {
            $roleSuperAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        }

        if (is_null($admin)) {
            $admin           = new User();
            $admin->first_name     = "superadmin";
            $admin->last_name     = "superadmin";
            $admin->email    = "superadmin@gmail.com";
            $admin->phone    = "4343434343";
            $admin->role_id = "1";
            $admin->password = Hash::make('12345678');
            $admin->save();
        }

         // Ensure the data is inserted into model_has_roles table manually
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $roleSuperAdmin->id,
            'model_type' => User::class,
            'model_id' => $admin->id
        ]);

        return $roleSuperAdmin;
    }
}
