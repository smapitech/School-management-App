<?php

declare(strict_types=1);

namespace App;

final class Auth
{
    public const ROLES = [
        'superadmin' => 'Superadmin',
        'admin' => 'Admin',
        'teacher' => 'Teacher',
        'accountant' => 'Accountant',
        'receptionist' => 'Receptionist',
        'parent' => 'Parent',
        'student' => 'Student',
    ];

    /**
     * Legacy fallback permissions used when the database matrix is not available.
     * These still preserve the existing special actions in the project.
     *
     * @var array<string, array<string, array<int, string>>>
     */
    public const ROLE_PERMISSIONS = [
        'superadmin' => ['*' => ['view', 'create', 'edit', 'delete', 'upload', 'settings', 'manage', 'print', 'export']],
        'admin' => [
            'dashboard' => ['view'],
            'students' => ['view', 'create', 'edit', 'delete', 'upload'],
            'student_portal' => ['view'],
            'parent_portal' => ['view'],
            'admissions' => ['view', 'create', 'edit', 'delete'],
            'staff' => ['view', 'create', 'edit'],
            'classes' => ['view', 'create', 'edit'],
            'class_assign' => ['view', 'create', 'edit', 'delete'],
            'subject_form' => ['view', 'create', 'edit', 'delete'],
            'subject_assignment' => ['view', 'create', 'edit', 'delete'],
            'timetable' => ['view', 'create', 'edit', 'delete', 'print'],
            'attendance' => ['view', 'create', 'edit'],
            'teacher_attendance' => ['view', 'create', 'edit'],
            'fees' => ['view', 'create', 'edit', 'delete', 'print'],
            'accounting' => ['view', 'create', 'edit', 'delete', 'print', 'export'],
            'human_resource' => ['view', 'create', 'edit'],
            'payroll' => ['view', 'create', 'edit', 'delete', 'print'],
            'exams' => ['view', 'create', 'edit', 'delete'],
            'mark_distribution' => ['view', 'create', 'edit', 'delete', 'manage'],
            'exam_mark' => ['view', 'create', 'edit', 'delete', 'print'],
            'result_preview' => ['view', 'print', 'edit'],
            'exam_schedule' => ['view', 'create', 'edit', 'delete', 'print'],
            'marksheet_templates' => ['view', 'create', 'edit', 'delete', 'manage', 'print'],
            'homework' => ['view', 'create', 'edit', 'delete'],
            'notifications' => ['view', 'create', 'edit', 'delete', 'send', 'manage'],
            'notification_manager' => ['view', 'create', 'edit', 'delete', 'send', 'manage'],
            'website' => ['view', 'create', 'edit', 'delete', 'upload'],
            'library' => ['view', 'create', 'edit', 'delete'],
            'transport' => ['view'],
            'hostel' => ['view'],
            'communication' => ['view', 'create', 'edit', 'delete', 'send', 'manage'],
            'reception' => ['view'],
            'reports' => ['view', 'create', 'edit', 'delete', 'print', 'export'],
            'settings' => ['view', 'create', 'edit', 'delete', 'manage', 'upload'],
            'profile' => ['view', 'edit'],
        ],
        'teacher' => [
            'dashboard' => ['view'],
            'students' => ['view_assigned'],
            'student_portal' => ['view'],
            'parent_portal' => ['view'],
            'staff' => ['view'],
            'payroll' => ['view_own_payslips', 'view_own_allowances', 'view_own_deductions', 'view_own_increments'],
            'classes' => ['view', 'view_assigned', 'view_students', 'view_subjects', 'view_timetable', 'create_timetable', 'edit_own_timetable', 'delete_own_timetable'],
            'class_assign' => ['view'],
            'subject_form' => ['view'],
            'subject_assignment' => ['view'],
            'timetable' => ['view', 'create', 'edit'],
            'attendance' => ['view', 'mark_assigned_students', 'view_assigned_attendance', 'view_own_staff_attendance'],
            'teacher_attendance' => ['view', 'view_own_staff_attendance'],
            'fees' => ['view'],
            'accounting' => ['view'],
            'human_resource' => ['view'],
            'exams' => ['view'],
            'mark_distribution' => ['view'],
            'exam_mark' => ['view', 'create', 'edit'],
            'result_preview' => ['view', 'print', 'edit'],
            'exam_schedule' => ['view'],
            'marksheet_templates' => ['view'],
            'homework' => ['view', 'create', 'edit'],
            'notifications' => ['view'],
            'communication' => ['view', 'message_assigned_parents', 'message_teachers', 'view_own_conversations'],
            'website' => ['view'],
            'reports' => ['view'],
            'profile' => ['view', 'edit'],
        ],
        'accountant' => [
            'dashboard' => ['view'],
            'students' => ['view'],
            'fees' => ['view', 'create', 'edit', 'delete', 'print'],
            'accounting' => ['view', 'create', 'edit', 'delete', 'print', 'export'],
            'human_resource' => ['view', 'create', 'edit'],
            'payroll' => ['view', 'print'],
            'reports' => ['view', 'print', 'export'],
            'notifications' => ['view'],
            'profile' => ['view', 'edit'],
        ],
        'receptionist' => [
            'dashboard' => ['view'],
            'admissions' => ['view', 'create', 'edit'],
            'students' => ['view', 'create'],
            'communication' => ['view', 'create'],
            'notifications' => ['view'],
            'profile' => ['view', 'edit'],
        ],
        'parent' => [
            'dashboard' => ['view'],
            'parent_portal' => ['view'],
            'notifications' => ['view'],
            'profile' => ['view', 'edit'],
        ],
        'student' => [
            'dashboard' => ['view'],
            'student_portal' => ['view', 'edit'],
            'notifications' => ['view'],
            'profile' => ['view', 'edit'],
        ],
    ];

    /**
     * Database-backed permissions for the current request.
     *
     * @var array<string, array<string, array<string, bool>>>
     */
    private static array $runtimeRolePermissions = [];
    private static bool $runtimePermissionsBooted = false;

    public static function bootRolePermissions(array $matrix): void
    {
        self::$runtimeRolePermissions = self::normalizeRuntimePermissions($matrix);
        self::$runtimePermissionsBooted = true;
    }

    public static function clearRolePermissions(): void
    {
        self::$runtimeRolePermissions = [];
        self::$runtimePermissionsBooted = false;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function can(string $module, string $action = 'view', ?array $user = null): bool
    {
        $user ??= self::user();

        if (!$user) {
            return false;
        }

        $role = strtolower(trim((string) ($user['role'] ?? '')));
        $module = strtolower(trim($module));
        $action = strtolower(trim($action));

        if ($role === 'superadmin') {
            return true;
        }

        if ($module === 'dashboard' && $action === 'view') {
            return true;
        }

        if ($module === 'profile' && in_array($action, ['view', 'edit'], true)) {
            return true;
        }

        $runtimePermissions = self::$runtimePermissionsBooted ? (self::$runtimeRolePermissions[$role] ?? []) : [];
        $fallbackPermissions = self::legacyPermissionsForRole($role);
        $genericAction = in_array($action, self::genericActions(), true);
        $managedRole = in_array($role, self::managedRoles(), true);

        if ($managedRole && self::$runtimePermissionsBooted) {
            if (!array_key_exists($module, $runtimePermissions)) {
                if ($action === 'view' && self::moduleHasVisibleChildren($module, $user)) {
                    return true;
                }

                return false;
            }

            if ($genericAction) {
                if ($action === 'view') {
                    if (!empty($runtimePermissions[$module]['view'])) {
                        return true;
                    }

                    if (self::moduleHasVisibleChildren($module, $user)) {
                        return true;
                    }
                }

                return !empty($runtimePermissions[$module][$action]);
            }

            if (!empty($runtimePermissions[$module]['view'])) {
                return !empty($fallbackPermissions[$module][$action] ?? false);
            }

            return false;
        }

        if ($genericAction) {
            if (array_key_exists($module, $runtimePermissions)) {
                if ($action === 'view') {
                    if (!empty($runtimePermissions[$module]['view'])) {
                        return true;
                    }

                    if (self::moduleHasVisibleChildren($module, $user)) {
                        return true;
                    }
                }

                return !empty($runtimePermissions[$module][$action]);
            }

            if ($action === 'view' && self::moduleHasVisibleChildren($module, $user)) {
                return true;
            }

            return !empty($fallbackPermissions[$module][$action] ?? false);
        }

        return !empty($fallbackPermissions[$module][$action] ?? false);
    }

    public static function visibleModules(array $modules, ?array $user = null): array
    {
        return array_filter(
            $modules,
            fn (array $module, string $key): bool => self::can($key, 'view', $user),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @return array<string, array<string, array<string, bool>>>
     */
    private static function normalizeRuntimePermissions(array $matrix): array
    {
        $normalized = [];

        foreach ($matrix as $role => $modules) {
            $roleKey = strtolower(trim((string) $role));
            if ($roleKey === '') {
                continue;
            }

            foreach ((array) $modules as $moduleKey => $actions) {
                $moduleKey = strtolower(trim((string) $moduleKey));
                if ($moduleKey === '') {
                    continue;
                }

                $normalized[$roleKey][$moduleKey] = self::normalizeActionMap((array) $actions);
            }
        }

        return $normalized;
    }

    /**
     * @return array<string, bool>
     */
    private static function normalizeActionMap(array $actions): array
    {
        $map = [];

        foreach ($actions as $key => $value) {
            if (is_int($key)) {
                $map[strtolower(trim((string) $value))] = true;
                continue;
            }

            $map[strtolower(trim((string) $key))] = (bool) $value;
        }

        return $map;
    }

    /**
     * @return array<string, array<string, bool>>
     */
    private static function legacyPermissionsForRole(string $role): array
    {
        $role = strtolower(trim($role));
        $legacy = self::ROLE_PERMISSIONS[$role] ?? [];
        $normalized = [];

        foreach ($legacy as $module => $actions) {
            $normalized[strtolower(trim((string) $module))] = self::normalizeActionMap((array) $actions);
        }

        return $normalized;
    }

    private static function moduleHasVisibleChildren(string $module, ?array $user = null): bool
    {
        $user ??= self::user();
        if (!$user) {
            return false;
        }

        $role = strtolower(trim((string) ($user['role'] ?? '')));
        $modules = Modules::all();
        $definition = $modules[$module] ?? null;

        if (!$definition) {
            return false;
        }

        $children = array_merge(
            $definition['children'] ?? [],
            $definition['role_children'][$role] ?? []
        );

        foreach ($children as $child) {
            $childKey = strtolower(trim((string) ($child['permission_key'] ?? '')));
            if ($childKey !== '' && $childKey !== $module && self::can($childKey, 'view', $user)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private static function genericActions(): array
    {
        return ['view', 'create', 'edit', 'delete', 'upload', 'print', 'export', 'manage'];
    }

    /**
     * @return array<int, string>
     */
    private static function managedRoles(): array
    {
        return ['admin', 'teacher', 'accountant', 'receptionist'];
    }
}
