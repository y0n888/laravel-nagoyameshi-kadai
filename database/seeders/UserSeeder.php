<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory()->count(100)->create();

        foreach (range(1, 7) as $id) {
            User::create([
                'id' => $id, // IDを指定
                'name' => fake()->name(),
                'kana' => fake()->kanaName(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' =>Hash::make('password'),
                'remember_token' => Str::random(10),
                'postal_code' => fake()->postcode(),
                'address' => fake()->address(),
                'phone_number' => fake()->phoneNumber(),
            ]);
        }   
    }
}
