<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MMS - Archives Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
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

    .badge-version {
      font-size: .7rem;
      padding: 3px 7px;
      vertical-align: middle;
      background: var(--teal-600) !important;
    }

    .page-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--navy);
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .filter-card {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
      border: 1px solid var(--line);
      padding: 22px;
      margin-bottom: 16px;
    }

    .filter-title {
      font-size: .95rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 16px;
    }

    .form-select,
    .form-control {
      font-size: .87rem;
    }

    .form-select:focus,
    .form-control:focus {
      border-color: var(--blue-700);
      box-shadow: 0 0 0 .18rem rgba(28, 95, 196, .18);
    }

    .btn-query {
      border: 1.5px solid #27ae60;
      color: #27ae60;
      background: #fff;
      font-size: .87rem;
      padding: 6px 18px;
      border-radius: 5px;
    }

    .btn-query:hover {
      background: #27ae60;
      color: #fff;
    }

    .btn-reset-form {
      border: 1.5px solid #e74c3c;
      color: #e74c3c;
      background: #fff;
      font-size: .87rem;
      padding: 6px 18px;
      border-radius: 5px;
    }

    .btn-reset-form:hover {
      background: #e74c3c;
      color: #fff;
    }

    .query-text-card {
      background: #fff;
      border-radius: 8px;
      padding: 14px 20px;
      margin-bottom: 16px;
      border-left: 4px solid var(--blue-700);
      font-size: .87rem;
      color: var(--ink-700);
    }

    .query-text-card strong {
      color: var(--blue-700);
    }

    .data-card {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
      border: 1px solid var(--line);
      padding: 22px;
    }

    .data-title {
      font-size: 1rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 14px;
    }

    .show-row {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: .87rem;
      margin-bottom: 14px;
    }

    .btn-pdf {
      background: #fff;
      border: 1.5px solid #e74c3c;
      color: #e74c3c;
      font-size: .78rem;
      padding: 4px 12px;
      border-radius: 4px;
    }

    .btn-pdf:hover {
      background: #e74c3c;
      color: #fff;
    }

    .btn-excel {
      background: #fff;
      border: 1.5px solid #27ae60;
      color: #27ae60;
      font-size: .78rem;
      padding: 4px 12px;
      border-radius: 4px;
    }

    .btn-excel:hover {
      background: #27ae60;
      color: #fff;
    }

    .btn-excel[disabled] {
      opacity: .6;
      cursor: wait;
    }

    .btn-print {
      background: #fff;
      border: 1.5px solid var(--ink-400);
      color: var(--ink-400);
      font-size: .78rem;
      padding: 4px 12px;
      border-radius: 4px;
    }

    .btn-print:hover {
      background: var(--ink-400);
      color: #fff;
    }

    .table thead th {
      font-size: .81rem;
      font-weight: 700;
      color: var(--ink-400);
      text-transform: uppercase;
      letter-spacing: .3px;
      border-bottom: 2px solid var(--line);
    }

    .table tbody td {
      font-size: .89rem;
      color: var(--ink-700);
      vertical-align: top;
      border-color: var(--line);
    }

    .news-row {
      cursor: pointer;
    }

    .news-row:hover {
      background: var(--blue-100);
    }

    .news-row.expanded {
      background: var(--blue-100);
    }

    .detail-row td {
      background: var(--blue-100);
      padding: 18px 20px;
    }

    .detail-item {
      margin-bottom: 10px;
    }

    .detail-item:last-child {
      margin-bottom: 0;
    }

    .detail-label {
      display: inline-block;
      min-width: 110px;
      font-size: .75rem;
      font-weight: 700;
      color: var(--blue-700);
      letter-spacing: .5px;
      margin-right: 6px;
      vertical-align: top;
    }

    .detail-value {
      font-size: .87rem;
      color: var(--ink-700);
    }

    .id-chip {
      color: var(--blue-700);
      font-weight: 700;
    }

    .no-data {
      color: var(--ink-400);
      text-align: center;
      padding: 24px;
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

    .input-group-text {
      background: var(--blue-100);
      color: var(--blue-700);
      font-size: .85rem;
      border-right: none;
    }
  </style>
</head>
<body>

<?php $isWriter = ($role === 'WRITER'); ?>
<?php $dashboardUrl = $isWriter ? site_url('writer/dashboard') : site_url('editor/dashboard'); ?>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-fire me-1"></i>MMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php
      $role          = strtoupper(session('role') ?? '');
      $isAdmin       = $role === 'ADMIN';
      $dashboardHref = site_url(ltrim(\App\Libraries\RoleRedirector::urlFor($role), '/'));
    ?>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= $dashboardHref ?>"><i class="fa fa-gauge me-1"></i>Dashboard</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fa fa-th me-1"></i>Applications</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('news') ?>"><i class="fa fa-newspaper me-2 text-muted"></i>News</a></li>
            <li><a class="dropdown-item active" href="<?= site_url('archived') ?>"><i class="fa fa-archive me-2 text-muted"></i>Archives</a></li>
              <li>
                <a class="dropdown-item" href="<?= site_url('admin/users') ?>">
                  <i class="fa fa-users me-2 text-muted"></i>
                  User Management
                </a>
              </li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fa fa-cog me-1"></i>Maintenance</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('maintenance/slants') ?>"><i class="fa fa-tag me-2 text-muted"></i>Slant</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/categories') ?>"><i class="fa fa-list me-2 text-muted"></i>Category</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/subcategories') ?>"><i class="fa fa-list-ul me-2 text-muted"></i>SubCategory</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/departments') ?>"><i class="fa fa-building me-2 text-muted"></i>Department</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/types') ?>"><i class="fa fa-file-alt me-2 text-muted"></i>Type</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/mediums') ?>"><i class="fa fa-broadcast-tower me-2 text-muted"></i>Medium</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/programs') ?>"><i class="fa fa-tv me-2 text-muted"></i>Program</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/stations') ?>"><i class="fa fa-satellite-dish me-2 text-muted"></i>Station</a></li>
            <li><a class="dropdown-item" href="<?= site_url('maintenance/reporters') ?>"><i class="fa fa-user-tie me-2 text-muted"></i>Reporter</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fa fa-rotate me-1"></i>Revision <span class="badge badge-version">V4.0</span></a>
        </li>
      </ul>
      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item bell-badge"><a class="nav-link" href="#"><i class="fa fa-bell fa-lg"></i><span class="badge">3</span></a></li>
        <li class="nav-item"><div class="user-avatar" id="userInitials">AD</div></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown" id="userName">ADMIN</a>
          <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= site_url('admin/users') ?>"><i class="fa fa-users me-2"></i>User Management</a></li>
              <li><a class="dropdown-item" href="<?= site_url('maintenance') ?>"><i class="fa fa-cog me-2"></i>Maintenance</a></li>
              <li><hr class="dropdown-divider"/></li>
            <li>
              <a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>">
                <i class="fa fa-sign-out-alt me-2"></i>Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 py-4">
  <div class="page-title"><i class="fa fa-database" style="color:var(--blue-700);"></i>Archives Data</div>

  <div class="filter-card">
    <div class="filter-title">Custom Filter</div>
    <div class="row g-2 mb-3">
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-calendar"></i> Date Range</span>
          <input type="text" class="form-control" id="dateRange" placeholder="Select date range" readonly/>
        </div>
      </div>
      <div class="col-md-8">
        <input type="text" class="form-control" id="searchText" placeholder="Search Text"/>
      </div>
    </div>
    <div class="row g-2 mb-2">
      <div class="col-md-4">
        <select class="form-select" id="fCategory">
          <option value="">Choose Category...</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= esc($c['category_name']) ?>"><?= esc($c['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="fSubCategory">
          <option value="">Choose SubCategory...</option>
          <?php foreach ($subCategories as $s): ?>
            <option value="<?= esc($s['sub_category']) ?>"><?= esc($s['sub_category']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="fDepartment">
          <option value="">Choose Department...</option>
          <?php foreach ($departments as $d): ?>
            <option value="<?= esc($d['department_name']) ?>"><?= esc($d['department_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="row g-2 mb-2">
      <div class="col-md-4">
        <select class="form-select" id="fSlant">
          <option value="">Choose Slant...</option>
          <?php foreach ($slants as $s): ?>
            <option value="<?= esc($s['slant_name']) ?>"><?= esc($s['slant_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="fType">
          <option value="">Choose Type...</option>
          <?php foreach ($types as $t): ?>
            <option value="<?= esc($t['type_name']) ?>"><?= esc($t['type_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="fMedium">
          <option value="">Choose Medium...</option>
          <?php foreach ($mediums as $m): ?>
            <option value="<?= esc($m['medium_name']) ?>"><?= esc($m['medium_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="row g-2 mb-3">
      <div class="col-md-4">
        <select class="form-select" id="fStation">
          <option value="">Choose Station...</option>
          <?php foreach ($stations as $s): ?>
            <option value="<?= esc($s['station_name']) ?>"><?= esc($s['station_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="fProgram">
          <option value="">Choose Program...</option>
          <?php foreach ($programs as $p): ?>
            <option value="<?= esc($p['program_name']) ?>"><?= esc($p['program_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="fReporter">
          <option value="">Choose Reporter...</option>
          <?php foreach ($reporters as $r): ?>
            <option value="<?= esc($r['reporter_name']) ?>"><?= esc($r['reporter_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button class="btn-query" id="btnQuery"><i class="fa fa-search me-1"></i>Query</button>
      <button class="btn-reset-form" id="btnReset"><i class="fa fa-undo me-1"></i>Reset Form</button>
    </div>
  </div>

  <div class="query-text-card" id="queryTextBox" style="display:none;">
    <span id="queryTextContent"></span>
  </div>

  <div class="data-card">
    <div class="data-title">Filtered Data</div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div class="show-row">
        Show
        <select class="form-select form-select-sm" style="width:70px" id="pageSize">
          <option>10</option><option>25</option><option>50</option>
        </select>
        entries
        <button class="btn-pdf ms-3" id="btnPdf">PDF</button>
        <button class="btn-excel" id="btnExcel">Excel</button>
        <button class="btn-print" id="btnPrint">Print</button>
      </div>
    </div>
    <div id="resultsContainer">
      <div class="no-data"><i class="fa fa-filter me-2"></i>Use the filter above and click Query to view archived articles.</div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3">
      <span class="text-muted" style="font-size:.83rem;" id="resultInfo"></span>
    </div>
  </div>
</div>

<div class="container-fluid px-4">
  <footer class="d-flex justify-content-between">
    <span>Indulged by MISD © 2020</span>
    <span>Follow us &nbsp;<i class="fab fa-facebook"></i>&nbsp;<i class="fab fa-twitter"></i>&nbsp;<i class="fab fa-google-plus-g"></i></span>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.0/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
<script>
const DATA_URL = "<?= site_url('archived/data') ?>";
const AVERAGES_URL = "<?= site_url('archived/averages') ?>";

const DISPLAY_TZ = 'Asia/Manila';

const UNSET_TS = 946656000;

let filteredResults = [];
let lastQueryParams = '';
let fp;


function esc(v) {
  if (v === null || v === undefined || v === '') return '—';
  return String(v).replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));
}

function tzParts(unixTs) {
  const d = new Date(unixTs * 1000);
  const fmt = new Intl.DateTimeFormat('en-CA', {
    timeZone: DISPLAY_TZ,
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit', second: '2-digit',
    hour12: false,
  });
  const parts = Object.fromEntries(fmt.formatToParts(d).map(p => [p.type, p.value]));
  if (parts.hour === '24') parts.hour = '00';
  return parts;
}

function tsToClock(unixTs) {
  const n = parseInt(unixTs, 10);
  if (!n || n <= 0) return '—';
  const p = tzParts(n);
  return `${p.year}-${p.month}-${p.day}, ${p.hour}:${p.minute}:${p.second}`;
}

function formatBroadcastTime(unixTs) {
  const n = parseInt(unixTs, 10);
  if (!n || n <= 0) return '—';
  const p = tzParts(n);
  let h = parseInt(p.hour, 10);
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return `${p.year}-${p.month}-${p.day} ${String(h).padStart(2, '0')}:${p.minute}:${p.second} ${ampm}`;
}

function secondsToDuration(sec) {
  const h = String(Math.floor(sec / 3600)).padStart(2, '0');
  const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
  const s = String(sec % 60).padStart(2, '0');
  return `${h}:${m}:${s}`;
}

function entryDurationRaw(a) {
  const start = parseInt(a.entry_start, 10) || 0;
  const end   = parseInt(a.entry_end, 10) || 0;
  if (start <= 0 || end <= 0 || end <= start) return '—';
  return secondsToDuration(end - start);
}

function editDurationRaw(a) {
  const entryEnd = parseInt(a.entry_end, 10) || 0;
  const rawStart = parseInt(a.editing_start, 10) || 0;
  const start = (rawStart > UNSET_TS) ? rawStart : entryEnd;
  const end   = parseInt(a.editing_end, 10) || 0;

  if (end <= UNSET_TS || start <= UNSET_TS || end <= start) return '—';
  return secondsToDuration(end - start);
}

function decodeSubCategory(raw) {
  raw = String(raw ?? '').trim();
  if (!raw) return '';

  let decoded = null;
  try {
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) decoded = parsed;
  } catch (e) { /* not JSON, fall through */ }

  if (!decoded) {
    try {
      const parsed = JSON.parse('[' + raw + ']');
      if (Array.isArray(parsed)) decoded = parsed;
    } catch (e) { /* fall through */ }
  }

  if (!decoded) decoded = raw.split(',');

  return decoded
    .map(v => String(v).trim().replace(/^"+|"+$/g, ''))
    .filter(Boolean)
    .join(', ');
}

fp = flatpickr('#dateRange', {
  mode: 'range',
  dateFormat: 'm/d/Y',
  defaultDate: [new Date(new Date().getFullYear(), new Date().getMonth(), 1), new Date()]
});


async function runQuery() {
  const dates = fp.selectedDates;
  let fromDate = null, toDate = null;
  if (dates.length >= 2) { fromDate = dates[0]; toDate = dates[1]; }
  else if (dates.length === 1) { fromDate = toDate = dates[0]; }

  const params = new URLSearchParams();
  const searchText = document.getElementById('searchText').value;
  if (searchText) params.set('search', searchText);
  if (fromDate && toDate) {
    params.set('date_from', fromDate.toISOString().split('T')[0]);
    params.set('date_to', toDate.toISOString().split('T')[0]);
  }
  const fieldMap = { fCategory:'category', fSubCategory:'subCategory', fDepartment:'department', fSlant:'slant', fType:'type', fMedium:'medium', fStation:'station', fProgram:'program', fReporter:'reporter' };
  Object.entries(fieldMap).forEach(([elId, param]) => {
    const val = document.getElementById(elId).value;
    if (val) params.set(param, val);
  });

  lastQueryParams = params.toString();

  const res = await fetch(`${DATA_URL}?${lastQueryParams}`);
  const json = await res.json();
  filteredResults = json.data;

  let qt = 'Query Text: { ';
  if (fromDate && toDate) qt += `From: <strong>'${fromDate.toISOString().split('T')[0]}'</strong> To: <strong>'${toDate.toISOString().split('T')[0]}'</strong>`;
  const cat = document.getElementById('fCategory').value;
  const subC = document.getElementById('fSubCategory').value;
  const type = document.getElementById('fType').value;
  const med = document.getElementById('fMedium').value;
  const rep = document.getElementById('fReporter').value;
  if (cat)  qt += `, Category: '${cat}'`;
  if (subC) qt += `, SubCategory: '${subC}'`;
  if (type) qt += `, Type: '${type}'`;
  if (med)  qt += `, Medium: '${med}'`;
  if (rep)  qt += `, Reporter: '${rep}'`;
  qt += ' }';
  document.getElementById('queryTextContent').innerHTML = qt;
  document.getElementById('queryTextBox').style.display = '';

  renderResults();
}

function renderResults() {
  const size = parseInt(document.getElementById('pageSize').value);
  const page = filteredResults.slice(0, size);
  const container = document.getElementById('resultsContainer');

  if (filteredResults.length === 0) {
    container.innerHTML = '<div class="no-data">No archived articles found matching the criteria.</div>';
    document.getElementById('resultInfo').textContent = '';
    return;
  }

  const rowsHtml = page.map(a => {
    const subCat = decodeSubCategory(a.sub_category);
    const safeId = esc(a.id);

    return `
      <tr class="news-row" data-id="${safeId}">
        <td><strong class="id-chip">${safeId}</strong></td>
        <td>${esc(a.news_date)}</td>
        <td>${formatBroadcastTime(a.entry_start)}</td>
        <td>${esc(a.type)}</td>
      </tr>
      <tr class="detail-row" id="detail-${safeId}" style="display:none;">
        <td colspan="4">
          <div class="detail-item"><span class="detail-label">CATEGORY</span><span class="detail-value">${esc(a.category)}</span></div>
          <div class="detail-item"><span class="detail-label">SUB-CATEGORY</span><span class="detail-value">${subCat || '—'}</span></div>
          <div class="detail-item"><span class="detail-label">SUMMARY</span><span class="detail-value">${esc(a.summary)}</span></div>
          <div class="detail-item"><span class="detail-label">SLANT</span><span class="detail-value">${esc(a.slant)}</span></div>
          <div class="detail-item"><span class="detail-label">MEDIUM</span><span class="detail-value">${esc(a.medium)}</span></div>
          <div class="detail-item"><span class="detail-label">STATION</span><span class="detail-value">${esc(a.station)}</span></div>
          <div class="detail-item"><span class="detail-label">PROGRAM</span><span class="detail-value">${esc(a.program)}</span></div>
          <div class="detail-item"><span class="detail-label">REPORTER</span><span class="detail-value">${esc(a.reporter)}</span></div>
        </td>
      </tr>`;
  }).join('');

  container.innerHTML = `
    <table class="table table-bordered">
      <thead><tr><th>News ID</th><th>News Date</th><th>Broadcast Time</th><th>Type</th></tr></thead>
      <tbody>${rowsHtml}</tbody>
    </table>`;
  document.getElementById('resultInfo').textContent = `Showing ${Math.min(size, filteredResults.length)} of ${filteredResults.length} results`;

  // Toggle a row's detail panel when the News ID row is clicked.
  container.querySelectorAll('.news-row').forEach(row => {
    row.addEventListener('click', () => {
      const detail = document.getElementById(`detail-${row.dataset.id}`);
      if (!detail) return;
      const isOpen = detail.style.display !== 'none';
      detail.style.display = isOpen ? 'none' : '';
      row.classList.toggle('expanded', !isOpen);
    });
  });
}

function resetFilters() {
  fp.clear();
  document.getElementById('searchText').value = '';
  ['fCategory','fSubCategory','fDepartment','fSlant','fType','fMedium','fStation','fProgram','fReporter']
    .forEach(id => { document.getElementById(id).selectedIndex = 0; });
  filteredResults = [];
  lastQueryParams = '';
  document.getElementById('queryTextBox').style.display = 'none';
  document.getElementById('resultsContainer').innerHTML = '<div class="no-data"><i class="fa fa-filter me-2"></i>Use the filter above and click Query to view archived articles.</div>';
  document.getElementById('resultInfo').textContent = '';
}

/* ------------------------------------------------------------------ */
/* Exports                                                              */
/* ------------------------------------------------------------------ */

function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('l', 'mm', 'a4');

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(14);
  doc.setTextColor(14, 44, 82);
  doc.text('Archives Data', 14, 15);

  const rows = filteredResults.map(a => [
    a.id,
    a.type || '',
    `${a.writer_first_name || ''} ${a.writer_last_name || ''}`.trim() || '—',
    tsToClock(a.entry_start),
    tsToClock(a.entry_end),
    entryDurationRaw(a),
    `${a.editor_first_name || ''} ${a.editor_last_name || ''}`.trim() || '—',
    tsToClock(a.editing_start),
    tsToClock(a.editing_end),
    editDurationRaw(a),
  ]);

  doc.autoTable({
    head: [['NEWS ID','TYPE','WRITER','ENTRY START','ENTRY END','ENTRY DURATION','EDITOR','EDIT START','EDIT END','EDIT DURATION']],
    body: rows,
    startY: 22,
    styles: {
      fontSize: 7,
      font: 'helvetica',
      textColor: [44, 62, 88],
      lineColor: [227, 235, 245],
      lineWidth: 0.3,
      cellPadding: 2
    },
    headStyles: {
      fillColor: [14, 44, 82],
      textColor: [255, 255, 255],
      fontStyle: 'bold',
      fontSize: 7,
      lineColor: [28, 95, 196],
      lineWidth: 0.5,
      halign: 'center',
      valign: 'middle'
    },
    alternateRowStyles: {
      fillColor: [245, 247, 250],
      lineColor: [227, 235, 245],
      lineWidth: 0.2
    },
    columnStyles: {
      0: { fontStyle: 'bold', textColor: [28, 95, 196], halign: 'left' },
      1: { halign: 'center' },
      2: { halign: 'left' },
      3: { halign: 'center' },
      4: { halign: 'center' },
      5: { halign: 'center' },
      6: { halign: 'left' },
      7: { halign: 'center' },
      8: { halign: 'center' },
      9: { halign: 'center' }
    },
    didParseCell: function(data) {
      if (data.section === 'head') {
        data.cell.styles.halign = 'center';
      }
    },
    margin: { left: 10, right: 10 }
  });

  doc.save('archives.pdf');
}

function styleRawHeaderRow(row) {
  row.eachCell(cell => {
    cell.font = { bold: true, size: 10, name: 'Calibri', color: { argb: 'FFFFFFFF' } };
    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF0E2C52' } };
    cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
    cell.border = {
      top: { style: 'medium', color: { argb: 'FF1C5FC4' } },
      bottom: { style: 'medium', color: { argb: 'FF1C5FC4' } },
      left: { style: 'thin', color: { argb: 'FFE3EBF5' } },
      right: { style: 'thin', color: { argb: 'FFE3EBF5' } }
    };
  });
  row.height = 30;
}

function styleRawBodyRows(ws) {
  const ink = '2C3E58', line = 'E3EBF5', altRow = 'F5F7FA', blue = '1C5FC4';

  for (let r = 2; r <= ws.rowCount; r++) {
    const row = ws.getRow(r);
    const isAlt = r % 2 === 0;

    row.eachCell({ includeEmpty: true }, (cell) => {
      cell.font = { size: 10, name: 'Calibri', color: { argb: 'FF' + ink } };
      cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: isAlt ? 'FF' + altRow : 'FFFFFFFF' } };
      cell.alignment = { vertical: 'middle' };
      cell.border = {
        top: { style: 'thin', color: { argb: 'FF' + line } },
        bottom: { style: 'thin', color: { argb: 'FF' + line } },
        left: { style: 'thin', color: { argb: 'FF' + line } },
        right: { style: 'thin', color: { argb: 'FF' + line } }
      };
    });

    row.getCell(1).font = { bold: true, size: 10, name: 'Calibri', color: { argb: 'FF' + blue } };
    row.height = 20;
  }
}

async function fetchAverages() {
  try {
    const res = await fetch(`${AVERAGES_URL}?${lastQueryParams}`);
    if (!res.ok) throw new Error('Averages request failed');
    return await res.json();
  } catch (e) {
    console.error(e);
    return { monitoring: {}, archiving: {} };
  }
}

/** "2026-01" -> "JANUARY 2026" */
function monthLabelFromKey(key) {
  const [y, m] = key.split('-').map(Number);
  return new Date(y, m - 1, 1)
    .toLocaleString('en-US', { month: 'long', year: 'numeric' })
    .toUpperCase();
}

/** { "2026-01": [...], "2026-02": [...] } -> [{ monthLabel, rows }], chronological. */
function toMonthBlocks(monthMap) {
  return Object.keys(monthMap)
    .sort()
    .map(key => ({ monthLabel: monthLabelFromKey(key), rows: monthMap[key] }));
}

function renderMonthBlocks(ws, blocks, startCol) {
  let row = 1;

  blocks.forEach(block => {
    const titleCell = ws.getCell(row, startCol);
    titleCell.value = block.monthLabel;
    titleCell.font = { bold: true, size: 12, color: { argb: 'FF0E2C52' } };
    row += 1;

    const headers = ['COUNT', 'NAME', 'START', 'END', 'AVERAGE'];
    headers.forEach((h, i) => {
      const cell = ws.getCell(row, startCol + i);
      cell.value = h;
      cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
      cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFED7D31' } };
      cell.alignment = { horizontal: 'center', vertical: 'middle' };
      cell.border = {
        top: { style: 'thin', color: { argb: 'FFED7D31' } },
        bottom: { style: 'thin', color: { argb: 'FFED7D31' } },
        left: { style: 'thin', color: { argb: 'FFED7D31' } },
        right: { style: 'thin', color: { argb: 'FFED7D31' } }
      };
    });
    row += 1;

    if (block.rows.length === 0) {
      ws.getCell(row, startCol).value = 'No data';
      ws.getCell(row, startCol).font = { italic: true, color: { argb: 'FF7C8AA3' } };
      row += 1;
    } else {
      block.rows.forEach(r => {
        const cells = [r.count, r.name, r.start, r.end, r.average];
        cells.forEach((val, i) => {
          const cell = ws.getCell(row, startCol + i);
          cell.value = (val === null || val === undefined) ? '—' : val;
          cell.border = {
            top: { style: 'thin', color: { argb: 'FFE3EBF5' } },
            bottom: { style: 'thin', color: { argb: 'FFE3EBF5' } },
            left: { style: 'thin', color: { argb: 'FFE3EBF5' } },
            right: { style: 'thin', color: { argb: 'FFE3EBF5' } }
          };
          if (i >= 2 && typeof val === 'number') {
            cell.numFmt = '0.00';
            cell.alignment = { horizontal: 'center' };
          }
        });
        row += 1;
      });
    }

    row += 1; // blank spacer row before the next month block
  });

  ws.getColumn(startCol).width     = 10; // COUNT
  ws.getColumn(startCol + 1).width = 24; // NAME
  ws.getColumn(startCol + 2).width = 10; // START
  ws.getColumn(startCol + 3).width = 10; // END
  ws.getColumn(startCol + 4).width = 12; // AVERAGE
}

/**
 * Lays out all month blocks for one report into two side-by-side columns
 * (A-E then G-K), first half of the months on the left, second half on
 * the right — matching the source workbook's grid.
 */
function renderReportSheet(wb, sheetName, monthMap) {
  const ws = wb.addWorksheet(sheetName);
  const blocks = toMonthBlocks(monthMap);
  const half = Math.ceil(blocks.length / 2);

  renderMonthBlocks(ws, blocks.slice(0, half), 1); // columns A-E
  if (blocks.length > half) {
    renderMonthBlocks(ws, blocks.slice(half), 7); // columns G-K
  }
}

async function exportExcel() {
  const btn = document.getElementById('btnExcel');
  const originalLabel = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Building...';

  try {
    const wb = new ExcelJS.Workbook();
    wb.creator = 'MMS';
    wb.created = new Date();

    const ws = wb.addWorksheet('Archives', { views: [{ state: 'frozen', ySplit: 1 }] });
    ws.columns = [
      { header: 'NEWS ID',        key: 'id',              width: 24 },
      { header: 'TYPE',           key: 'type',            width: 14 },
      { header: 'WRITER',         key: 'writer',          width: 22 },
      { header: 'ENTRY START',    key: 'entry_start',     width: 20 },
      { header: 'ENTRY END',      key: 'entry_end',       width: 20 },
      { header: 'ENTRY DURATION', key: 'entry_duration',  width: 16 },
      { header: 'EDITOR',         key: 'editor',          width: 22 },
      { header: 'EDIT START',     key: 'editing_start',   width: 20 },
      { header: 'EDIT END',       key: 'editing_end',     width: 20 },
      { header: 'EDIT DURATION',  key: 'edit_duration',   width: 16 },
    ];

    filteredResults.forEach(a => {
      ws.addRow({
        id: a.id,
        type: a.type || '',
        writer: `${a.writer_first_name || ''} ${a.writer_last_name || ''}`.trim(),
        entry_start: tsToClock(a.entry_start),
        entry_end: tsToClock(a.entry_end),
        entry_duration: entryDurationRaw(a),
        editor: `${a.editor_first_name || ''} ${a.editor_last_name || ''}`.trim(),
        editing_start: tsToClock(a.editing_start),
        editing_end: tsToClock(a.editing_end),
        edit_duration: editDurationRaw(a),
      });
    });

    styleRawHeaderRow(ws.getRow(1));
    styleRawBodyRows(ws);
    ws.autoFilter = { from: 'A1', to: 'J1' };

    const { monitoring = {}, archiving = {} } = await fetchAverages();
    renderReportSheet(wb, 'Monitoring', monitoring);
    renderReportSheet(wb, 'Archiving', archiving);

    const buf = await wb.xlsx.writeBuffer();
    const blob = new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    saveAs(blob, 'archives.xlsx');
  } finally {
    btn.disabled = false;
    btn.textContent = originalLabel;
  }
}

document.getElementById('pageSize').addEventListener('change', renderResults);
document.getElementById('btnQuery').addEventListener('click', runQuery);
document.getElementById('btnReset').addEventListener('click', resetFilters);
document.getElementById('btnPdf').addEventListener('click', exportPDF);
document.getElementById('btnExcel').addEventListener('click', exportExcel);
document.getElementById('btnPrint').addEventListener('click', () => window.print());
</script>
</body>
</html>