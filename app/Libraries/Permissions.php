<?php

namespace App\Libraries;

class Permissions
{
    /**
     * General application permissions.
     */
    private const MATRIX = [
        'archived' => [
            'ADMIN' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'WRITER' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'EDITOR' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
        ],

        'article' => [
            'ADMIN' => [
                'view'    => true,
                'create'  => false,
                'edit'    => false,
                'delete'  => false,
                'archive' => false,
            ],
            'WRITER' => [
                'view'    => true,
                'create'  => true,
                'edit'    => true,
                'delete'  => true,
                'archive' => false,
            ],
            'EDITOR' => [
                'view'    => true,
                'create'  => true,  // CHANGED — editors can now create articles
                'edit'    => true,
                'delete'  => false,
                'archive' => true,
            ],
        ],

        // =====================================================
        // USERS (Admin > User Management page)
        // =====================================================
        //
        // This is intentionally separate from MAINTENANCE_MATRIX
        // below — that matrix is keyed by maintenance *tabs*
        // (slants, categories, etc.) and has no 'users' entry,
        // so looking permissions up there for user management
        // always fell through to denyAll(), even for ADMIN.
        'users' => [
            'ADMIN' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => false,
            ],
            'WRITER' => [
                'view'   => false,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'EDITOR' => [
                'view'   => false,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
        ],
    ];


    /**
     * Maintenance permissions.
     *
     * ADMIN
     * - Full CRUD on all maintenance modules.
     *
     * WRITER
     * - View only.
     *
     * EDITOR
     * - Category: View + Create + Edit
     * - Sub-Category: View + Create + Edit
     * - Government Offices: View + Create + Edit
     * - Everything else: View only
     * - No delete permission
     */
    private const MAINTENANCE_MATRIX = [

        // =====================================================
        // ADMIN
        // =====================================================
        'ADMIN' => [
            'slants' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'categories' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'subcategories' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'departments' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'types' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'mediums' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'programs' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'stations' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
            'reporters' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
            ],
        ],


        // =====================================================
        // WRITER - VIEW ONLY
        // =====================================================
        'WRITER' => [
            'slants' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'categories' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'subcategories' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'departments' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'types' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'mediums' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'programs' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'stations' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
            'reporters' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
        ],


        // =====================================================
        // EDITOR
        // =====================================================
        'EDITOR' => [

            'slants' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],

            'categories' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => false,
            ],

            'subcategories' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => false,
            ],

            // Government Offices
            'departments' => [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => false,
            ],

            'types' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],

            'mediums' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],

            'programs' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],

            'stations' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],

            'reporters' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
            ],
        ],
    ];


    /**
     * Check a general module permission.
     */
    public static function can(
        string $module,
        string $ability,
        ?string $role = null
    ): bool {
        $role = self::normalizeRole($role);

        return self::MATRIX[$module][$role][$ability] ?? false;
    }


    /**
     * Get all general permissions for a module.
     */
    public static function all(
        string $module,
        ?string $role = null
    ): array {
        $role = self::normalizeRole($role);

        return self::MATRIX[$module][$role]
            ?? self::denyAll();
    }


    /**
     * Check permission for a specific maintenance tab.
     */
    public static function canMaintenance(
        string $tab,
        string $ability,
        ?string $role = null
    ): bool {
        $role = self::normalizeRole($role);
        $tab  = self::normalizeTab($tab);

        return self::MAINTENANCE_MATRIX[$role][$tab][$ability]
            ?? false;
    }


    /**
     * Get all permissions for a maintenance tab.
     */
    public static function maintenance(
        string $tab,
        ?string $role = null
    ): array {
        $role = self::normalizeRole($role);
        $tab  = self::normalizeTab($tab);

        return self::MAINTENANCE_MATRIX[$role][$tab]
            ?? self::denyAll();
    }


    /**
     * Normalize role.
     */
    private static function normalizeRole(?string $role): string
    {
        $role ??= (string) session('role');

        return strtoupper(trim($role));
    }


    /**
     * Normalize maintenance tab.
     */
    private static function normalizeTab(string $tab): string
    {
        return strtolower(trim($tab));
    }


    /**
     * Default deny.
     */
    private static function denyAll(): array
    {
        return [
            'view'   => false,
            'create' => false,
            'edit'   => false,
            'delete' => false,
        ];
    }
}