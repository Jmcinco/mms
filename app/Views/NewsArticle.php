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

   .row-locked {
     opacity: .55;
   }

   .row-locked:hover {
     background: transparent !important;
   }

   .lock-badge {
     font-size: .68rem;
     font-weight: 600;
     padding: 2px 8px;
     border-radius: 20px;
     background: var(--bg);
     color: var(--ink-400);
     border: 1px solid var(--line);
     white-space: nowrap;
   }

   .btn-locked {
     background: var(--bg);
     color: var(--ink-400);
     border: 1px solid var(--line);
     font-size: .78rem;
     padding: 4px 10px;
     border-radius: 5px;
     cursor: not-allowed;
   }

   body {
     background: var(--bg);
     font-family: 'Inter', system-ui, -apple-system, sans-serif;
     color: var(--ink-700);
   }

   /* ============ NAVBAR ============ */
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

   /* ============ VIEW MODAL ============ */
   .view-section-title {
     font-size: .78rem;
     font-weight: 700;
     text-transform: uppercase;
     letter-spacing: .3px;
     color: var(--ink-400);
     margin-bottom: 6px;
   }

   .view-content-box {
     border: 1px solid var(--line);
     border-radius: 8px;
     padding: 14px;
     background: var(--bg);
     font-size: .9rem;
     max-height: 260px;
     overflow-y: auto;
   }

   .view-field {
     margin-bottom: 16px;
   }

   .view-field .value {
     font-size: .88rem;
     color: var(--ink-700);
   }

   .view-chip {
     display: inline-block;
     background: var(--blue-100);
     color: var(--blue-700);
     border: 1px solid var(--sky-200);
     border-radius: 4px;
     padding: 3px 9px;
     font-size: .78rem;
     margin: 2px 4px 2px 0;
   }

   .view-status-badge {
     font-size: .72rem;
     font-weight: 700;
     text-transform: uppercase;
     padding: 3px 10px;
     border-radius: 20px;
   }

   .view-status-submitted {
     background: #fff4e0;
     color: #b8790a;
   }

   .view-status-draft {
     background: var(--bg);
     color: var(--ink-400);
   }

   .view-status-archived {
     background: #eaf7f4;
     color: var(--teal-600);
   }
 </style>
</head>
<body>

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
            <li><a class="dropdown-item active" href="<?= site_url('news') ?>"><i class="fa fa-newspaper me-2 text-muted"></i>News</a></li>
            <li><a class="dropdown-item" href="<?= site_url('archived') ?>"><i class="fa fa-archive me-2 text-muted"></i>Archives</a></li>
            <li>
              <a class="dropdown-item" href="<?= site_url('admin/users') ?>">
                <i class="fa fa-users me-2 text-muted"></i>User Management
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
        <li class="nav-item"><div class="user-avatar" id="userInitials"><?= esc(strtoupper(substr(session('first_name') ?? 'U', 0, 1) . substr(session('last_name') ?? 'U', 0, 1))) ?></div></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown" id="userName"><?= esc(strtoupper(trim((session('first_name') ?? '') . ' ' . (session('last_name') ?? ''))) ?: $role) ?></a>
          <ul class="dropdown-menu dropdown-menu-end">
            <?php if ($isAdmin): ?>
              <li><a class="dropdown-item" href="<?= site_url('admin/users') ?>"><i class="fa fa-users me-2"></i>User Management</a></li>
              <li><a class="dropdown-item" href="<?= site_url('maintenance') ?>"><i class="fa fa-cog me-2"></i>Maintenance</a></li>
              <li><hr class="dropdown-divider"/></li>
            <?php endif ?>
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
<?php
$role = strtoupper(session('role') ?? '');

$isWriter = $role === 'WRITER';
$isEditor = $role === 'EDITOR';
$isAdmin  = $role === 'ADMIN';
?>

<div class="container-fluid px-4 py-3">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= site_url('news') ?>">Tables</a>
            </li>
            <li class="breadcrumb-item active">Data Table</li>
        </ol>
    </nav>

    <div class="page-card">

        <div class="page-title">
            <i class="fa fa-database"></i>
            News Articles
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <?php if ($isWriter): ?>

                <a href="<?= site_url('writer/create-article') ?>"
                   class="btn btn-add">
                    <i class="fa fa-plus me-1"></i>
                    Add New
                </a>

            <?php else: ?>

                <div></div>

            <?php endif; ?>

            <input
                type="text"
                class="form-control search-box"
                id="searchInput"
                placeholder="Search..."
                oninput="filterTable()">

        </div>

        <div class="table-responsive">

            <table class="table" id="articlesTable">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>News ID</th>
                        <th>Summary</th>
                        <th>News Date</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody id="tableBody">

                    <tr>
                        <td colspan="5" class="no-data">
                            Loading...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">

            <span id="pageInfo" class="pagination-info"></span>

            <div class="d-flex gap-2">

                <button
                    class="btn btn-page"
                    id="prevBtn"
                    onclick="changePage(-1)">
                    Previous
                </button>

                <button
                    class="btn btn-page"
                    id="nextBtn"
                    onclick="changePage(1)">
                    Next
                </button>

            </div>

        </div>

    </div>

</div>

<?php if ($isWriter): ?>

<!-- =========================================================
     READ-ONLY VIEW MODAL (Writer)
     ---------------------------------------------------------
     Shown instead of navigating to the editor's full
     review/edit page. Populated entirely client-side from the
     row data already returned by /news/data — no extra
     request needed, and nothing here is editable.
========================================================= -->
<div class="modal fade" id="viewArticleModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header border-0">

                <h5 class="modal-title">
                    <i class="fa fa-eye me-2"></i>
                    Article <span id="viewModalId" class="id-chip"></span>
                    <span id="viewModalStatus" class="view-status-badge ms-2"></span>
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-12">
                        <div class="view-field">
                            <div class="view-section-title">Content</div>
                            <div class="view-content-box" id="viewModalContent"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="view-field">
                            <div class="view-section-title">Category</div>
                            <div class="value" id="viewModalCategory">—</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="view-field">
                            <div class="view-section-title">News Date</div>
                            <div class="value" id="viewModalDate">—</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="view-field">
                            <div class="view-section-title">Sub-Category</div>
                            <div class="value" id="viewModalSubCategory">—</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="view-field">
                            <div class="view-section-title">Government Offices</div>
                            <div class="value" id="viewModalGovOffices">—</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="view-field">
                            <div class="view-section-title">Remarks</div>
                            <div class="value" id="viewModalRemarks">—</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="view-field">
                            <div class="view-section-title">Slant</div>
                            <div class="value" id="viewModalSlant">—</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="view-field">
                            <div class="view-section-title">Type</div>
                            <div class="value" id="viewModalType">—</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="view-field">
                            <div class="view-section-title">Medium</div>
                            <div class="value" id="viewModalMedium">—</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="view-field">
                            <div class="view-section-title">Station</div>
                            <div class="value" id="viewModalStation">—</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="view-field">
                            <div class="view-section-title">Program</div>
                            <div class="value" id="viewModalProgram">—</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="view-field">
                            <div class="view-section-title">Anchor / Reporter</div>
                            <div class="value" id="viewModalReporter">—</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="view-field">
                            <div class="view-section-title">Alert</div>
                            <div class="value" id="viewModalAlert">—</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="view-field">
                            <div class="view-section-title">Timestamp</div>
                            <div class="value" id="viewModalTimestamp">—</div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                    class="btn btn-secondary btn-sm"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

<?php endif; ?>

<div class="container-fluid px-4">
  <footer class="d-flex justify-content-between">
    <span>Indulged by MISD © 2020</span>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

const USER_ROLE  = "<?= $role ?>";

const DATA_URL   = "<?= site_url('news/data') ?>";
const EDIT_URL   = "<?= site_url('writer/create-article') ?>";
const VIEW_URL   = "<?= site_url('editor/view-article') ?>";

let currentPage = 1;
let pageSize = 10;
let totalEntries = 0;
let searchTimer = null;

// Rows from the last successful fetch, keyed by id — the View
// Modal reads straight out of this instead of firing a second
// request, since /news/data already returns every column.
let rowsById = {};

function csrfHeaders() {

    const name = document.querySelector('meta[name="csrf-name"]').content;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    return {
        "X-Requested-With":"XMLHttpRequest",
        [name]:token
    };

}

function escapeHtml(str){

    const div=document.createElement("div");

    div.textContent=str ?? "";

    return div.innerHTML;

}

/**
 * ================================================================
 * DECODE MULTIPLE VALUES
 * ================================================================
 *
 * sub_category / gov_offices / reporter can be stored either as a
 * JSON-encoded array (writer save flow) or a comma-separated
 * string (editor update flow) — this normalizes either shape into
 * a clean array of trimmed strings for display.
 */
function decodeMultiple(raw){

    if(raw===null || raw===undefined || raw==="") return [];

    if(Array.isArray(raw)){
        return raw.map(v=>String(v).trim()).filter(Boolean);
    }

    try{
        const parsed=JSON.parse(raw);
        if(Array.isArray(parsed)){
            return parsed.map(v=>String(v).trim()).filter(Boolean);
        }
    }catch(e){
        // not JSON — fall through to CSV handling
    }

    return String(raw)
        .split(",")
        .map(v=>v.trim())
        .filter(Boolean);

}

function renderChips(values){

    if(!values.length) return "—";

    return values
        .map(v=>`<span class="view-chip">${escapeHtml(v)}</span>`)
        .join("");

}

async function fetchArticles(){

    const search=document.getElementById("searchInput").value;

    const response=await fetch(
        `${DATA_URL}?page=${currentPage}&perPage=${pageSize}&search=${encodeURIComponent(search)}`,
        {
            headers:{
                "X-Requested-With":"XMLHttpRequest"
            }
        }
    );

    const json=await response.json();

    totalEntries=json.total ?? 0;

    const rows=json.data ?? [];

    rowsById={};
    rows.forEach(r=>{ rowsById[r.id]=r; });

    renderTable(rows);

}

function renderTable(rows){

    const tbody=document.getElementById("tableBody");

    if(rows.length===0){

        tbody.innerHTML=
        `<tr>
            <td colspan="5" class="no-data">
                No data available
            </td>
        </tr>`;

        return;

    }

    const start=(currentPage-1)*pageSize;

    tbody.innerHTML=rows.map((a,i)=>{

        const summary=a.summary ?? "";

        const shortSummary=summary.length>80
            ? summary.substring(0,80)+"..."
            : summary;

        const isLocked = !!a.is_locked;
        const lockedBySelf = !!a.locked_by_self;
        const lockedByName = a.locked_by_name ?? "another editor";

        // A writer's own DRAFT article is still editable via the
        // create-article form. Anything past DRAFT (submitted,
        // archived) is read-only — including for the writer who
        // created it — hence the View Modal instead of Edit.
        const isDraft = (a.status ?? "").toLowerCase() === "draft";

        const rowClass = isLocked ? "row-locked" : "";

        const lockBadge = isLocked
            ? `
                <span
                    class="lock-badge ms-1"
                    title="Being edited by ${escapeHtml(lockedByName)}"
                >
                    <i class="fa fa-lock"></i>
                    ${escapeHtml(lockedByName)}
                </span>
            `
            : "";

        let actions="";

        if(USER_ROLE==="WRITER"){

                // Writer can only view, never edit — all articles
                // are read-only via the view modal.
                actions=`
                    <button
                        type="button"
                        class="btn btn-edit"
                        onclick="viewArticle('${a.id}')">
                        <i class="fa fa-eye"></i> View
                    </button>
                `;

        }else if(isLocked && !lockedBySelf){

            // EDITOR / ADMIN — locked by someone else: disable the action
            actions=`
                <button
                    type="button"
                    class="btn btn-locked"
                    disabled
                    title="Locked by ${escapeHtml(lockedByName)}">
                    <i class="fa fa-lock"></i> Locked
                </button>
            `;

        }else{

            // EDITOR / ADMIN — full review/edit page
            actions=`
                <a href="${VIEW_URL}/${encodeURIComponent(a.id)}"
                    class="btn btn-edit">
                    <i class="fa fa-eye"></i> View
                </a>
            `;

        }

        return `
        <tr class="${rowClass}">

            <td>${start+i+1}</td>

            <td>
                <span class="fw-semibold id-chip">
                    ${escapeHtml(a.id)}
                </span>
                ${lockBadge}
            </td>

            <td>${escapeHtml(shortSummary)}</td>

            <td>${escapeHtml(a.news_date)}</td>

            <td>${actions}</td>

        </tr>
        `;

    }).join("");

    const end=Math.min(start+pageSize,totalEntries);

    document.getElementById("pageInfo").textContent=
        `Showing ${start+1} to ${end} of ${totalEntries} entries`;

    document.getElementById("prevBtn").disabled=currentPage===1;

    document.getElementById("nextBtn").disabled=end>=totalEntries;

}

function filterTable(){

    clearTimeout(searchTimer);

    searchTimer=setTimeout(()=>{

        currentPage=1;

        fetchArticles();

    },300);

}

function changePage(dir){

    const next=currentPage+dir;

    if(next<1)return;

    currentPage=next;

    fetchArticles();

}

<?php if ($isWriter): ?>

/**
 * ================================================================
 * VIEW ARTICLE (read-only modal)
 * ================================================================
 *
 * Populated entirely from the row already loaded into rowsById —
 * no request is fired, and no field here is editable. This is
 * intentionally the writer's only way to inspect a submitted or
 * archived article; they never reach the editor's review screen.
 */
function viewArticle(id){

    const a=rowsById[id];

    if(!a){
        alert("Unable to load this article. Please refresh and try again.");
        return;
    }

    document.getElementById("viewModalId").textContent=a.id ?? "";

    const status=(a.status ?? "").toLowerCase();
    const statusEl=document.getElementById("viewModalStatus");
    statusEl.textContent=status ? status.charAt(0).toUpperCase()+status.slice(1) : "—";
    statusEl.className="view-status-badge ms-2 view-status-"+(status || "draft");

    // Content is rendered as HTML (same rich-text markup produced
    // by the Quill editor on submit) — not user-editable here.
    document.getElementById("viewModalContent").innerHTML=a.content ?? "";

    document.getElementById("viewModalCategory").textContent=a.category || "—";
    document.getElementById("viewModalDate").textContent=a.news_date || "—";
    document.getElementById("viewModalRemarks").textContent=a.remarks || "—";
    document.getElementById("viewModalSlant").textContent=a.slant || "—";
    document.getElementById("viewModalType").textContent=a.type || "—";
    document.getElementById("viewModalMedium").textContent=a.medium || "—";
    document.getElementById("viewModalStation").textContent=a.station || "—";
    document.getElementById("viewModalProgram").textContent=a.program || "—";
    document.getElementById("viewModalAlert").textContent=a.alert || "No";

    let timestampText = "—";
    const ts = a.entry_end && parseInt(a.entry_end) > 0 ? a.entry_end : (a.entry_start && parseInt(a.entry_start) > 0 ? a.entry_start : null);
    if (ts) {
        const d = new Date(parseInt(ts) * 1000);
        const y = d.getFullYear();
        const mo = String(d.getMonth() + 1).padStart(2, '0');
        const da = String(d.getDate()).padStart(2, '0');
        const h = String(d.getHours()).padStart(2, '0');
        const mi = String(d.getMinutes()).padStart(2, '0');
        const s = String(d.getSeconds()).padStart(2, '0');
        timestampText = `${y}-${mo}-${da}, ${h}:${mi}:${s}`;
    }
    document.getElementById("viewModalTimestamp").textContent=timestampText;

    document.getElementById("viewModalSubCategory").innerHTML=
        renderChips(decodeMultiple(a.sub_category));

    document.getElementById("viewModalGovOffices").innerHTML=
        renderChips(decodeMultiple(a.gov_offices));

    document.getElementById("viewModalReporter").innerHTML=
        renderChips(decodeMultiple(a.reporter));

    new bootstrap.Modal(
        document.getElementById("viewArticleModal")
    ).show();

}

<?php endif; ?>

fetchArticles();

</script>
</body>
</html>