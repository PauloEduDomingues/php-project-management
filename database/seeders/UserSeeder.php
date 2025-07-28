<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'User00',
            'email' => 'user00@gmail.com',
            'password' => '123456'
        ])->assignRole('admin');

        User::create([
            'name' => 'User01',
            'email' => 'user01@gmail.com',
            'password' => '123456'
        ])->assignRole('manager');

        User::create([
            'name' => 'User02',
            'email' => 'user02@gmail.com',
            'password' => '123456'
        ])->assignRole('colaborator');
    }
}
