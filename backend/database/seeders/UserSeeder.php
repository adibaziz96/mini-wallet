<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Adib',
            'email' => 'adib@example.com',
            'password' => Hash::make('123'),
            'balance' => 10000.00,
        ]);

        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('123'),
                'balance' => $faker->randomFloat(2, 100, 2000),
            ]);
        }
    }
}
