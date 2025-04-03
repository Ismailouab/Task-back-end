<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('teams')->insert([
            ['name' => 'Team Alpha', 'slug' => 'team-alpha', 'description' => 'Alpha team description', 'owner_id' => 1, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Team Beta', 'slug' => 'team-beta', 'description' => 'Beta team description', 'owner_id' => 2, 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Team Gamma', 'slug' => 'team-gamma', 'description' => 'Gamma team description', 'owner_id' => 3, 'is_public' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Team Delta', 'slug' => 'team-delta', 'description' => 'Delta team description', 'owner_id' => 4, 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
