<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Complete list of permissions
        $permissions = [
            // Patient Permissions
            'access_patient',
            'insert_patient',
            'update_patient',
            'delete_patient',
            'detail_patient',

            // Ordonance Permissions
            'access_ordonance',
            'insert_ordonance',
            'update_ordonance',
            'delete_ordonance',

            // Creance Permissions
            'access_creance',
            'search_creance',

            // Debt Permissions
            'access_debt',
            'insert_debt',
            'delete_debt',

            // External Debt Permissions
            'access_external_debt',
            'insert_external_debt',
            'delete_external_debt',

            // Document Permissions
            'access_document',
            'insert_document',
            'delete_document',
            'download_document',
            'detail_document',

            // Supplier Permissions
            'access_supplier',
            'add_supplier',
            'delete_supplier',
            'modify_supplier',

            // Stock Permissions
            'access_stock',
            'add_stock',
            'delete_stock',
            'modify_stock',

            // Product Permissions
            'access_product',
            'add_product',
            'delete_product',
            'modify_product',

            // Historique Enter Permissions
            'access_historique_enter',
            'add_historique_enter',
            'delete_historique_enter',
            'modify_historique_enter',

            // Historique Sortie Permissions
            'access_historique_sortie',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Create the doctor role and assign all permissions
        $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'sanctum']);
        $doctorRole->syncPermissions(Permission::all());
    }
}