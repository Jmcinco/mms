<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// app/Config/Routes.php
$routes->get('archived/averages', 'ArchivedController::averages');
/*
|--------------------------------------------------------------------------
| PUBLIC / AUTH
|--------------------------------------------------------------------------
*/

$routes->get(
    '/',
    'AuthController::index'
);

$routes->post(
    'auth/login',
    'AuthController::login'
);

$routes->get(
    'auth/logout',
    'AuthController::logout'
);


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

$routes->group(
    'admin',
    [
        'filter' => 'role:ADMIN'
    ],
    static function (
        $routes
    ) {

        $routes->get(
            'dashboard',
            'AdminController::index'
        );

        $routes->get(
            'dashboard/data',
            'AdminController::dashboardData'
        );


        /*
         * USER MANAGEMENT
         */

        $routes->get(
            'users',
            'AdminController::users'
        );

        $routes->get(
            'users/data',
            'AdminController::usersData'
        );

        $routes->post(
            'users/save',
            'AdminController::usersSave'
        );

        $routes->post(
            'users/toggle-status/(:num)',
            'AdminController::usersToggleStatus/$1'
        );

    }
);


/*
|--------------------------------------------------------------------------
| WRITER
|--------------------------------------------------------------------------
|
| Writer workflow:
|
| NEW
| ↓
| entry_start saved in SESSION
| ↓
| CREATE ARTICLE
| ↓
| DRAFT or SUBMIT
|
*/

$routes->group(
    'writer',
    [
        'filter' => 'role:WRITER'
    ],
    static function (
        $routes
    ) {

        /*
         * WRITER DASHBOARD
         */

        $routes->get(
            'dashboard',
            'WriterController::index'
        );

        $routes->get(
            'dashboard/data',
            'WriterController::dashboardData'
        );

        $routes->get(
            'dashboard/list',
            'WriterController::data'
        );

        $routes->delete(
            'dashboard/delete/(:segment)',
            'WriterController::delete/$1'
        );

    }
);


/*
|--------------------------------------------------------------------------
| ARTICLE CREATION (WRITER + EDITOR)
|--------------------------------------------------------------------------
|
| WRITER and EDITOR share the same create/edit-draft screen and the
| same save endpoint. Both roles are permitted to reach this screen
| at the routing level; the CONTROLLER decides which ability applies
| ('create' vs 'edit') via Permissions::can(), and additionally
| enforces ownership rules for WRITER (see
| ArticleWriterController::save()).
|
| NOTE: this used to live under /writer/create-article. It has moved
| to a shared, unprefixed path since it is no longer writer-exclusive.
| Any bookmarks/links to the old /writer/create-article URL must be
| updated to /create-article.
|
| session('article_entry_start') is still set inside
| ArticleWriterController::create() regardless of which of these two
| roles opens the form.
*/

$routes->group(
    '',
    [
        'filter' => 'role:WRITER,EDITOR'
    ],
    static function (
        $routes
    ) {

        /*
         * -----------------------------------------------------
         * CREATE NEW ARTICLE / EDIT EXISTING DRAFT
         *
         * URL:
         *
         * /create-article
         * /create-article?id=PMU-XXXXXXXX
         *
         * The create() controller validates:
         *
         * - Permissions::can('article', 'create', $role)
         * - (for WRITER editing an existing draft) ownership
         *   and draft status
         * -----------------------------------------------------
         */

        $routes->get(
            'create-article',
            'ArticleWriterController::create'
        );


        /*
         * -----------------------------------------------------
         * SAVE DRAFT / SUBMIT ARTICLE
         *
         * POST:
         *
         * id = (blank for new article, set for an update)
         * -----------------------------------------------------
         */

        $routes->post(
            'create-article/save',
            'ArticleWriterController::save'
        );

    }
);


/*
|--------------------------------------------------------------------------
| EDITOR
|--------------------------------------------------------------------------
*/

$routes->group(
    'editor',
    [
        'filter' => 'role:EDITOR'
    ],
    static function (
        $routes
    ) {

        /*
         * EDITOR DASHBOARD
         */

        $routes->get(
            'dashboard',
            'EditorController::index'
        );

        $routes->get(
            'dashboard/data',
            'EditorController::dashboardData'
        );

        $routes->get(
            'dashboard/list',
            'EditorController::data'
        );

        $routes->delete(
            'dashboard/delete/(:segment)',
            'EditorController::delete/$1'
        );


        /*
         * EDITOR DASHBOARD DATA
         */

        $routes->get(
            'dashboard/news-type',
            'EditorController::newsTypeData'
        );

        $routes->get(
            'dashboard/monitors',
            'EditorController::monitorsList'
        );

    }
);


/*
|--------------------------------------------------------------------------
| NEWS ARTICLE LISTING
|--------------------------------------------------------------------------
|
| ADMIN
| WRITER
| EDITOR
|
*/

$routes->group(
    '',
    [
        'filter' =>
            'role:ADMIN,WRITER,EDITOR'
    ],
    static function (
        $routes
    ) {

        /*
         * NEWS LISTING
         */

        $routes->get(
            'news',
            'ArticleWriterController::index'
        );

        /*
         * NEWS DATA
         */

        $routes->get(
            'news/data',
            'ArticleWriterController::data'
        );

        /*
         * DELETE
         *
         * Backend should still enforce that
         * WRITER can only delete DRAFT articles.
         */

        $routes->delete(
            'news/delete/(:segment)',
            'ArticleWriterController::delete/$1'
        );

    }
);


/*
|--------------------------------------------------------------------------
| ARTICLE VIEW
|--------------------------------------------------------------------------
|
| ADMIN
| EDITOR
| WRITER
|
| Writer access must remain read-only.
|
*/

$routes->group(
    '',
    [
        'filter' =>
            'role:ADMIN,EDITOR,WRITER'
    ],
    static function (
        $routes
    ) {

        $routes->get(
            'editor/view-article/(:segment)',
            'ArticleViewController::view/$1'
        );

    }
);


/*
|--------------------------------------------------------------------------
| ARTICLE EDIT / ARCHIVE
|--------------------------------------------------------------------------
|
| ADMIN + EDITOR ONLY
|
*/

$routes->group(
    '',
    [
        'filter' =>
            'role:ADMIN,EDITOR'
    ],
    static function (
        $routes
    ) {

        /*
         * EDIT ARTICLE
         */

        $routes->post(
            'editor/view-article/(:segment)/update',
            'ArticleViewController::update/$1'
        );


        /*
         * ARCHIVE ARTICLE
         */

        $routes->post(
            'editor/view-article/(:segment)/archive',
            'ArticleViewController::archive/$1'
        );


        /*
         * ARTICLE LOCK
         */

        $routes->post(
            'editor/view-article/(:segment)/lock-heartbeat',
            'ArticleViewController::heartbeat/$1'
        );

        $routes->post(
            'editor/view-article/(:segment)/lock-release',
            'ArticleViewController::releaseLockAction/$1'
        );

    }
);


/*
|--------------------------------------------------------------------------
| ARCHIVED ARTICLES
|--------------------------------------------------------------------------
|
| ADMIN
| WRITER
| EDITOR
|
*/

$routes->group(
    '',
    [
        'filter' =>
            'role:ADMIN,WRITER,EDITOR'
    ],
    static function (
        $routes
    ) {

        $routes->get(
            'archived',
            'ArchivedController::index'
        );

        $routes->get(
            'archived/data',
            'ArchivedController::data'
        );

    }
);


/*
|--------------------------------------------------------------------------
| MAINTENANCE
|--------------------------------------------------------------------------
*/

$routes->group(
    '',
    [
        'filter' =>
            'role:ADMIN,WRITER,EDITOR'
    ],
    static function (
        $routes
    ) {

        $routes->get(
            'maintenance',
            'MaintenanceController::index'
        );

        $routes->get(
            'maintenance/(:segment)',
            'MaintenanceController::index/$1'
        );

        $routes->get(
            'maintenance/data/(:segment)',
            'MaintenanceController::data/$1'
        );

        $routes->post(
            'maintenance/save/(:segment)',
            'MaintenanceController::save/$1'
        );

        $routes->post(
            'maintenance/delete/(:segment)/(:num)',
            'MaintenanceController::delete/$1/$2'
        );

        $routes->post(
            'maintenance/bulk/(:segment)',
            'MaintenanceController::bulkUpload/$1'
        );

        $routes->get(
            'maintenance/bulk-template/(:segment)',
            'MaintenanceController::bulkTemplate/$1'
        );

    }
);