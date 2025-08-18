<?php

namespace Database\Seeders;

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
        $admin = Role::create(['name' => 'admin']);
        $registrationMgmt = Role::create(['name' => 'petugas_pendaftaran']);
        $doctor = Role::create(['name' => 'dokter']);
        $cashier = Role::create(['name' => 'kasir']);

        // Permissions
        $permissions = [
            // Patient management
            'view_patients', 'create_patients', 'edit_patients',
            
            // Medical records
            'view_medical_records', 'create_medical_records', 'edit_medical_records',
            
            // Prescriptions
            'view_prescriptions', 'create_prescriptions', 'edit_prescriptions',
            
            // Billing
            'view_bills', 'create_bills', 'process_payments',
            
            // Master data
            'manage_employees', 'manage_medicines', 'manage_actions',
            
            // Reports
            'view_reports', 'view_analytics'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign Permission
        $admin->givePermissionTo(Permission::all());
    
        $registrationMgmt->givePermissionTo([
            'view_patients', 'create_patients', 'edit_patients'
        ]);
        
        $doctor->givePermissionTo([
            'view_patients', 'view_medical_records', 'create_medical_records',
            'edit_medical_records', 'view_prescriptions', 'create_prescriptions'
        ]);
        
        $cashier->givePermissionTo([
            'view_patients', 'view_bills', 'create_bills', 'process_payments'
        ]);
    }
}
