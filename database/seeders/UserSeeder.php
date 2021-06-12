<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(['Annie', 'Bob'] as $Key => $name) {
            $userId = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => $name.'@gmail.com',
                'password' => Hash::make('password'),
            ]);
            DB::table('plays')->insert([
                'user_id' => $userId,
                'player' => $Key + 1,
            ]);
        }
    }
}
