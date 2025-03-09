<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([[
            'name'=>'admin1',
            'email'=>'hamedtgh17@gmail.com',
            'password'=>Hash::make('Hamed123456'),
            'role'=>'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name'=>'admin2',
            'email'=>'admin2@gmail.com',
            'password'=>Hash::make('Hamed123456'),
            'role'=>'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name'=>'admin3',
            'email'=>'admin3@gmail.com',
            'password'=>Hash::make('Hamed123456'),
            'role'=>'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        ]);
    }
}
