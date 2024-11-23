<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* $permissions = [
            'view patients',
            'edit patients',
            'create patients',
            'delete patients',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        } */

        // Create roles and assign existing permissions
        /*     $doctor = Role::create(['name' => 'doctor']);
        $nurse = Role::create(['name' => 'nurse']); */
        /*  $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $nurse = Role::firstOrCreate(['name' => 'nurse']); */
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $nurseRole = Role::firstOrCreate(['name' => 'nurse']);

        // Assign 'doctor' role to all users with `role` column value 'doctor'
        User::where('role', 'doctor')->each(function ($user) use ($doctorRole) {
            $user->assignRole($doctorRole);
        });

        // Assign 'nurse' role to all users with `role` column value 'nurse'
        User::where('role', 'nurse')->each(function ($user) use ($nurseRole) {
            $user->assignRole($nurseRole);
        });

        $this->command->info('Roles assigned based on the role column!');
        // Assign all permissions to doctor
        /*   $doctor->givePermissionTo(Permission::all());

        // Assign limited permissions to nurse
        $nurse->givePermissionTo(['view patients']); */
    }
}
