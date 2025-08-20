<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'petugas_pendaftaran']);
        Role::create(['name' => 'dokter']);
        Role::create(['name' => 'kasir']);

        User::find(1)->assignRole('admin');
        User::find(2)->assignRole('petugas_pendaftaran');
    }
}
