<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta name="csrf-name" content="<?= csrf_token() ?>">
  <title>MMS – News Article</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
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
      box-shadow: 0 10px 30px rgba(14, 44, 82, .1);
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

    /* ============ PAGE ============ */
    .breadcrumb {
      background: transparent;
      padding: 0;
      font-size: .85rem;
      margin-bottom: 0;
    }

    .breadcrumb-item a {
      color: var(--blue-700);
      text-decoration: none;
    }

    .breadcrumb-item.active {
      color: var(--ink-400);
    }

    .page-card {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
      border: 1px solid var(--line);
      padding: 28px;
    }

    .page-title {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--navy);
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .page-title i {
      color: var(--ink-400);
    }

    .btn-add {
      background: #fff;
      border: 1px solid var(--blue-700);
      color: var(--blue-700);
      font-size: .87rem;
      padding: 6px 16px;
      border-radius: 6px;
      font-weight: 500;
      transition: background .15s ease, color .15s ease;
    }

    .btn-add:hover {
      background: var(--blue-700);
      color: #fff;
    }

    .search-box {
      max-width: 200px;
    }

    .search-box:focus {
      border-color: var(--blue-500);
      box-shadow: 0 0 0 .2rem rgba(76, 139, 245, .15);
    }

    /* ============ TABLE ============ */
    .table thead th {
      font-size: .74rem;
      font-weight: 700;
      color: var(--ink-400);
      text-transform: uppercase;
      letter-spacing: .3px;
      border-bottom: 2px solid var(--line);
    }

    .table tbody td {
      font-size: .86rem;
      color: var(--ink-700);
      vertical-align: middle;
      border-color: var(--line);
    }

    .table tbody tr:hover {
      background: var(--blue-100);
    }

    .id-chip {
      color: var(--blue-700) !important;
      font-weight: 700;
    }

    .btn-edit {
      background: var(--blue-700);
      color: #fff;
      border: none;
      font-size: .78rem;
      padding: 4px 10px;
      border-radius: 5px;
      transition: background .15s ease;
    }

    .btn-edit:hover {
      background: var(--blue-600);
      color: #fff;
    }

    .btn-del {
      background: #e74c3c;
      color: #fff;
      border: none;
      font-size: .78rem;
      padding: 4px 10px;
      border-radius: 5px;
      transition: background .15s ease;
    }

    .btn-del:hover {
      background: #c0392b;
    }

    .pagination-info {
      font-size: .83rem;
      color: var(--ink-400);
    }

    .btn-page {
      border: 1px solid var(--line);
      background: #fff;
      color: var(--ink-700);
      font-size: .85rem;
      padding: 5px 14px;
      border-radius: 6px;
      transition: background .15s ease;
    }

    .btn-page:hover {
      background: var(--blue-100);
    }

    .btn-page:disabled {
      opacity: .5;
      cursor: not-allowed;
    }

    .no-data {
      color: var(--ink-400);
      font-size: .9rem;
      text-align: center;
      padding: 24px;
    }

    footer {
      font-size: .8rem;
      color: var(--ink-400);
      padding: 20px 0;
    }

    /* ============ MODAL ============ */
    .modal-content {
      border-radius: 12px;
      border: none;
    }

    .modal-title.text-danger {
      color: #e0483f !important;
    }
  </style>
  <?= $this->renderSection('styles') ?>
</head>
<body>

<?= $this->renderSection('content') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>