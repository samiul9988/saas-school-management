<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'monthly_price' => 1000,
                'yearly_price' => 10000,
                'max_students' => 200,
                'max_teachers' => 20,
                'max_branches' => 1,
                'sort_order' => 1,
                'features' => ['students', 'teachers', 'attendance', 'exams', 'results', 'basic_fees'],
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'monthly_price' => 2500,
                'yearly_price' => 25000,
                'max_students' => 500,
                'max_teachers' => 50,
                'max_branches' => 1,
                'sort_order' => 2,
                'features' => ['students', 'teachers', 'attendance', 'exams', 'results', 'basic_fees', 'parent_portal', 'sms', 'library', 'payroll', 'advanced_reports'],
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'monthly_price' => 5000,
                'yearly_price' => 50000,
                'max_students' => null,
                'max_teachers' => null,
                'max_branches' => 10,
                'sort_order' => 3,
                'features' => ['students', 'teachers', 'attendance', 'exams', 'results', 'basic_fees', 'parent_portal', 'sms', 'library', 'payroll', 'advanced_reports', 'transport', 'hostel', 'inventory', 'online_payment', 'multi_branch', 'advanced_accounting'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
