<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Ini Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Ini Petugas Pendaftaran',
            'email' => 'petugas@example.com',
            'password' => bcrypt('petugas'),
            'role' => 'petugas_pendaftaran'
        ]);

        $this->call([
            PermissionSeeder::class,
        ]);
    }
}
