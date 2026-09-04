<?php
    $role = strtoupper(session('role') ?? '');
    $isAdmin = $role === 'ADMIN';
    $isWriter = $role === 'WRITER';
    $dashboardHref = site_url(ltrim(\App\Libraries\RoleRedirector::urlFor($role), '/'));
    $isArchived = strtolower((string) ($article['status'] ?? '')) === 'archived';
    $decodeMultiple = static function ($value): array {
        if (
            $value === null ||
            $value === ''
        ) {
            return [];
        }
        $decoded = json_decode(
            (string) $value,
            true
        );
        if (is_array($decoded)) {
            return array_values(
                array_filter(
                    array_map(
                        static fn ($item) =>
                            trim((string) $item),
                        $decoded
                    ),
                    static fn ($item) =>
                        $item !== ''
                )
            );
        }
        return [
            trim((string) $value)
        ];
    };
    $selectedSubCategories =
        $decodeMultiple(
            $article['sub_category'] ?? null
        );
    $selectedGovernmentOffices =
        $decodeMultiple(
            $article['gov_offices'] ?? null
        );
    $selectedReporters =
        $decodeMultiple(
            $article['reporter'] ?? null
        );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title>MMS – View Article</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" >
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css" >
    <style>
        :root{
            --navy:      #0e2c52;
            --blue-700:  #1c5fc4;
            --blue-600:  #2f6fe0;
            --blue-500:  #4c8bf5;
            --blue-100:  #e6f0fd;
            --sky-200:   #cfe2fb;
            --ink-700:   #2c3e58;
            --ink-400:   #7c8aa3;
            --bg:        #eef3f9;
            --teal-600:  #0f9d8c;
            --line:      #e3ebf5;
        }
        body {
            background: var(--bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--ink-700);
        }
        .navbar {
            background: #fff;
            border-bottom: 1px solid var(--line);
            padding: 0 24px;
        }
        .navbar-brand {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--navy) !important;
            letter-spacing: .2px;
        }
        .navbar-brand i {
            color: var(--blue-700);
        }
        .nav-link {
            color: var(--ink-400) !important;
            font-weight: 500;
            font-size: .92rem;
            padding: 18px 14px !important;
            border-bottom: 2px solid transparent;
            transition: color .15s ease, border-color .15s ease;
        }
        .nav-link:hover,
        .nav-link.active {
            color: var(--blue-700) !important;
            border-bottom: 2px solid var(--blue-700);
        }
        .dropdown-menu {
            border: 1px solid var(--line);
            box-shadow: 0 10px 30px rgba(14,44,82,.1);
            border-radius: 10px;
            padding: 6px;
        }
        .dropdown-item {
            border-radius: 6px;
            font-size: .88rem;
            padding: 8px 12px;
        }
        .dropdown-item:hover {
            color: var(--blue-700);
            background: var(--blue-100);
        }
        .badge-version {
            font-size: .7rem;
            padding: 3px 7px;
            vertical-align: middle;
            background: var(--teal-600) !important;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--blue-700);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: .85rem;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            font-size: .85rem;
        }
        .breadcrumb-item a {
            color: var(--blue-700);
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: var(--ink-400);
        }
        .section-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 14px rgba(14,44,82,.06);
            border: 1px solid var(--line);
            padding: 20px;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 14px;
        }
        #editor {
            height: 280px;
            font-size: .93rem;
        }
        .ql-toolbar {
            border-radius: 6px 6px 0 0;
            border-color: var(--line) !important;
        }
        .ql-container {
            border-radius: 0 0 6px 6px;
            font-size: .93rem;
            border-color: var(--line) !important;
        }
        .word-count {
            font-size: .75rem;
            color: var(--ink-400);
            text-align: right;
            margin-top: 4px;
        }
        .form-label {
            font-size: .83rem;
            font-weight: 600;
            color: var(--ink-700);
            margin-bottom: 4px;
        }
        .form-select,
        .form-control {
            font-size: .88rem;
            border-radius: 6px;
            border-color: var(--line);
        }
        .form-select:focus,
        .form-control:focus {
            border-color: var(--blue-500);
            box-shadow: 0 0 0 .18rem rgba(76,139,245,.18);
        }
        .broadcast-time-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .broadcast-display {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--blue-700);
            color: #fff;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: .85rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .bc-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
            animation: bcpulse 1s infinite;
        }
        .status-badge {
            font-size: .78rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .status-submitted {
            background: var(--blue-100);
            color: var(--blue-700);
        }
        .status-archived {
            background: var(--bg);
            color: var(--ink-400);
        }
        .article-id-badge {
            background: var(--blue-100);
            border: 1px solid var(--sky-200);
            color: var(--blue-700);
            border-radius: 6px;
            padding: 4px 12px;
            font-size: .85rem;
            font-weight: 700;
        }
        .btn-submit {
            background: var(--blue-700);
            border: none;
            color: #fff;
            font-size: .85rem;
            padding: 7px 18px;
            border-radius: 6px;
            transition: background .15s ease;
        }
        .btn-submit:hover {
            background: var(--blue-600);
            color: #fff;
        }
        .btn-submit:disabled {
            opacity: .6;
            cursor: not-allowed;
        }
        .btn-archive {
            background: #e67e22;
            border: none;
            color: #fff;
            font-size: .85rem;
            padding: 7px 18px;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-archive:hover {
            background: #d35400;
        }
        .btn-back {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--ink-700);
            font-size: .85rem;
            padding: 7px 18px;
            border-radius: 6px;
            transition: background .15s ease;
        }
        .btn-back:hover {
            background: var(--blue-100);
            color: var(--blue-700);
        }
        footer {
            font-size: .8rem;
            color: var(--ink-400);
            padding: 20px 0;
        }
        .bell-badge {
            position: relative;
        }
        .bell-badge .fa-bell {
            color: var(--ink-400) !important;
        }
        .bell-badge .badge {
            position: absolute;
            top: -4px;
            right: -6px;
            font-size: .6rem;
            background: #e0483f !important;
        }
        fieldset[disabled] .form-select,
        fieldset[disabled] .form-control {
            background: var(--bg);
        }
        .search-help {
            display: block;
            margin-top: 5px;
            color: var(--ink-400);
            font-size: .75rem;
        }
        .ts-control {
            min-height: 38px;
            font-size: .88rem;
            border-radius: 6px !important;
            border-color: var(--line) !important;
        }
        .ts-control input {
            font-size: .88rem;
        }
        .ts-dropdown {
            font-size: .88rem;
        }
        .ts-wrapper.multi .ts-control > div {
            background: var(--blue-100);
            border: 1px solid var(--sky-200);
            color: var(--blue-700);
            border-radius: 4px;
            padding: 3px 7px;
            margin: 2px 3px 2px 0;
        }
        .ts-wrapper.multi .ts-control > div .remove {
            color: var(--blue-700);
            border-left: 1px solid var(--sky-200);
            margin-left: 5px;
        }
        .ts-wrapper.disabled .ts-control {
            background: var(--bg);
            opacity: 1;
        }
        @keyframes bcpulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .3; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

        <a class="navbar-brand" href="#">
            <i class="fa-solid fa-fire me-1"></i>
            MMS
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">

            <ul class="navbar-nav me-auto">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $dashboardHref ?>">
                        <i class="fa fa-gauge me-1"></i>
                        Dashboard
                    </a>
                </li>

                <!-- Applications -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#"
                       data-bs-toggle="dropdown">
                        <i class="fa fa-th me-1"></i>
                        Applications
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="<?= site_url('news') ?>">
                                <i class="fa fa-newspaper me-2 text-muted"></i>
                                News
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= site_url('archived') ?>">
                                <i class="fa fa-archive me-2 text-muted"></i>
                                Archives
                            </a>
                        </li>

                        <?php if ($isAdmin): ?>
                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('admin/users') ?>">
                                    <i class="fa fa-users me-2 text-muted"></i>
                                    User Management
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>
                </li>

                <!-- Maintenance -->
                <?php if ($isAdmin): ?>
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#"
                           data-bs-toggle="dropdown">
                            <i class="fa fa-cog me-1"></i>
                            Maintenance
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/slants') ?>">
                                    <i class="fa fa-tag me-2 text-muted"></i>
                                    Slant
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/categories') ?>">
                                    <i class="fa fa-list me-2 text-muted"></i>
                                    Category
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/subcategories') ?>">
                                    <i class="fa fa-list-ul me-2 text-muted"></i>
                                    SubCategory
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/departments') ?>">
                                    <i class="fa fa-building me-2 text-muted"></i>
                                    Department
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/types') ?>">
                                    <i class="fa fa-file-alt me-2 text-muted"></i>
                                    Type
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/mediums') ?>">
                                    <i class="fa fa-broadcast-tower me-2 text-muted"></i>
                                    Medium
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/programs') ?>">
                                    <i class="fa fa-tv me-2 text-muted"></i>
                                    Program
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/stations') ?>">
                                    <i class="fa fa-satellite-dish me-2 text-muted"></i>
                                    Station
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance/reporters') ?>">
                                    <i class="fa fa-user-tie me-2 text-muted"></i>
                                    Reporter
                                </a>
                            </li>

                        </ul>
                    </li>
                <?php endif ?>

                <!-- Revision -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa fa-rotate me-1"></i>
                        Revision
                        <span class="badge badge-version">V4.0</span>
                    </a>
                </li>

            </ul>

            <!-- Right Navigation -->
            <ul class="navbar-nav align-items-center gap-2">

                <!-- Notifications -->
                <li class="nav-item bell-badge">
                    <a class="nav-link" href="#">
                        <i class="fa fa-bell fa-lg"></i>
                        <span class="badge">3</span>
                    </a>
                </li>

                <!-- User Avatar -->
                <li class="nav-item">
                    <div class="user-avatar" id="userInitials">
                        <?= esc(
                            strtoupper(
                                substr(session('first_name') ?? 'U', 0, 1) .
                                substr(session('last_name') ?? 'U', 0, 1)
                            )
                        ) ?>
                    </div>
                </li>

                <!-- User Menu -->
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle fw-semibold"
                       href="#"
                       data-bs-toggle="dropdown"
                       id="userName">

                        <?= esc(
                            strtoupper(
                                trim(
                                    (session('first_name') ?? '') . ' ' .
                                    (session('last_name') ?? '')
                                )
                            ) ?: $role
                        ) ?>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <?php if ($isAdmin): ?>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('admin/users') ?>">
                                    <i class="fa fa-users me-2"></i>
                                    User Management
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="<?= site_url('maintenance') ?>">
                                    <i class="fa fa-cog me-2"></i>
                                    Maintenance
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                        <?php endif ?>

                        <li>
                            <a class="dropdown-item text-danger"
                               href="<?= site_url('auth/logout') ?>">
                                <i class="fa fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>

        </div>
    </div>
</nav>
<div class="container-fluid px-4 py-3">

    <!-- BREADCRUMB -->

    <nav aria-label="breadcrumb" class="mb-3" >

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a href="<?= $dashboardHref ?>">
                    Forms
                </a>

            </li>

            <li class="breadcrumb-item">

                <a href="<?= $dashboardHref ?>">
                    News
                </a>

            </li>

            <li class="breadcrumb-item active">View Article</li>

        </ol>

    </nav>

    <!-- LOCK BANNER -->

    <?php if (! empty($isLocked)): ?>

        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">

            <i class="fa fa-lock"></i>

            <div>
                This article is currently being edited by
                <strong><?= esc($lockedByName ?? 'another editor') ?></strong>.
                You're viewing a read-only copy — changes here won't be saved.
            </div>

        </div>

    <?php endif ?>

    <!-- ARTICLE HEADER -->

    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">

        <span class="article-id-badge">

            <i class="fa fa-hashtag me-1"></i>

            <?= esc($article['id']) ?>

        </span>

        <span
            class="status-badge
            <?= $isArchived
                ? 'status-archived'
                : 'status-submitted'
            ?>"
        >

            <?= esc(
                ucfirst(
                    $article['status']
                )
            ) ?>

        </span>

        <span class="text-muted" style="font-size:.85rem;" >

            <?php if (! empty($isLocked)): ?>

                Read-only while another editor has this open.

            <?php elseif ($isArchived): ?>

                This article is archived and can no longer be edited.

            <?php else: ?>

                Review and edit the content and metadata below.

            <?php endif ?>

        </span>

    </div>

    <div class="row g-3">
        <div class="col-lg-7">

            <fieldset <?= $canEdit ? '' : 'disabled' ?>>

                <!-- CONTENT -->

                <div class="section-card mb-3">

                    <div class="section-title">
                        Content
                    </div>

                    <div id="editor">
                        <?= $article['content'] ?? '' ?>
                    </div>

                    <div class="word-count">

                        <span id="wordCount">0</span>

                        WORDS

                    </div>

                </div>

                <!-- CLASSIFICATION -->

                <div class="section-card">

                    <div class="section-title">
                        Classification
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">

                            <label class="form-label">Category</label>

                            <select class="form-select" id="category" >

                                <option value="">
                                    Nothing Selected
                                </option>

                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?= esc($category['category_name']) ?>"
                                        <?= (
                                            ($article['category'] ?? '')
                                            ===
                                            $category['category_name']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $category['category_name']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-md-6">

                            <label class="form-label">Sub-Category</label>

                            <select id="subCategory" name="subCategory[]" multiple >

                                <?php foreach ($subCategories as $subCategory): ?>

                                    <option
                                        value="<?= esc(
                                            $subCategory['sub_category']
                                        ) ?>"
                                        <?= in_array(
                                            $subCategory['sub_category'],
                                            $selectedSubCategories,
                                            true
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $subCategory['sub_category']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                            <small class="search-help">

                                <i class="fa fa-magnifying-glass me-1"></i>

                                Search and select one or more
                                Sub-Categories.

                            </small>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Government Offices</label>

                            <select id="govOffices" name="govOffices[]" multiple >

                                <?php foreach ($departments as $department): ?>

                                    <option
                                        value="<?= esc(
                                            $department['department_name']
                                        ) ?>"
                                        <?= in_array(
                                            $department['department_name'],
                                            $selectedGovernmentOffices,
                                            true
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $department['department_name']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                            <small class="search-help">

                                <i class="fa fa-magnifying-glass me-1"></i>

                                Search and select one or more
                                Government Offices.

                            </small>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Remarks</label>

                            <textarea class="form-control" id="remarks" rows="3" ><?= esc(
                                $article['remarks'] ?? ''
                            ) ?></textarea>

                        </div>

                    </div>

                </div>

            </fieldset>

        </div>
        <div class="col-lg-5">

            <div class="section-card">

                <div class="section-title">
                    Metadata
                </div>

                <!-- EDIT TIME -->
                <?php
                    $eds = $article['editing_start'] ?? null;
                    $hasEditStart = $eds && is_numeric($eds) && (int) $eds > 0;
                ?>

                <div class="broadcast-time-row">

                    Edit Time

                    <div class="broadcast-display">

                        <?php if (! $isArchived): ?>
                        <span class="bc-dot"></span>
                        <?php endif ?>

                        <i class="fa fa-stopwatch me-1"></i>

                        <strong id="editingTimer">
                            <?php if ($isArchived && ! empty($article['editing_end']) && is_numeric($article['editing_end']) && (int) $article['editing_end'] > 0 && $hasEditStart): ?>
                                <?php
                                    $secs = max(0, (int) $article['editing_end'] - (int) $eds);
                                    echo str_pad((int) floor($secs / 3600), 2, '0', STR_PAD_LEFT)
                                        . ':' . str_pad((int) floor(($secs % 3600) / 60), 2, '0', STR_PAD_LEFT)
                                        . ':' . str_pad($secs % 60, 2, '0', STR_PAD_LEFT);
                                ?>
                            <?php else: ?>
                                00:00:00
                            <?php endif ?>
                        </strong>

                    </div>

                </div>

                <fieldset <?= $canEdit ? '' : 'disabled' ?>>

                    <div class="row g-2">
                        <div class="col-12">

                            <label class="form-label">Slant</label>

                            <select class="form-select" id="slant" >

                                <option value="">
                                    Nothing Selected
                                </option>

                                <?php foreach ($slants as $slant): ?>

                                    <option
                                        value="<?= esc(
                                            $slant['slant_name']
                                        ) ?>"
                                        <?= (
                                            ($article['slant'] ?? '')
                                            ===
                                            $slant['slant_name']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $slant['slant_name']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Type</label>

                            <select class="form-select" id="type" >

                                <option value="">
                                    Nothing Selected
                                </option>

                                <?php foreach ($types as $type): ?>

                                    <option
                                        value="<?= esc(
                                            $type['type_name']
                                        ) ?>"
                                        <?= (
                                            ($article['type'] ?? '')
                                            ===
                                            $type['type_name']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $type['type_name']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Medium</label>

                            <select class="form-select" id="medium" >

                                <option value="">
                                    Nothing Selected
                                </option>

                                <?php foreach ($mediums as $medium): ?>

                                    <option
                                        value="<?= esc(
                                            $medium['medium_name']
                                        ) ?>"
                                        <?= (
                                            ($article['medium'] ?? '')
                                            ===
                                            $medium['medium_name']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $medium['medium_name']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Station</label>

                            <select class="form-select" id="station" >

                                <option value="">
                                    Nothing Selected
                                </option>

                                <?php foreach ($stations as $station): ?>

                                    <option
                                        value="<?= esc(
                                            $station['station_name']
                                        ) ?>"
                                        <?= (
                                            ($article['station'] ?? '')
                                            ===
                                            $station['station_name']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            trim(
                                                ($station['station_from'] ?? '')
                                                . ' - '
                                                . $station['station_name']
                                            )
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Program</label>

                            <select class="form-select" id="program" >

                                <option value="">
                                    Nothing Selected
                                </option>

                                <?php foreach ($programs as $program): ?>

                                    <option
                                        value="<?= esc(
                                            $program['program_name']
                                        ) ?>"
                                        <?= (
                                            ($article['program'] ?? '')
                                            ===
                                            $program['program_name']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            trim(
                                                ($program['from_name'] ?? '')
                                                . ' - '
                                                . $program['program_name']
                                            )
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Anchor/Reporter</label>

                            <select id="reporter" name="reporter[]" multiple >

                                <?php foreach ($reporters as $reporter): ?>

                                    <option
                                        value="<?= esc(
                                            $reporter['reporter_name']
                                        ) ?>"
                                        <?= in_array(
                                            $reporter['reporter_name'],
                                            $selectedReporters,
                                            true
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= esc(
                                            $reporter['reporter_name']
                                        ) ?>

                                    </option>

                                <?php endforeach ?>

                            </select>

                            <small class="search-help">

                                <i class="fa fa-magnifying-glass me-1"></i>

                                Search and select one or more
                                Anchors/Reporters.

                            </small>

                        </div>
                        <div class="col-12">

                            <label class="form-label">Alert</label>

                            <select class="form-select" id="alert" >

                                <option
                                    value="No"
                                    <?= (
                                        ($article['alert'] ?? 'No')
                                        === 'No'
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    No
                                </option>

                                <option
                                    value="Yes"
                                    <?= (
                                        ($article['alert'] ?? '')
                                        === 'Yes'
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Yes
                                </option>

                            </select>

                        </div>

                    </div>

                </fieldset>
                <div class="col-12 mt-4 d-flex gap-2 flex-wrap">

                    <?php if ($canEdit): ?>

                        <button class="btn-submit" id="saveBtn" onclick="saveArticle()" >

                            <i class="fa fa-check me-1"></i>

                            Save Changes

                        </button>

                    <?php endif ?>

                    <?php if
                      ( $canArchive &&
                        ! $isArchived
                    ): ?>

                        <button class="btn-archive" id="archiveBtn" onclick="confirmArchive()" >

                            <i class="fa fa-box-archive me-1"></i>

                            Submit to Archive

                        </button>

                    <?php endif ?>

                    <button class="btn-back" onclick="goBack()" >

                        <i class="fa fa-arrow-left me-1"></i>

                        Back to Listing

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="archiveModal" tabindex="-1" >

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header border-0">

                <h5 class="modal-title text-warning">

                    <i class="fa fa-box-archive me-2"></i>

                    Confirm Archive

                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>

            </div>

            <div class="modal-body">

                Archiving

                <strong>
                    <?= esc($article['id']) ?>
                </strong>

                will lock it from further edits.

                Continue?

            </div>

            <div class="modal-footer border-0">

                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" >Cancel</button>

                <button class="btn btn-warning btn-sm text-white" onclick="archiveArticle()" >Archive</button>

            </div>

        </div>

    </div>

</div>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999" >

    <div id="toastMsg" class="toast align-items-center text-bg-success border-0" role="alert" >

        <div class="d-flex">

            <div class="toast-body" id="toastText" >
                Saved successfully!
            </div>

            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" ></button>

        </div>

    </div>

</div>
<div class="container-fluid px-4">

    <footer class="d-flex justify-content-between">

        <span>Indulged by MISD © 2020</span>

        <span>

            Follow us

            &nbsp;

            <i class="fab fa-facebook"></i>

            &nbsp;

            <i class="fab fa-twitter"></i>

            &nbsp;

            <i class="fab fa-google-plus-g"></i>

        </span>

    </footer>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js" ></script>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js" ></script>

<script>

    const ARTICLE_ID =
        <?= json_encode($article['id']) ?>;

    const UPDATE_URL =
        '<?= site_url('editor/view-article') ?>/'
        + ARTICLE_ID
        + '/update';

    const ARCHIVE_URL =
        '<?= site_url('editor/view-article') ?>/'
        + ARTICLE_ID
        + '/archive';

    const HEARTBEAT_URL =
        '<?= site_url('editor/view-article') ?>/'
        + ARTICLE_ID
        + '/lock-heartbeat';

    const RELEASE_URL =
        '<?= site_url('editor/view-article') ?>/'
        + ARTICLE_ID
        + '/lock-release';

    const LISTING_URL =
        <?= json_encode($dashboardHref) ?>;

    const CAN_EDIT =
        <?= json_encode($canEdit) ?>;

    const IS_ARCHIVED =
        <?= json_encode($isArchived) ?>;

    const IS_LOCKED =
        <?= json_encode($isLocked ?? false) ?>;

    let quill = null;

    let subCategorySelect = null;

    let govOfficesSelect = null;

    let reporterSelect = null;

    let lockHeartbeatTimer = null;

    function csrfHeaders()
    {
        const name =
            document
                .querySelector(
                    'meta[name="csrf-name"]'
                )
                .content;

        const token =
            document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                .content;

        return {
            [name]: token
        };
    }

    function goBack()
    {
        window.location.href =
            LISTING_URL;
    }

    function showToast(
        msg,
        type = 'success'
    )
    {
        const toast =
            document.getElementById(
                'toastMsg'
            );

        toast.className =
            `toast align-items-center text-bg-${type} border-0`;

        document.getElementById(
            'toastText'
        ).textContent = msg;

        new bootstrap.Toast(
            toast,
            {
                delay: 2500
            }
        ).show();
    }

    /*
     * =========================================================
     * LOCK HEARTBEAT / RELEASE
     * =========================================================
     *
     * Only runs when we actually hold the edit lock (CAN_EDIT
     * and not IS_LOCKED). Keeps the lock alive every 60s, well
     * under the server-side 300s TTL. If a heartbeat fails, the
     * lock expired and someone else took it — reload read-only.
     */
    function startLockHeartbeat()
    {
        if (IS_LOCKED || !CAN_EDIT) {
            return;
        }

        lockHeartbeatTimer = setInterval(
            async () => {

                try {

                    const form = new FormData();

                    Object.entries(csrfHeaders())
                        .forEach(([key, value]) => form.append(key, value));

                    const response = await fetch(
                        HEARTBEAT_URL,
                        {
                            method: 'POST',
                            body: form,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    const json = await response.json();

                    if (json.csrfToken) {

                        const csrfMeta = document.querySelector(
                            'meta[name="csrf-token"]'
                        );

                        if (csrfMeta) {
                            csrfMeta.content = json.csrfToken;
                        }
                    }

                    if (!json.status) {

                        clearInterval(lockHeartbeatTimer);

                        showToast(
                            'Someone else is now editing this article. Reloading as read-only.',
                            'danger'
                        );

                        setTimeout(
                            () => window.location.reload(),
                            2000
                        );
                    }

                }
                catch (error) {

                    console.error(
                        'Lock heartbeat failed:',
                        error
                    );
                }

            },
            60000
        );
    }

    function releaseLockBeacon()
    {
        if (IS_LOCKED || !CAN_EDIT) {
            return;
        }

        const form = new FormData();

        Object.entries(csrfHeaders())
            .forEach(([key, value]) => form.append(key, value));

        navigator.sendBeacon(RELEASE_URL, form);
    }

    window.addEventListener('beforeunload', releaseLockBeacon);
    window.addEventListener('pagehide', releaseLockBeacon);

    function initializeSearchableSelects()
    {
        subCategorySelect =
            new TomSelect(
                '#subCategory',
                {

                    plugins: [
                        'remove_button'
                    ],

                    placeholder:
                        'Search Sub-Category...',

                    searchField: [
                        'text'
                    ],

                    valueField:
                        'value',

                    labelField:
                        'text',

                    maxItems:
                        null,

                    create:
                        false,

                    persist:
                        false,

                    hideSelected:
                        true,

                    closeAfterSelect:
                        false,

                    allowEmptyOption:
                        false,

                    selectOnTab:
                        true,

                    disabled:
                        !CAN_EDIT

                }
            );

        govOfficesSelect =
            new TomSelect(
                '#govOffices',
                {

                    plugins: [
                        'remove_button'
                    ],

                    placeholder:
                        'Search Government Offices...',

                    searchField: [
                        'text'
                    ],

                    valueField:
                        'value',

                    labelField:
                        'text',

                    maxItems:
                        null,

                    create:
                        false,

                    persist:
                        false,

                    hideSelected:
                        true,

                    closeAfterSelect:
                        false,

                    allowEmptyOption:
                        false,

                    selectOnTab:
                        true,

                    disabled:
                        !CAN_EDIT

                }
            );

        reporterSelect =
            new TomSelect(
                '#reporter',
                {

                    plugins: [
                        'remove_button'
                    ],

                    placeholder:
                        'Search Anchor/Reporter...',

                    searchField: [
                        'text'
                    ],

                    valueField:
                        'value',

                    labelField:
                        'text',

                    maxItems:
                        null,

                    create:
                        false,

                    persist:
                        false,

                    hideSelected:
                        true,

                    closeAfterSelect:
                        false,

                    allowEmptyOption:
                        false,

                    selectOnTab:
                        true,

                    disabled:
                        !CAN_EDIT

                }
            );

    }

    async function saveArticle()
    {

        if (!CAN_EDIT) {
            return;
        }

        const content =
            quill.root.innerHTML.trim();

        if (
            !content ||
            content === '<p><br></p>'
        ) {

            showToast(
                'Content cannot be empty.',
                'danger'
            );

            return;
        }

        const button =
            document.getElementById(
                'saveBtn'
            );

        button.disabled = true;

        const form =
            new FormData();

        form.append(
            'content',
            content
        );

        form.append(
            'category',
            document.getElementById(
                'category'
            ).value
        );

        form.append(
            'remarks',
            document.getElementById(
                'remarks'
            ).value
        );

        form.append(
            'slant',
            document.getElementById(
                'slant'
            ).value
        );

        form.append(
            'type',
            document.getElementById(
                'type'
            ).value
        );

        form.append(
            'medium',
            document.getElementById(
                'medium'
            ).value
        );

        form.append(
            'station',
            document.getElementById(
                'station'
            ).value
        );

        form.append(
            'program',
            document.getElementById(
                'program'
            ).value
        );

        form.append(
            'alert',
            document.getElementById(
                'alert'
            ).value
        );

        const selectedSubCategories =
            subCategorySelect
                ? subCategorySelect.getValue()
                : [];

        selectedSubCategories.forEach(
            value => {

                form.append(
                    'subCategory[]',
                    value
                );

            }
        );

        const selectedGovernmentOffices =
            govOfficesSelect
                ? govOfficesSelect.getValue()
                : [];

        selectedGovernmentOffices.forEach(
            value => {

                form.append(
                    'govOffices[]',
                    value
                );

            }
        );

        const selectedReporters =
            reporterSelect
                ? reporterSelect.getValue()
                : [];

        selectedReporters.forEach(
            value => {

                form.append(
                    'reporter[]',
                    value
                );

            }
        );

        Object.entries(
            csrfHeaders()
        ).forEach(
            ([key, value]) => {

                form.append(
                    key,
                    value
                );

            }
        );

        try {

            const response =
                await fetch(
                    UPDATE_URL,
                    {
                        method: 'POST',
                        body: form,
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                );

            const contentType =
                response.headers.get(
                    'content-type'
                ) || '';

            if (
                !contentType.includes(
                    'application/json'
                )
            ) {

                throw new Error(
                    'Server did not return JSON.'
                );

            }

            const json =
                await response.json();

            if (json.csrfToken) {

                const csrfMeta =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    );

                if (csrfMeta) {

                    csrfMeta.content =
                        json.csrfToken;

                }

            }

            if (
                !response.ok ||
                !json.status
            ) {

                showToast(
                    json.message
                        || 'Something went wrong.',
                    'danger'
                );

                button.disabled =
                    false;

                // If a 423 (article locked by someone else)
                // came back, stop editing and reload so the
                // read-only banner appears.
                if (response.status === 423) {

                    setTimeout(
                        () => window.location.reload(),
                        1500
                    );

                }

                return;

            }

            showToast(
                json.message
                    || 'Article updated successfully!'
            );

            button.disabled =
                false;

        }
        catch (error) {

            console.error(error);

            showToast(
                'Network error. Please try again.',
                'danger'
            );

            button.disabled =
                false;

        }

    }

    function confirmArchive()
    {

        new bootstrap.Modal(
            document.getElementById(
                'archiveModal'
            )
        ).show();

    }

    async function archiveArticle()
    {

        try {

            const response =
                await fetch(
                    ARCHIVE_URL,
                    {
                        method: 'POST',

                        headers:
                            csrfHeaders()
                    }
                );

            const json =
                await response.json();

            const modal =
                bootstrap.Modal.getInstance(
                    document.getElementById(
                        'archiveModal'
                    )
                );

            if (modal) {
                modal.hide();
            }

            if (json.csrfToken) {

                const csrfMeta =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    );

                if (csrfMeta) {

                    csrfMeta.content =
                        json.csrfToken;

                }

            }

            if (
                !response.ok ||
                !json.status
            ) {

                showToast(
                    json.message
                        || 'Unable to archive article.',
                    'danger'
                );

                return;

            }

            showToast(
                json.message
                    || 'Article successfully archived!'
            );

            setTimeout(
                () => {

                    window.location.href =
                        '<?= site_url('archived') ?>';

                },
                1500
            );

        }
        catch (error) {

            console.error(error);

            showToast(
                'Network error. Please try again.',
                'danger'
            );

        }

    }

    function init()
    {
        quill =
            new Quill(
                '#editor',
                {

                    theme:
                        'snow',

                    readOnly:
                        !CAN_EDIT,

                    modules: {

                        toolbar:
                            CAN_EDIT
                                ? [
                                    [
                                        {
                                            header: [
                                                1,
                                                2,
                                                3,
                                                false
                                            ]
                                        }
                                    ],

                                    [
                                        'bold',
                                        'italic',
                                        'underline'
                                    ],

                                    [
                                        {
                                            align: []
                                        }
                                    ],

                                    [
                                        {
                                            list:
                                                'ordered'
                                        },

                                        {
                                            list:
                                                'bullet'
                                        }
                                    ],

                                    [
                                        'link'
                                    ],

                                    [
                                        'clean'
                                    ]

                                ]
                                : false

                    }

                }
            );

        quill.on(
            'text-change',
            () => {

                const words =
                    quill
                        .getText()
                        .trim()
                        .split(/\s+/)
                        .filter(Boolean)
                        .length;

                document.getElementById(
                    'wordCount'
                ).textContent =
                    words;

            }
        );

        document.getElementById(
            'wordCount'
        ).textContent =
            quill
                .getText()
                .trim()
                .split(/\s+/)
                .filter(Boolean)
                .length;

        initializeSearchableSelects();

        startLockHeartbeat();

        startEditingTimer();

    }

    init();

    /*
    |--------------------------------------------------------------------------
    | EDITING TIMER
    |--------------------------------------------------------------------------
    */

    function formatDuration(totalSeconds) {

        totalSeconds = Math.max(
            0,
            Math.floor(totalSeconds)
        );

        const hours =
            Math.floor(totalSeconds / 3600);

        const minutes =
            Math.floor(
                (totalSeconds % 3600) / 60
            );

        const seconds =
            totalSeconds % 60;

        return (
            String(hours).padStart(2, '0')
            + ':'
            + String(minutes).padStart(2, '0')
            + ':'
            + String(seconds).padStart(2, '0')
        );

    }

    function startEditingTimer() {

        const el =
            document.getElementById(
                'editingTimer'
            );

        if (!el) return;

        if (IS_ARCHIVED) return;

        let seconds = 0;

        const tick = () => {

            seconds++;

            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;

            el.textContent =
                String(h).padStart(2, '0')
                + ':'
                + String(m).padStart(2, '0')
                + ':'
                + String(s).padStart(2, '0');

        };

        tick();

        setInterval(tick, 1000);

    }

</script>

</body>

</html>