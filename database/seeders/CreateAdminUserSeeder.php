<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'ahmed',
            'email' => 'admin@admin.com',
            'password' => 12345678,
            'auth' => 1,
        ]);

        $user->media()->create( ['file_name' => '01.jpg', 'file_type' => 'image/jpg', 'file_size' => rand(100, 900), 'file_status' => true, 'file_sort' => 0]);
    }
}
