<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;

class FixAdminRole extends Command
{
    protected $signature = 'fix:admin-role';
    protected $description = 'Fix admin role assignment for sidebar';

    public function handle()
    {
        $this->info('Creating Super Admin role...');
        
        // Create or update Super Admin role
        $role = Role::updateOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'permissions' => '{"master_database":true,"absensi":true,"overtime":true,"cuti_tunjangan":true,"slip_gaji":true,"training_development":true,"reports":true,"settings":true}',
                'description' => 'Full access to all system features',
                'is_active' => true
            ]
        );

        $this->info('Super Admin role created/updated with ID: ' . $role->id);

        // Get first admin user
        $admin = User::first();
        if (!$admin) {
            $this->error('No admin user found!');
            return;
        }

        $this->info('Found admin user: ' . $admin->name . ' (ID: ' . $admin->id . ')');

        // Check if admin already has this role
        if ($admin->roles()->where('role_id', $role->id)->exists()) {
            $this->info('Admin already has Super Admin role');
        } else {
            // Assign role to admin
            $admin->roles()->attach($role->id);
            $this->info('Super Admin role assigned to admin user');
        }

        // Verify assignment
        $adminRoles = $admin->roles()->get();
        $this->info('Admin roles: ' . $adminRoles->pluck('name')->implode(', '));

        $this->info('Admin role fix completed!');
    }
}
