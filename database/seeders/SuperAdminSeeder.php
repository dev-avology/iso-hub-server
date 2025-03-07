<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('name', 'superadmin')->first();

        if (is_null($admin)) {
            $admin           = new User();
            $admin->name     = "superadmin";
            $admin->email    = "superadmin@gmail.com";
            $admin->role = "superadmin";
            $admin->password = Hash::make('12345678');
            $admin->save();
        }
        \Log::info($admin);
        \Log::info('super admin');
    }
}
