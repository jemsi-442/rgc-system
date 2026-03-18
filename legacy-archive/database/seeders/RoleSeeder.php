<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Mwanachama',
                'slug' => 'mwanachama',
                'description' => 'Church Member - Limited view-only permissions',
                'permissions' => [
                    // View permissions only
                    'view_dashboard',
                    'view_events',
                    'view_own_profile',
                    'update_own_profile',
                ],
            ],
            [
                'name' => 'Mhasibu',
                'slug' => 'mhasibu',
                'description' => 'Accountant - Full financial management permissions',
                'permissions' => [
                    // Dashboard
                    'view_dashboard',
                    'view_analytics',

                    // Income management
                    'view_incomes',
                    'create_incomes',
                    'update_incomes',
                    'delete_incomes',
                    'export_incomes',

                    // Expense management
                    'view_expenses',
                    'create_expenses',
                    'update_expenses',
                    'delete_expenses',
                    'export_expenses',

                    // Category management
                    'view_income_categories',
                    'create_income_categories',
                    'update_income_categories',
                    'delete_income_categories',

                    'view_expense_categories',
                    'create_expense_categories',
                    'update_expense_categories',
                    'delete_expense_categories',

                    // Financial reports
                    'view_financial_reports',
                    'generate_financial_reports',
                    'export_financial_reports',

                    // Requests
                    'view_requests',
                    'create_requests',
                    'update_requests',

                    // Events (view only)
                    'view_events',

                    // Members (view only)
                    'view_members',

                    // Profile
                    'view_own_profile',
                    'update_own_profile',
                ],
            ],
            [
                'name' => 'Mchungaji',
                'slug' => 'mchungaji',
                'description' => 'Pastor - Full administrative permissions for everything',
                'permissions' => [
                    // Dashboard
                    'view_dashboard',
                    'view_analytics',

                    // Income management
                    'view_incomes',
                    'create_incomes',
                    'update_incomes',
                    'delete_incomes',
                    'export_incomes',

                    // Expense management
                    'view_expenses',
                    'create_expenses',
                    'update_expenses',
                    'delete_expenses',
                    'export_expenses',

                    // Category management
                    'view_income_categories',
                    'create_income_categories',
                    'update_income_categories',
                    'delete_income_categories',

                    'view_expense_categories',
                    'create_expense_categories',
                    'update_expense_categories',
                    'delete_expense_categories',

                    // Financial reports
                    'view_financial_reports',
                    'generate_financial_reports',
                    'export_financial_reports',

                    // Member management
                    'view_members',
                    'create_members',
                    'update_members',
                    'delete_members',
                    'export_members',

                    // User management
                    'view_users',
                    'create_users',
                    'update_users',
                    'delete_users',

                    // Role management
                    'view_roles',
                    'create_roles',
                    'update_roles',
                    'delete_roles',

                    // Event management
                    'view_events',
                    'create_events',
                    'update_events',
                    'delete_events',

                    // Request management
                    'view_requests',
                    'create_requests',
                    'update_requests',
                    'delete_requests',
                    'approve_requests',
                    'reject_requests',

                    // Settings management
                    'view_settings',
                    'update_settings',

                    // System administration
                    'access_system_settings',
                    'view_audit_logs',
                    'manage_backup',

                    // Profile
                    'view_own_profile',
                    'update_own_profile',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
