<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MMS – User Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    body{background:#f4f6f9;font-family:'Segoe UI',sans-serif;}
    .navbar{background:#fff;border-bottom:2px solid #f0f0f0;padding:0 24px;}
    .navbar-brand{font-size:1.5rem;font-weight:900;color:#c0392b !important;letter-spacing:1px;}
    .nav-link{color:#444 !important;font-weight:500;font-size:.93rem;padding:18px 14px !important;}
    .nav-link:hover,.nav-link.active{color:#c0392b !important;border-bottom:2px solid #c0392b;}
    .dropdown-item:hover{color:#c0392b;background:#fef5f5;}
    .badge-version{font-size:.7rem;padding:3px 7px;vertical-align:middle;}
    .user-avatar{width:36px;height:36px;border-radius:50%;background:#ddd;display:inline-flex;align-items:center;justify-content:center;font-weight:700;color:#555;font-size:.85rem;}
    .page-title{font-size:1.25rem;font-weight:700;color:#333;display:flex;align-items:center;gap:10px;margin-bottom:20px;}
    .page-card{background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.07);padding:28px;}
    .breadcrumb{background:transparent;padding:0;font-size:.85rem;}
    .breadcrumb-item a{color:#c0392b;text-decoration:none;}
    .form-label{font-size:.83rem;font-weight:600;color:#555;}
    .form-control:focus,.form-select:focus{border-color:#c0392b;box-shadow:0 0 0 .18rem rgba(192,57,43,.18);}
    .btn-add-user{background:#c0392b;border:none;color:#fff;padding:7px 18px;border-radius:5px;font-size:.88rem;}
    .btn-add-user:hover{background:#a93226;color:#fff;}
    .btn-search-bar{max-width:220px;}
    .table thead th{font-size:.8rem;font-weight:600;color:#888;text-transform:uppercase;border-bottom:2px solid #eee;}
    .table tbody td{font-size:.9rem;color:#444;vertical-align:middle;}
    .table tbody tr:hover{background:#fafafa;}
    .role-badge{font-size:.7rem;padding:3px 9px;border-radius:10px;font-weight:600;}
    .role-admin{background:#fde8e8;color:#c0392b;}
    .role-writer{background:#e8f5e9;color:#27ae60;}
    .role-editor{background:#e3f2fd;color:#1565c0;}
    .status-active{background:#e8f5e9;color:#27ae60;}
    .status-inactive{background:#fde8e8;color:#c0392b;}
    .btn-edit{background:#3498db;color:#fff;border:none;font-size:.78rem;padding:4px 10px;border-radius:4px;}
    .btn-edit:hover{background:#2980b9;}
    .btn-toggle{border:1px solid #dee2e6;background:#fff;color:#444;font-size:.78rem;padding:4px 10px;border-radius:4px;}
    .btn-toggle:hover{background:#f8f9fa;}
    .no-data{color:#aaa;text-align:center;padding:24px;font-size:.9rem;}
    footer{font-size:.8rem;color:#aaa;padding:20px 0;}
    .bell-badge{position:relative;}
    .bell-badge .badge{position:absolute;top:-4px;right:-6px;font-size:.6rem;}
    .modal-header.red{background:#c0392b;color:#fff;}
    .modal-header.red .btn-close{filter:invert(1);}
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
        <li class="nav-item"><a class="nav-link active" href="<?= $dashboardHref ?>"><i class="fa fa-gauge me-1"></i>Dashboard</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fa fa-th me-1"></i>Applications</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= site_url('news') ?>"><i class="fa fa-newspaper me-2 text-muted"></i>News</a></li>
            <li><a class="dropdown-item" href="<?= site_url('archived') ?>"><i class="fa fa-archive me-2 text-muted"></i>Archives</a></li>
            <?php if ($isAdmin): ?>
              <li>
                <a class="dropdown-item" href="<?= site_url('admin/users') ?>">
                  <i class="fa fa-users me-2 text-muted"></i>
                  User Management
                </a>
              </li>
            <?php endif ?>
          </ul>
        </li>
        <?php if ($isAdmin): ?>
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
        <?php endif ?>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fa fa-rotate me-1"></i>Revision <span class="badge bg-success badge-version">V4.0</span></a>
        </li>
      </ul>
      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item bell-badge"><a class="nav-link" href="#"><i class="fa fa-bell fa-lg text-muted"></i><span class="badge bg-danger">3</span></a></li>
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


<div class="container-fluid px-4 py-3">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Admin</a></li>
      <li class="breadcrumb-item active">User Management</li>
    </ol>
  </nav>

  <div class="page-card">
    <div class="page-title"><i class="fa fa-users text-muted"></i>User Management</div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <button class="btn btn-add-user" id="btnAdd"><i class="fa fa-plus me-1"></i>Add New User</button>
      <input type="text" class="form-control btn-search-bar" id="searchInput" placeholder="Search..."/>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr><th>#</th><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody id="tableBody">
          <tr><td colspan="6" class="no-data">Loading...</td></tr>
        </tbody>
      </table>
    </div>

    <div class="mt-2">
      <span class="text-muted" style="font-size:.83rem;" id="pageInfo">Showing 0 entries</span>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header red">
        <h5 class="modal-title" id="modalTitle"><i class="fa fa-user-plus me-2"></i>Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editUserId"/>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="firstName"/>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="lastName"/>
          </div>
          <div class="col-md-6">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="username"/>
          </div>
          <div class="col-md-6">
            <label class="form-label">Password <span id="pwHint" class="text-muted" style="font-weight:400;"></span></label>
            <div class="input-group">
              <input type="password" class="form-control" id="password"/>
              <button class="btn btn-outline-secondary" type="button" id="btnTogglePwd">
                <i class="fa fa-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select" id="role">
              <option value="">-- Select Role --</option>
              <option value="ADMIN">Admin</option>
              <option value="WRITER">Writer</option>
              <option value="EDITOR">Editor</option>
            </select>
          </div>
        </div>
        <div id="formError" class="alert alert-danger mt-3 d-none"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-sm" style="background:#c0392b;color:#fff;" id="btnSaveUser">
          <i class="fa fa-save me-1"></i>Save User
        </button>
      </div>
    </div>
  </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
  <div id="toastMsg" class="toast align-items-center text-bg-success border-0">
    <div class="d-flex">
      <div class="toast-body" id="toastText"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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
<script>
const DATA_URL   = "<?= site_url('admin/users/data') ?>";
const SAVE_URL   = "<?= site_url('admin/users/save') ?>";
const TOGGLE_URL = "<?= site_url('admin/users/toggle-status') ?>";
const CSRF_NAME  = "<?= csrf_token() ?>";

// NOTE: this is now `let`, not `const` — CodeIgniter regenerates the CSRF
// hash after every valid POST, so we must update it from each response
// (see the `if (json.csrfToken) csrfToken = json.csrfToken;` lines below).
// Without this, only the very first POST after page load would succeed and
// every action after that (e.g. Edit, after an earlier Add or Toggle) would
// silently fail with a stale-token 403.
let csrfToken = "<?= csrf_hash() ?>";

const CURRENT_USER_ID = <?= (int) session('user_id') ?>;

let allUsers = [];

function showToast(msg, type='success') {
  const t = document.getElementById('toastMsg');
  t.className = `toast align-items-center text-bg-${type} border-0`;
  document.getElementById('toastText').textContent = msg;
  new bootstrap.Toast(t, {delay:2500}).show();
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

function getRoleBadge(role) {
  const cls = { ADMIN:'role-admin', WRITER:'role-writer', EDITOR:'role-editor' }[role] || '';
  const label = role.charAt(0) + role.slice(1).toLowerCase();
  return `<span class="role-badge ${cls}">${label}</span>`;
}

function getStatusBadge(status) {
  const isActive = status.toUpperCase() === 'ACTIVE';
  return `<span class="role-badge ${isActive ? 'status-active' : 'status-inactive'}">${status}</span>`;
}

async function loadUsers() {
  const search = document.getElementById('searchInput').value;
  const res = await fetch(`${DATA_URL}?search=${encodeURIComponent(search)}`);
  const json = await res.json();
  allUsers = json.data;
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('tableBody');
  if (!allUsers.length) {
    tbody.innerHTML = '<tr><td colspan="6" class="no-data">No users found.</td></tr>';
    document.getElementById('pageInfo').textContent = 'Showing 0 entries';
    return;
  }
  tbody.innerHTML = allUsers.map((u, i) => `
    <tr>
      <td>${i+1}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="user-avatar" style="width:28px;height:28px;font-size:.72rem;">${escapeHtml((u.first_name[0]+u.last_name[0]).toUpperCase())}</div>
          <div class="fw-semibold">${escapeHtml(u.first_name)} ${escapeHtml(u.last_name)}</div>
        </div>
      </td>
      <td><code>${escapeHtml(u.username)}</code></td>
      <td>${getRoleBadge(u.role)}</td>
      <td>${getStatusBadge(u.status)}</td>
      <td>
        <button class="btn btn-edit me-1" data-id="${u.user_id}"><i class="fa fa-pen"></i> Edit</button>
        <button class="btn btn-toggle" data-id="${u.user_id}" data-status="${u.status}">
          ${u.status.toUpperCase() === 'ACTIVE' ? 'Deactivate' : 'Activate'}
        </button>
      </td>
    </tr>`).join('');
  document.getElementById('pageInfo').textContent = `Showing ${allUsers.length} entries`;

  tbody.querySelectorAll('.btn-edit').forEach(btn =>
    btn.addEventListener('click', () => editUser(parseInt(btn.dataset.id)))
  );
  tbody.querySelectorAll('.btn-toggle').forEach(btn =>
    btn.addEventListener('click', () => toggleStatus(parseInt(btn.dataset.id)))
  );
}

function openAddModal() {
  document.getElementById('modalTitle').innerHTML = '<i class="fa fa-user-plus me-2"></i>Add New User';
  document.getElementById('editUserId').value = '';
  ['firstName','lastName','username','password'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('role').value = '';
  document.getElementById('pwHint').textContent = '';
  document.getElementById('formError').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('userModal')).show();
}

function editUser(id) {
  // user_id from the JSON API and id from the button's dataset can end up
  // as different types (e.g. "5" vs 5, depending on the DB driver), so
  // compare numerically rather than with strict equality.
  const u = allUsers.find(x => Number(x.user_id) === Number(id));
  if (!u) {
    showToast('Could not find that user. Try refreshing the page.', 'danger');
    return;
  }
  document.getElementById('modalTitle').innerHTML = '<i class="fa fa-user-edit me-2"></i>Edit User';
  document.getElementById('editUserId').value = id;
  document.getElementById('firstName').value = u.first_name;
  document.getElementById('lastName').value = u.last_name;
  document.getElementById('username').value = u.username;
  document.getElementById('password').value = '';
  document.getElementById('role').value = u.role;
  document.getElementById('pwHint').textContent = '(leave blank to keep current password)';
  document.getElementById('formError').classList.add('d-none');
  new bootstrap.Modal(document.getElementById('userModal')).show();
}

async function saveUser() {
  const errEl = document.getElementById('formError');
  const fd = new FormData();
  fd.set('id', document.getElementById('editUserId').value);
  fd.set('firstName', document.getElementById('firstName').value.trim());
  fd.set('lastName', document.getElementById('lastName').value.trim());
  fd.set('username', document.getElementById('username').value.trim());
  fd.set('password', document.getElementById('password').value.trim());
  fd.set('role', document.getElementById('role').value);
  // CSRF token goes in the form body (like the Maintenance page does),
  // not as a header — CI's CSRF filter reads it from POST by default.
  fd.set(CSRF_NAME, csrfToken);

  if (!fd.get('firstName') || !fd.get('lastName') || !fd.get('username') || !fd.get('role')) {
    errEl.textContent = 'All fields except password (on edit) are required.';
    errEl.classList.remove('d-none');
    return;
  }

  let json;
  try {
    const res = await fetch(SAVE_URL, { method: 'POST', body: fd });
    json = await res.json();
  } catch (e) {
    errEl.textContent = 'Your session may have expired. Please refresh the page and try again.';
    errEl.classList.remove('d-none');
    return;
  }

  if (json.csrfToken) csrfToken = json.csrfToken;

  if (!json.status) {
    errEl.textContent = json.message || 'Something went wrong.';
    errEl.classList.remove('d-none');
    return;
  }

  bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
  showToast(json.message);
  loadUsers();
}

async function toggleStatus(id) {
  const fd = new FormData();
  fd.set(CSRF_NAME, csrfToken);

  let json;
  try {
    const res = await fetch(`${TOGGLE_URL}/${id}`, { method: 'POST', body: fd });
    json = await res.json();
  } catch (e) {
    showToast('Your session may have expired. Please refresh the page and try again.', 'danger');
    return;
  }

  if (json.csrfToken) csrfToken = json.csrfToken;

  showToast(json.message, json.status ? 'success' : 'danger');
  if (json.status) loadUsers();
}

document.getElementById('btnTogglePwd').addEventListener('click', () => {
  const p = document.getElementById('password');
  const i = document.getElementById('eyeIcon');
  if (p.type === 'password') { p.type = 'text'; i.className = 'fa fa-eye-slash'; }
  else { p.type = 'password'; i.className = 'fa fa-eye'; }
});

let searchTimer;
document.getElementById('searchInput').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadUsers, 300);
});
document.getElementById('btnAdd').addEventListener('click', openAddModal);
document.getElementById('btnSaveUser').addEventListener('click', saveUser);

loadUsers();
</script>
</body> 
</html>