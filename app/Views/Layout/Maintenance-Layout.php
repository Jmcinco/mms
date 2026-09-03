<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MMS – Maintenance</title>
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

    .sidebar-nav .nav-link {
      padding: 10px 16px !important;
      border-radius: 6px;
      margin-bottom: 3px;
      color: var(--ink-700) !important;
      font-size: .9rem;
      border: none;
    }

    .sidebar-nav .nav-link:hover {
      background: var(--blue-100);
      color: var(--blue-700) !important;
    }

    .sidebar-nav .nav-link.active {
      background: var(--blue-700) !important;
      color: #fff !important;
      font-weight: 600;
    }

    .sidebar-nav .nav-link .fa {
      width: 18px;
    }

    .main-panel {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
      border: 1px solid var(--line);
      padding: 24px;
    }

    .panel-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .panel-title .panel-title-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-label {
      font-size: .83rem;
      font-weight: 600;
      color: var(--ink-700);
    }

    .form-control:focus {
      border-color: var(--blue-700);
      box-shadow: 0 0 0 .18rem rgba(28, 95, 196, .18);
    }

    .btn-add-item {
      background: var(--blue-700);
      border: none;
      color: #fff;
      padding: 7px 18px;
      border-radius: 5px;
      font-size: .88rem;
    }

    .btn-add-item:hover {
      background: var(--blue-600);
      color: #fff;
    }

    .btn-bulk-upload {
      border: 1px solid var(--line);
      color: var(--ink-700);
      background: #fff;
      font-size: .85rem;
      padding: 6px 14px;
      border-radius: 5px;
    }

    .btn-bulk-upload:hover {
      border-color: var(--blue-700);
      color: var(--blue-700);
      background: var(--blue-100);
    }

    .table thead th {
      font-size: .8rem;
      font-weight: 700;
      color: var(--ink-400);
      text-transform: uppercase;
      letter-spacing: .3px;
      border-bottom: 2px solid var(--line);
    }

    .table tbody td {
      font-size: .9rem;
      color: var(--ink-700);
      vertical-align: middle;
      border-color: var(--line);
    }

    .btn-edit {
      background: var(--blue-700);
      color: #fff;
      border: none;
      font-size: .75rem;
      padding: 3px 9px;
      border-radius: 4px;
    }

    .btn-edit:hover {
      background: var(--blue-600);
    }

    .btn-del {
      background: #e74c3c;
      color: #fff;
      border: none;
      font-size: .75rem;
      padding: 3px 9px;
      border-radius: 4px;
    }

    .btn-del:hover {
      background: #c0392b;
    }

    .no-items {
      color: var(--ink-400);
      text-align: center;
      padding: 20px;
      font-size: .9rem;
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

    .modal-header.red {
      background: var(--blue-700);
      color: #fff;
    }

    .modal-header.red .btn-close {
      filter: invert(1);
    }

    #bulkErrorList {
      list-style: none;
      padding-left: 0;
      margin-top: 8px;
    }

    #bulkErrorList li {
      padding: 3px 0;
      border-bottom: 1px dashed var(--line);
    }

    .bulk-drop-hint {
      font-size: .78rem;
      color: var(--ink-400);
      margin-top: 4px;
    }

    /* ============ PAGINATION ============ */
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
  </style>
       <?= $this->renderSection('styles') ?>
</head>
<body>

<?= $this->renderSection('content') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>