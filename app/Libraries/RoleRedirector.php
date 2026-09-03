<?php

namespace App\Libraries;

class RoleRedirector
{
    /**
     * IMPORTANT: these must be real route URLs, not view names.
     * Each role has its own filtered route group in routes.php
     * (role:ADMIN, role:WRITER, role:EDITOR) — so each role must land on
     * its own prefixed route, or RoleFilter will reject it as unauthorized.
     * All three controllers (AdminController, WriterController,
     * EditorController) happen to render the same `UserDashboard` view,
     * but that's a view-layer detail and has nothing to do with the URL.
     */
    private const ROUTES = [
        'ADMIN'  => 'admin/dashboard',
        'WRITER' => 'writer/dashboard',
        'EDITOR' => 'editor/dashboard',
    ];

    private const DEFAULT_ROUTE = '/';

    public static function urlFor(string $role): string
    {
        return self::ROUTES[strtoupper($role)] ?? self::DEFAULT_ROUTE;
        return site_url($route);
    }
}
