<?php

namespace App\Support;

class ModulePermissionMap
{
    /**
     * Maps the first segment of a `school.*` route name to the module its
     * access should be checked against. Segments not listed here (dashboard,
     * profile, positions/users management itself, dynamic helper endpoints
     * like groups lookups) are always allowed once a user is school staff.
     *
     * @var array<string, string>
     */
    public const MAP = [
        'students' => 'students',

        'teachers' => 'hr',
        'designations' => 'hr',

        'hr-attendance' => 'hr_attendance',

        'student-attendance' => 'student_attendance',
        'attendance' => 'student_attendance',

        'classes' => 'academics',
        'sections' => 'academics',
        'subjects' => 'academics',
        'academic-years' => 'academics',
        'routine' => 'academics',

        'exams' => 'exams',
        'exam-configuration' => 'exams',
        'exam-short-codes' => 'exams',
        'exam-subjects' => 'exams',
        'grades' => 'exams',
        'marks' => 'exams',
        'mark-input' => 'exams',
        'mark-update' => 'exams',
        'mark-sheets' => 'exams',
        'reports' => 'exams',
        'results' => 'exams',
        'results-process' => 'exams',
        'subject-components' => 'exams',

        'finance' => 'finance',
        'fee-types' => 'finance',
        'student-fees' => 'finance',
        'fee-payments' => 'finance',
        'incomes' => 'finance',
        'expenses' => 'finance',
        'accounts' => 'finance',
        'journal-entries' => 'finance',
        'accounting-reports' => 'finance',

        'payrolls' => 'payroll',
        'salary-structures' => 'payroll',

        'sms-wallet' => 'sms',
        'sms-credit-orders' => 'sms',
        'sms-send' => 'sms',
        'sms-reports' => 'sms',
        'sms-templates' => 'sms',

        'books' => 'library',
        'book-categories' => 'library',
        'book-issues' => 'library',

        'inventory' => 'inventory',
        'inventory-categories' => 'inventory',
        'inventory-items' => 'inventory',

        'transport-routes' => 'transport',
        'vehicles' => 'transport',
        'student-transports' => 'transport',

        'signatures' => 'institute_setting',
        'notices' => 'institute_setting',
        'users' => 'institute_setting',
        'positions' => 'institute_setting',
    ];

    /**
     * Maps an HTTP method to the permission action it represents.
     *
     * @var array<string, string>
     */
    public const ACTION_BY_METHOD = [
        'GET' => 'view',
        'HEAD' => 'view',
        'POST' => 'create',
        'PUT' => 'edit',
        'PATCH' => 'edit',
        'DELETE' => 'delete',
    ];

    public static function moduleForRouteName(?string $routeName): ?string
    {
        if (! $routeName || ! str_starts_with($routeName, 'school.')) {
            return null;
        }

        $segment = explode('.', substr($routeName, 7))[0];

        return self::MAP[$segment] ?? null;
    }

    public static function actionForMethod(string $method): string
    {
        return self::ACTION_BY_METHOD[strtoupper($method)] ?? 'view';
    }
}
