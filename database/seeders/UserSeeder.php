<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::truncate();
        foreach(['Annie', 'Bob'] as $Key => $name) {
            $userId = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => $name.'@gmail.com',
                'password' => Hash::make('password'),
            ]);
            DB::table('plays')->insert(['user_id' => $userId]);
        }
    }
}
