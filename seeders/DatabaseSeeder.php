<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin untuk login
        User::firstOrCreate(
            ['email' => 'admin@umkudus.ac.id'],
            [
                'name'     => 'Administrator',
                'password' => bcrypt('password'),
            ]
        );

        // Seeder data master ormawa
        $this->call([
            OrmawaSeeder::class,
        ]);
    }
}
