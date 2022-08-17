<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $user = [
            'name' => 'admin',
            'email' => 'admin@abc.com',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('admin'),
            'login_type' => 'Manual'
        ];

        $user = User::create($user);

        DB::table('user_roles')->delete();

        $role = Role::where('name', 'admin')->first('id');

        $userRole = [
            'role_id' => $role->id,
            'user_id' => $user->id
        ];

        UserRole::create($userRole);

    }
}
