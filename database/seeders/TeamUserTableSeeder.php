<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class TeamUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('team_user')->insert([
            ['team_id' => 1, 'user_id' => 1, 'role' => 'owner', 'created_at' => now(), 'updated_at' => now()],
            ['team_id' => 1, 'user_id' => 2, 'role' => 'member', 'created_at' => now(), 'updated_at' => now()],
            ['team_id' => 2, 'user_id' => 3, 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['team_id' => 3, 'user_id' => 4, 'role' => 'member', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
