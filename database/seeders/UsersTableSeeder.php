<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            [
                'name' => 'Hajar Lafdaoui',
                'email' => 'hajarlafdaoui@gmail.com',
                'password' => Hash::make('password123'),
                'timezone' => 'UTC',
                'has_personal_workspace' => true,
                'last_active_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('password123'),
                'timezone' => 'UTC',
                'has_personal_workspace' => false,
                'last_active_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'janesmith@example.com',
                'password' => Hash::make('password123'),
                'timezone' => 'UTC',
                'has_personal_workspace' => false,
                'last_active_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alicebrown@example.com',
                'password' => Hash::make('password123'),
                'timezone' => 'UTC',
                'has_personal_workspace' => false,
                'last_active_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
