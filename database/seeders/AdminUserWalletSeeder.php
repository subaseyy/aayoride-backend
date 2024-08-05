<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\UserManagement\Entities\UserAccount;

class AdminUserWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::query()->firstWhere('email', 'admin@admin.com');
        UserAccount::query()->create([
            'user_id' => $admin->id
        ]);
    }
}
