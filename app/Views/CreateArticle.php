<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="<?= csrf_hash() ?>"
    >

    <meta
        name="csrf-name"
        content="<?= csrf_token() ?>"
    >

    <title>MMS – Create Article</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.quilljs.com/1.3.7/quill.snow.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css"
    >

    <style>

        :root {
            --navy: #0e2c52;
            --blue-700: #1c5fc4;
            --blue-600: #2f6fe0;
            --blue-500: #4c8bf5;
            --blue-100: #e6f0fd;
            --sky-200: #cfe2fb;
            --ink-700: #2c3e58;
            --ink-400: #7c8aa3;
            --bg: #eef3f9;
            --teal-600: #0f9d8c;
            --line: #e3ebf5;
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
            font-size: 1.5rem;
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
            font-size: .93rem;
            padding: 18px 14px !important;
            border-bottom: 2px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--blue-700) !important;
            border-bottom: 2px solid var(--blue-700);
        }

        .dropdown-menu {
            border: 1px solid var(--line);
            box-shadow: 0 10px 30px rgba(14, 44, 82, .1);
            border-radius: 10px;
            padding: 6px;
        }

        .dropdown-item {
            border-radius: 6px;
        }

        .dropdown-item:hover {
            color: var(--blue-700);
            background: var(--blue-100);
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

        .section-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
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
        }

        .ql-container {
            border-radius: 0 0 6px 6px;
            font-size: .93rem;
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
            border-radius: 5px;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--blue-700);
            box-shadow: 0 0 0 .18rem rgba(28, 95, 196, .18);
        }

        .ts-wrapper {
            font-size: .88rem;
        }

        .ts-wrapper.multi .ts-control {
            min-height: 39px;
            padding: 5px 8px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            background: #fff;
        }

        .ts-wrapper.focus .ts-control {
            border-color: var(--blue-700);
            box-shadow: 0 0 0 .18rem rgba(28, 95, 196, .18);
        }

        .ts-wrapper.multi .ts-control > div.item {
            background: var(--blue-100);
            border: 1px solid var(--sky-200);
            border-radius: 4px;
            padding: 4px 8px;
            color: var(--blue-700);
        }

        .ts-wrapper.multi .ts-control > div.item.active {
            background: var(--sky-200);
        }

        .ts-wrapper.plugin-remove_button .item .remove {
            border-left: 1px solid var(--sky-200);
            margin-left: 7px;
            padding-left: 7px;
            color: var(--blue-700);
        }

        .ts-dropdown {
            font-size: .88rem;
            border-radius: 5px;
        }

        .ts-dropdown .option {
            padding: 9px 11px;
            cursor: pointer;
        }

        .ts-dropdown .active {
            background: var(--blue-100);
            color: var(--blue-700);
        }

        .ts-dropdown .selected {
            background: var(--bg);
        }

        .search-help {
            display: block;
            margin-top: 5px;
            font-size: .72rem;
            color: var(--ink-400);
        }

        .broadcast-time-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .btn-submit {
            background: var(--blue-700);
            border: none;
            color: #fff;
            font-size: .85rem;
            padding: 7px 18px;
            border-radius: 5px;
        }

        .btn-submit:hover {
            background: var(--blue-600);
        }

        .btn-submit:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .btn-reset {
            background: #e74c3c;
            border: none;
            color: #fff;
            font-size: .85rem;
            padding: 7px 18px;
            border-radius: 5px;
        }

        .btn-back {
            background: #27ae60;
            border: none;
            color: #fff;
            font-size: .85rem;
            padding: 7px 18px;
            border-radius: 5px;
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

        footer {
            font-size: .8rem;
            color: var(--ink-400);
            padding: 20px 0;
        }

        @keyframes bcpulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .3;
            }
        }

    </style>

</head>

<body>

<?php

$role = strtoupper(trim((string) session('role')));

$isAdmin = $role === 'ADMIN';

$dashboardHref = site_url(
    ltrim(
        \App\Libraries\RoleRedirector::urlFor($role),
        '/'
    )
);

$decodeMultiple = static function ($value): array {

    if (
        $value === null
        || trim((string) $value) === ''
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
                    static fn ($item) => trim((string) $item),
                    $decoded
                ),
                static fn ($item) => $item !== ''
            )
        );

    }

    return [
        trim((string) $value)
    ];

};

$selectedSubCategories = $decodeMultiple(
    $article['sub_category'] ?? null
);

$selectedGovernmentOffices = $decodeMultiple(
    $article['gov_offices'] ?? null
);

$selectedReporters = $decodeMultiple(
    $article['reporter'] ?? null
);

/*
|--------------------------------------------------------------------------
| ENTRY START
|--------------------------------------------------------------------------
|
| This value should come from the controller/session.
|
*/

$entryStartedAt = $entryStartedAt ?? (time() * 1000);

?>

<nav class="navbar navbar-expand-lg">

    <div class="container-fluid">

        <a
            class="navbar-brand"
            href="<?= $dashboardHref ?>"
        >
            <i class="fa-solid fa-fire me-1"></i>
            MMS
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navMain"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div
            class="collapse navbar-collapse"
            id="navMain"
        >

            <ul class="navbar-nav me-auto">

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="<?= $dashboardHref ?>"
                    >
                        <i class="fa fa-gauge me-1"></i>
                        Dashboard
                    </a>

                </li>

                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle active"
                        href="#"
                        data-bs-toggle="dropdown"
                    >
                        <i class="fa fa-th me-1"></i>
                        Applications
                    </a>

                    <ul class="dropdown-menu">

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= site_url('news') ?>"
                            >
                                <i class="fa fa-newspaper me-2 text-muted"></i>
                                News
                            </a>

                        </li>

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= site_url('archived') ?>"
                            >
                                <i class="fa fa-archive me-2 text-muted"></i>
                                Archives
                            </a>

                        </li>

                        <?php if ($isAdmin): ?>

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="<?= site_url('admin/users') ?>"
                                >
                                    <i class="fa fa-users me-2 text-muted"></i>
                                    User Management
                                </a>

                            </li>

                        <?php endif ?>

                    </ul>

                </li>

            </ul>

            <ul class="navbar-nav align-items-center gap-2">

                <li class="nav-item">

                    <div class="user-avatar">

                        <?= esc(
                            strtoupper(
                                substr(
                                    (string) session('first_name'),
                                    0,
                                    1
                                )
                                .
                                substr(
                                    (string) session('last_name'),
                                    0,
                                    1
                                )
                            )
                        ) ?>

                    </div>

                </li>

                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle fw-semibold"
                        href="#"
                        data-bs-toggle="dropdown"
                    >

                        <?= esc(
                            strtoupper(
                                trim(
                                    (string) session('first_name')
                                    . ' '
                                    . (string) session('last_name')
                                )
                            ) ?: $role
                        ) ?>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>

                            <a
                                class="dropdown-item text-danger"
                                href="<?= site_url('auth/logout') ?>"
                            >
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

    <nav
        aria-label="breadcrumb"
        class="mb-3"
    >

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a href="<?= site_url('news') ?>">
                    Forms
                </a>

            </li>

            <li class="breadcrumb-item active">

                <?= $article ? 'Edit News' : 'Create News' ?>

            </li>

        </ol>

    </nav>


    <div class="row g-3">


        <!-- LEFT -->

        <div class="col-lg-7">


            <!-- CONTENT -->

            <div class="section-card mb-3">

                <div class="section-title">
                    Content
                </div>

                <div id="editor">

                    <?= $article['content'] ?? '' ?>

                </div>

                <div class="word-count">

                    <span id="wordCount">
                        0
                    </span>

                    WORDS

                </div>

            </div>


            <!-- CLASSIFICATION -->

            <div class="section-card">

                <div class="section-title">
                    Classification
                </div>

                <div class="row g-3">


                    <!-- CATEGORY -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            class="form-select"
                            id="category"
                        >

                            <option value="">
                                Nothing Selected
                            </option>

                            <?php foreach ($categories as $category): ?>

                                <option
                                    value="<?= esc($category['category_name']) ?>"
                                    <?= (($article['category'] ?? '') === $category['category_name'])
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($category['category_name']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                    </div>


                    <!-- SUB CATEGORY -->

                    <div class="col-md-6">

                        <label class="form-label">
                            Sub-Category
                        </label>

                        <select
                            id="subCategory"
                            name="subCategory[]"
                            multiple
                        >

                            <?php foreach ($subCategories as $subCategory): ?>

                                <option
                                    value="<?= esc($subCategory['sub_category']) ?>"
                                    <?= in_array(
                                        $subCategory['sub_category'],
                                        $selectedSubCategories,
                                        true
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($subCategory['sub_category']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                        <small class="search-help">

                            <i class="fa fa-magnifying-glass me-1"></i>

                            Search and select one or more
                            Sub-Categories.

                        </small>

                    </div>


                    <!-- GOVERNMENT OFFICES -->

                    <div class="col-12">

                        <label class="form-label">
                            Government Offices
                        </label>

                        <select
                            id="govOffices"
                            name="govOffices[]"
                            multiple
                        >

                            <?php foreach ($departments as $department): ?>

                                <option
                                    value="<?= esc($department['department_name']) ?>"
                                    <?= in_array(
                                        $department['department_name'],
                                        $selectedGovernmentOffices,
                                        true
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($department['department_name']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                        <small class="search-help">

                            <i class="fa fa-magnifying-glass me-1"></i>

                            Search and select one or more
                            Government Offices.

                        </small>

                    </div>


                    <!-- REMARKS -->

                    <div class="col-12">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea
                            class="form-control"
                            id="remarks"
                            rows="3"
                            placeholder="Enter remarks here..."
                        ><?= esc($article['remarks'] ?? '') ?></textarea>

                    </div>

                </div>

            </div>

        </div>


        <!-- RIGHT -->

        <div class="col-lg-5">

            <div class="section-card">

                <div class="section-title">
                    Metadata
                </div>


                <!-- ENTRY TIME -->

                <div class="broadcast-time-row">

                    Entry Time

                    <div class="broadcast-display">

                        <span class="bc-dot"></span>

                        <i class="fa fa-stopwatch me-1"></i>

                        <strong id="broadcastTime">
                            00:00:00
                        </strong>

                    </div>

                </div>


                <input
                    type="hidden"
                    id="entryStart"
                    value="<?= (int) $entryStartedAt ?>"
                >


                <div class="row g-2">


                    <!-- SLANT -->

                    <div class="col-12">

                        <label class="form-label">
                            Slant
                        </label>

                        <select
                            class="form-select"
                            id="slant"
                        >

                            <option value="">
                                Nothing Selected
                            </option>

                            <?php foreach ($slants as $slant): ?>

                                <option
                                    value="<?= esc($slant['slant_name']) ?>"
                                    <?= (($article['slant'] ?? '') === $slant['slant_name'])
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($slant['slant_name']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                    </div>


                    <!-- TYPE -->

                    <div class="col-12">

                        <label class="form-label">
                            Type
                        </label>

                        <select
                            class="form-select"
                            id="type"
                        >

                            <option value="">
                                Nothing Selected
                            </option>

                            <?php foreach ($types as $type): ?>

                                <option
                                    value="<?= esc($type['type_name']) ?>"
                                    <?= (($article['type'] ?? '') === $type['type_name'])
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($type['type_name']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                    </div>


                    <!-- MEDIUM -->

                    <div class="col-12">

                        <label class="form-label">
                            Medium
                        </label>

                        <select
                            class="form-select"
                            id="medium"
                        >

                            <option value="">
                                Nothing Selected
                            </option>

                            <?php foreach ($mediums as $medium): ?>

                                <option
                                    value="<?= esc($medium['medium_name']) ?>"
                                    <?= (($article['medium'] ?? '') === $medium['medium_name'])
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($medium['medium_name']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                    </div>


                    <!-- STATION -->

                    <div class="col-12">

                        <label class="form-label">
                            Station
                        </label>

                        <select
                            class="form-select"
                            id="station"
                        >

                            <option value="">
                                Nothing Selected
                            </option>

                            <?php foreach ($stations as $station): ?>

                                <option
                                    value="<?= esc($station['station_name']) ?>"
                                    <?= (($article['station'] ?? '') === $station['station_name'])
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


                    <!-- PROGRAM -->

                    <div class="col-12">

                        <label class="form-label">
                            Program
                        </label>

                        <select
                            class="form-select"
                            id="program"
                        >

                            <option value="">
                                Nothing Selected
                            </option>

                            <?php foreach ($programs as $program): ?>

                                <option
                                    value="<?= esc($program['program_name']) ?>"
                                    <?= (($article['program'] ?? '') === $program['program_name'])
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


                    <!-- REPORTER -->

                    <div class="col-12">

                        <label class="form-label">
                            Anchor/Reporter
                        </label>

                        <select
                            id="reporter"
                            name="reporter[]"
                            multiple
                        >

                            <?php foreach ($reporters as $reporter): ?>

                                <option
                                    value="<?= esc($reporter['reporter_name']) ?>"
                                    <?= in_array(
                                        $reporter['reporter_name'],
                                        $selectedReporters,
                                        true
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >

                                    <?= esc($reporter['reporter_name']) ?>

                                </option>

                            <?php endforeach ?>

                        </select>

                        <small class="search-help">

                            <i class="fa fa-magnifying-glass me-1"></i>

                            Search and select one or more
                            Anchors/Reporters.

                        </small>

                    </div>


                    <!-- ALERT -->

                    <div class="col-12">

                        <label class="form-label">
                            Alert
                        </label>

                        <select
                            class="form-select"
                            id="alert"
                        >

                            <option
                                value="No"
                                <?= (($article['alert'] ?? 'No') === 'No')
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                No
                            </option>

                            <option
                                value="Yes"
                                <?= (($article['alert'] ?? '') === 'Yes')
                                    ? 'selected'
                                    : ''
                                ?>
                            >
                                Yes
                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-12 mt-3 d-flex gap-2 flex-wrap">

                        <button
                            type="button"
                            class="btn-submit"
                            id="submitBtn"
                            onclick="submitArticle()"
                        >

                            <i class="fa fa-check me-1"></i>

                            <?= $article ? 'Update & Submit' : 'Submit' ?>

                        </button>


                        <button
                            type="button"
                            class="btn-reset"
                            onclick="resetForm()"
                        >

                            <i class="fa fa-redo me-1"></i>
                            Reset

                        </button>


                        <button
                            type="button"
                            class="btn-back"
                            onclick="goBack()"
                        >

                            <i class="fa fa-arrow-left me-1"></i>
                            Back To Listing

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- TOAST -->

<div
    class="position-fixed bottom-0 end-0 p-3"
    style="z-index: 9999;"
>

    <div
        id="toastMsg"
        class="toast align-items-center text-bg-success border-0"
        role="alert"
    >

        <div class="d-flex">

            <div
                class="toast-body"
                id="toastText"
            >
                Saved successfully!
            </div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"
            ></button>

        </div>

    </div>

</div>


<div class="container-fluid px-4">

    <footer class="d-flex justify-content-between">

        <span>
            Indulged by MISD © 2020
        </span>

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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>


<script>

        const SAVE_URL =
        '<?= site_url('create-article/save') ?>';

    const LISTING_URL =
        '<?= site_url('news') ?>';

    let editId =
        <?= json_encode($article['id'] ?? null) ?>;

    let quill;
    let timerInterval = null;

    let subCategorySelect = null;
    let govOfficesSelect = null;
    let reporterSelect = null;


    function csrfHeaders() {

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


    function elapsedSeconds() {

        const startedAtMs =
            Number(
                document
                    .getElementById('entryStart')
                    .value
            );

        if (
            !Number.isFinite(startedAtMs)
            || startedAtMs <= 0
        ) {
            return 0;
        }

        return (
            Date.now() - startedAtMs
        ) / 1000;

    }


    function startTimer() {

        if (timerInterval) {

            clearInterval(
                timerInterval
            );

        }

        const tick = () => {

            document
                .getElementById('broadcastTime')
                .textContent =
                    formatDuration(
                        elapsedSeconds()
                    );

        };

        tick();

        timerInterval =
            setInterval(
                tick,
                1000
            );

    }


    function initializeSearchableSelects() {

        subCategorySelect =
            new TomSelect(
                '#subCategory',
                {
                    plugins: [
                        'remove_button'
                    ],

                    placeholder:
                        'Search Sub-Category...',

                    maxItems:
                        null,

                    create:
                        false,

                    persist:
                        false,

                    hideSelected:
                        true
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

                    maxItems:
                        null,

                    create:
                        false,

                    persist:
                        false,

                    hideSelected:
                        true
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

                    maxItems:
                        null,

                    create:
                        false,

                    persist:
                        false,

                    hideSelected:
                        true
                }
            );

    }


    function resetForm() {

        quill.setText('');

        [
            'category',
            'slant',
            'type',
            'medium',
            'station',
            'program'
        ].forEach(
            id => {

                const element =
                    document.getElementById(id);

                if (element) {

                    element.selectedIndex = 0;

                }

            }
        );


        if (subCategorySelect) {

            subCategorySelect.clear();

        }


        if (govOfficesSelect) {

            govOfficesSelect.clear();

        }


        if (reporterSelect) {

            reporterSelect.clear();

        }


        document
            .getElementById('remarks')
            .value = '';


        document
            .getElementById('alert')
            .value = 'No';

    }


    function goBack() {

        window.location.href =
            LISTING_URL;

    }


    function showToast(
        message,
        type = 'success'
    ) {

        const toast =
            document.getElementById(
                'toastMsg'
            );

        toast.className =
            `toast align-items-center text-bg-${type} border-0`;

        document
            .getElementById('toastText')
            .textContent =
                message;

        new bootstrap.Toast(
            toast,
            {
                delay: 2500
            }
        ).show();

    }


    async function submitArticle() {

        const content =
            quill.root.innerHTML.trim();

        const plainText =
            quill.getText().trim();


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            !content
            || content === '<p><br></p>'
            || !plainText
        ) {

            showToast(
                'Content cannot be empty.',
                'danger'
            );

            return;

        }

        const category =
            document
                .getElementById('category')
                .value
                .trim();

        if (!category) {

            showToast(
                'Please select a Category.',
                'danger'
            );

            return;

        }

        const selectedSubs2 =
            subCategorySelect
                ? subCategorySelect.getValue()
                : [];

        if (
            !Array.isArray(selectedSubs2)
            || selectedSubs2.length === 0
        ) {

            showToast(
                'Please select at least one Sub-Category.',
                'danger'
            );

            return;

        }

        const selectedGov2 =
            govOfficesSelect
                ? govOfficesSelect.getValue()
                : [];

        if (
            !Array.isArray(selectedGov2)
            || selectedGov2.length === 0
        ) {

            showToast(
                'Please select at least one Government Office.',
                'danger'
            );

            return;

        }

        const slant =
            document
                .getElementById('slant')
                .value
                .trim();

        if (!slant) {

            showToast(
                'Please select a Slant.',
                'danger'
            );

            return;

        }

        const type =
            document
                .getElementById('type')
                .value
                .trim();

        if (!type) {

            showToast(
                'Please select a Type.',
                'danger'
            );

            return;

        }

        const medium =
            document
                .getElementById('medium')
                .value
                .trim();

        if (!medium) {

            showToast(
                'Please select a Medium.',
                'danger'
            );

            return;

        }

        const station =
            document
                .getElementById('station')
                .value
                .trim();

        if (!station) {

            showToast(
                'Please select a Station.',
                'danger'
            );

            return;

        }

        const program =
            document
                .getElementById('program')
                .value
                .trim();

        if (!program) {

            showToast(
                'Please select a Program.',
                'danger'
            );

            return;

        }

        const selectedReporters2 =
            reporterSelect
                ? reporterSelect.getValue()
                : [];

        if (
            !Array.isArray(selectedReporters2)
            || selectedReporters2.length === 0
        ) {

            showToast(
                'Please select at least one Anchor/Reporter.',
                'danger'
            );

            return;

        }


        const button =
            document.getElementById(
                'submitBtn'
            );

        button.disabled = true;


        /*
        |--------------------------------------------------------------------------
        | ENTRY TIME
        |--------------------------------------------------------------------------
        */

        const entryStartMs =
            Number(
                document
                    .getElementById('entryStart')
                    .value
            );

        const entryEndUnix =
            Math.floor(
                Date.now() / 1000
            );


        const form =
            new FormData();


        /*
        |--------------------------------------------------------------------------
        | ARTICLE ID
        |--------------------------------------------------------------------------
        */

        if (editId) {

            form.append(
                'id',
                editId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | WRITER CONTENT
        |--------------------------------------------------------------------------
        */

        form.append(
            'content',
            content
        );


        /*
        |--------------------------------------------------------------------------
        | CLASSIFICATION
        |--------------------------------------------------------------------------
        */

        form.append(
            'category',
            document
                .getElementById('category')
                .value
        );


        form.append(
            'remarks',
            document
                .getElementById('remarks')
                .value
        );


        /*
        |--------------------------------------------------------------------------
        | METADATA
        |--------------------------------------------------------------------------
        */

        form.append(
            'slant',
            document
                .getElementById('slant')
                .value
        );

        form.append(
            'type',
            document
                .getElementById('type')
                .value
        );

        form.append(
            'medium',
            document
                .getElementById('medium')
                .value
        );

        form.append(
            'station',
            document
                .getElementById('station')
                .value
        );

        form.append(
            'program',
            document
                .getElementById('program')
                .value
        );

        form.append(
            'alert',
            document
                .getElementById('alert')
                .value
        );


        /*
        |--------------------------------------------------------------------------
        | WORKFLOW TIMESTAMPS
        |--------------------------------------------------------------------------
        */

        form.append(
            'entry_start',
            Math.floor(entryStartMs / 1000)
        );

        form.append(
            'entry_end',
            entryEndUnix
        );

        form.append(
            'entry_time',
            formatDuration(
                elapsedSeconds()
            )
        );


        /*
        |--------------------------------------------------------------------------
        | WRITER WORKFLOW STATUS
        |--------------------------------------------------------------------------
        |
        | NEW
        |   ↓
        | FORM
        |   ↓
        | SUBMIT
        |   ↓
        | STATUS = SUBMITTED
        |
        */

        form.append(
            'status',
            'SUBMITTED'
        );


        /*
        |--------------------------------------------------------------------------
        | SUB CATEGORY
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | GOVERNMENT OFFICES
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | REPORTERS
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        const csrf =
            csrfHeaders();


        Object
            .entries(csrf)
            .forEach(
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
                    SAVE_URL,
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


            /*
            |--------------------------------------------------------------------------
            | UPDATE CSRF TOKEN
            |--------------------------------------------------------------------------
            */

            if (json.csrfToken) {

                document
                    .querySelector(
                        'meta[name="csrf-token"]'
                    )
                    .content =
                        json.csrfToken;

            }


            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            if (
                !response.ok
                || !json.status
            ) {

                showToast(
                    json.message
                    || 'Something went wrong.',
                    'danger'
                );

                button.disabled = false;

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            if (timerInterval) {

                clearInterval(
                    timerInterval
                );

            }


            showToast(
                json.message
                || 'Article submitted successfully!'
            );


            setTimeout(
                () => {

                    window.location.href =
                        LISTING_URL;

                },
                1500
            );

        } catch (error) {

            console.error(
                'Article save error:',
                error
            );


            showToast(
                'Unable to save article. Please try again.',
                'danger'
            );


            button.disabled = false;

        }

    }


    function init() {

        /*
        |--------------------------------------------------------------------------
        | SEARCHABLE SELECTS
        |--------------------------------------------------------------------------
        */

        initializeSearchableSelects();


        /*
        |--------------------------------------------------------------------------
        | QUILL EDITOR
        |--------------------------------------------------------------------------
        */

        quill =
            new Quill(
                '#editor',
                {
                    theme: 'snow',

                    modules: {

                        toolbar: [

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
                                    list: 'ordered'
                                },

                                {
                                    list: 'bullet'
                                }
                            ],

                            [
                                'link'
                            ],

                            [
                                'clean'
                            ]

                        ]

                    }

                }
            );


        /*
        |--------------------------------------------------------------------------
        | WORD COUNT
        |--------------------------------------------------------------------------
        */

        function updateWordCount() {

            const text =
                quill
                    .getText()
                    .trim();


            const words =
                text === ''
                    ? 0
                    : text
                        .split(/\s+/)
                        .filter(Boolean)
                        .length;


            document
                .getElementById('wordCount')
                .textContent =
                    words;

        }


        quill.on(
            'text-change',
            updateWordCount
        );


        updateWordCount();


        /*
        |--------------------------------------------------------------------------
        | START ENTRY TIMER
        |--------------------------------------------------------------------------
        */

        startTimer();

    }


    document.addEventListener(
        'DOMContentLoaded',
        init
    );

</script>

</body>

</html>