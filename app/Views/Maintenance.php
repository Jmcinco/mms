<?= $this->extend('Layout/Maintenance-Layout') ?>

<?= $this->section('content')?>

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
          <a class="nav-link" href="#"><i class="fa fa-rotate me-1"></i>Revision <span class="badge badge-version">V4.1</span></a>
        </li>
      </ul>
      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item bell-badge"><a class="nav-link" href="#"><i class="fa fa-bell fa-lg"></i><span class="badge">3</span></a></li>
        <li class="nav-item"><div class="user-avatar" id="userInitials">AD</div></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown" id="userName">ADMIN</a>
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


<div class="container-fluid px-4 py-3">
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">

        <li class="breadcrumb-item">
            <a href="<?= $dashboardHref ?>">
                <?= esc(ucfirst(strtolower($role))) ?>
            </a>
        </li>

        <li class="breadcrumb-item active">
            Maintenance
        </li>

    </ol>
</nav>

  <div class="row g-3">
    <!-- Sidebar -->
    <div class="col-lg-2 col-md-3">
      <div class="bg-white rounded-3 shadow-sm p-2" style="border:1px solid var(--line);">
        <div style="font-size:.75rem;font-weight:700;color:var(--ink-400);letter-spacing:.5px;padding:8px 12px 4px;">MODULES</div>
        <nav class="nav flex-column sidebar-nav" id="sidebarNav">
          <?php foreach ($tabs as $slug => $label): ?>
            <a class="nav-link<?= $slug === $tab ? ' active' : '' ?>"
               href="<?= site_url('maintenance/' . $slug) ?>"
               data-tab="<?= esc($slug) ?>"><i class="fa fa-list"></i> <?= esc($label) ?></a>
          <?php endforeach ?>
        </nav>
      </div>
    </div>

    <!-- Main Panel -->
    <div class="col-lg-10 col-md-9">
      <div class="main-panel">
        <div class="panel-title">
          <div class="panel-title-left">
            <i class="fa fa-cog" style="color:var(--blue-700);"></i>
            <span id="panelTitle"><?= esc($tabMeta['label']) ?> Management</span>
          </div>
          <button type="button" class="btn btn-bulk-upload" id="bulkUploadBtn" onclick="openBulkUpload()">
            <i class="fa fa-file-csv me-1"></i>Bulk Upload
          </button>
        </div>
        <div class="row g-2 mb-4 align-items-end" id="addFormRow">
          <?php $addColWidth = count($fields) > 1 ? intdiv(10, count($fields)) : 6; ?>
          <?php foreach ($fields as $f): ?>
            <div class="col-md-<?= $addColWidth ?>">
              <label class="form-label"><?= esc($f['label']) ?></label>
              <input type="text" class="form-control" id="new_<?= esc($f['key']) ?>"
                     placeholder="Enter <?= esc(strtolower($f['label'])) ?>..."/>
            </div>
          <?php endforeach ?>
          <div class="col-auto">
            <button class="btn btn-add-item" onclick="addItem()"><i class="fa fa-plus me-1"></i>Add Item</button>
          </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr id="tableHeadRow">
                <th>#</th>
                <?php foreach ($fields as $f): ?>
                  <th><?= esc($f['label']) ?></th>
                <?php endforeach ?>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <tr><td colspan="<?= count($fields) + 2 ?>" class="no-items">Loading...</td></tr>
            </tbody>
          </table>
        </div>

        <!-- Item count + Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
          <span class="text-muted" style="font-size:.83rem;" id="itemCount"></span>

          <div class="d-flex align-items-center gap-2">
            <span class="pagination-info" id="pageInfo"></span>
            <button class="btn btn-page" id="prevBtn" onclick="changePage(-1)">Previous</button>
            <button class="btn btn-page" id="nextBtn" onclick="changePage(1)">Next</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content">
      <div class="modal-header red">
        <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Edit Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editId"/>
        <!-- Populated dynamically with one input per field via renderEditFields() -->
        <div id="editFieldsContainer"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-sm" style="background:#1c5fc4;color:#fff;" onclick="saveEdit()"><i class="fa fa-save me-1"></i>Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="bulkUploadModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
    <div class="modal-content">
      <div class="modal-header red">
        <h5 class="modal-title"><i class="fa fa-file-csv me-2"></i>Bulk Upload — <span id="bulkModuleLabel">Items</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted" style="font-size:.85rem;">
          Upload a CSV file to add multiple items at once. The first row must be a header
          row with the column name(s) shown below.
        </p>

        <div class="mb-3 d-flex align-items-center gap-2">
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="downloadTemplate()">
            <i class="fa fa-download me-1"></i>Download CSV Template
          </button>
          <span class="bulk-drop-hint" id="bulkFieldHint"></span>
        </div>

        <div class="mb-2">
          <label class="form-label">CSV File</label>
          <input type="file" class="form-control" id="bulkFileInput" accept=".csv,text/csv"/>
          <div class="bulk-drop-hint">Duplicate rows and rows missing required fields are skipped automatically.</div>
        </div>

        <div id="bulkResults" style="display:none;">
          <div class="alert py-2 px-3" style="font-size:.85rem;" id="bulkSummary"></div>
          <ul id="bulkErrorList"></ul>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-sm" style="background:#1c5fc4;color:#fff;" id="bulkSubmitBtn" onclick="submitBulkUpload()">
          <i class="fa fa-upload me-1"></i>Upload
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
  <div id="toastMsg" class="toast align-items-center text-bg-success border-0">
    <div class="d-flex">
      <div class="toast-body" id="toastText">Saved!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<div class="container-fluid px-4">
  <footer class="d-flex justify-content-between">
    <span>Indulged by MISD © <?= date('Y') ?></span>
    <span>Follow us &nbsp;<i class="fab fa-facebook"></i>&nbsp;<i class="fab fa-twitter"></i>&nbsp;<i class="fab fa-google-plus-g"></i></span>
  </footer>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<?= $this->include('JS/Maintenance') ?>
<?= $this->endSection() ?>