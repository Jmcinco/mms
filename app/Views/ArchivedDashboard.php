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
    :root{
      --navy:      #0e2c52;   /* brand / headings / active nav text   */
      --blue-700:  #1c5fc4;   /* primary accent — buttons, links      */
      --blue-600:  #2f6fe0;   /* hover state of primary               */
      --blue-500:  #4c8bf5;   /* chart lines, secondary accents       */
      --blue-100:  #e6f0fd;   /* light hover / active backgrounds     */
      --sky-200:   #cfe2fb;   /* secondary accents, chips             */
      --ink-700:   #2c3e58;   /* body text                            */
      --ink-400:   #7c8aa3;   /* muted / labels                       */
      --bg:        #eef3f9;   /* page background                      */
      --teal-600:  #0f9d8c;   /* status / success accent              */
      --line:      #e3ebf5;
    }

    body{background:var(--bg);font-family:'Inter',system-ui,-apple-system,sans-serif;color:var(--ink-700);}
    .navbar{background:#fff;border-bottom:1px solid var(--line);padding:0 24px;}
    .navbar-brand{font-size:1.45rem;font-weight:800;color:var(--navy) !important;letter-spacing:.2px;}
    .navbar-brand i{color:var(--blue-700);}
    .nav-link{color:var(--ink-400) !important;font-weight:500;font-size:.93rem;padding:18px 14px !important;border-bottom:2px solid transparent;}
    .nav-link:hover,.nav-link.active{color:var(--blue-700) !important;border-bottom:2px solid var(--blue-700);}
    .dropdown-menu{border:1px solid var(--line);box-shadow:0 10px 30px rgba(14,44,82,.1);border-radius:10px;padding:6px;}
    .dropdown-item{border-radius:6px;}
    .dropdown-item:hover{color:var(--blue-700);background:var(--blue-100);}
    .user-avatar{width:36px;height:36px;border-radius:50%;background:var(--blue-700);display:inline-flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.85rem;}
    .badge-version{font-size:.7rem;padding:3px 7px;vertical-align:middle;background:var(--teal-600) !important;}
    .page-title{font-size:1.3rem;font-weight:700;color:var(--navy);display:flex;align-items:center;gap:10px;margin-bottom:20px;}
    .filter-card{background:#fff;border-radius:14px;box-shadow:0 2px 14px rgba(14,44,82,.06);border:1px solid var(--line);padding:22px;margin-bottom:16px;}
    .filter-title{font-size:.95rem;font-weight:700;color:var(--navy);margin-bottom:16px;}
    .form-select,.form-control{font-size:.87rem;}
    .form-select:focus,.form-control:focus{border-color:var(--blue-700);box-shadow:0 0 0 .18rem rgba(28,95,196,.18);}
    .btn-query{border:1.5px solid #27ae60;color:#27ae60;background:#fff;font-size:.87rem;padding:6px 18px;border-radius:5px;}
    .btn-query:hover{background:#27ae60;color:#fff;}
    .btn-reset-form{border:1.5px solid #e74c3c;color:#e74c3c;background:#fff;font-size:.87rem;padding:6px 18px;border-radius:5px;}
    .btn-reset-form:hover{background:#e74c3c;color:#fff;}
    .query-text-card{background:#fff;border-radius:8px;padding:14px 20px;margin-bottom:16px;border-left:4px solid var(--blue-700);font-size:.87rem;color:var(--ink-700);}
    .query-text-card strong{color:var(--blue-700);}
    .data-card{background:#fff;border-radius:14px;box-shadow:0 2px 14px rgba(14,44,82,.06);border:1px solid var(--line);padding:22px;}
    .data-title{font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:14px;}
    .show-row{display:flex;align-items:center;gap:10px;font-size:.87rem;margin-bottom:14px;}
    .btn-pdf{background:#fff;border:1.5px solid #e74c3c;color:#e74c3c;font-size:.78rem;padding:4px 12px;border-radius:4px;}
    .btn-pdf:hover{background:#e74c3c;color:#fff;}
    .btn-excel{background:#fff;border:1.5px solid #27ae60;color:#27ae60;font-size:.78rem;padding:4px 12px;border-radius:4px;}
    .btn-excel:hover{background:#27ae60;color:#fff;}
    .btn-print{background:#fff;border:1.5px solid var(--ink-400);color:var(--ink-400);font-size:.78rem;padding:4px 12px;border-radius:4px;}
    .btn-print:hover{background:var(--ink-400);color:#fff;}
    .table thead th{font-size:.81rem;font-weight:700;color:var(--ink-400);text-transform:uppercase;letter-spacing:.3px;border-bottom:2px solid var(--line);}
    .table tbody td{font-size:.89rem;color:var(--ink-700);vertical-align:top;border-color:var(--line);}
    .detail-row td{background:var(--blue-100);padding:14px 16px;}
    .detail-label{font-size:.75rem;font-weight:700;color:var(--blue-700);letter-spacing:.5px;margin-right:6px;}
    .detail-value{font-size:.87rem;color:var(--ink-700);}
    .id-chip{color:var(--blue-700);font-weight:700;}
    .no-data{color:var(--ink-400);text-align:center;padding:24px;font-size:.9rem;}
    footer{font-size:.8rem;color:var(--ink-400);padding:20px 0;}
    .bell-badge{position:relative;}
    .bell-badge .fa-bell{color:var(--ink-400) !important;}
    .bell-badge .badge{position:absolute;top:-4px;right:-6px;font-size:.6rem;background:#e0483f !important;}
    .input-group-text{background:var(--blue-100);color:var(--blue-700);font-size:.85rem;border-right:none;}
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
      <div class="col-md-4"><select class="form-select" id="fCategory"><option value="">Choose Category...</option></select></div>
      <div class="col-md-4"><select class="form-select" id="fSubCategory"><option value="">Choose SubCategory...</option></select></div>
      <div class="col-md-4"><select class="form-select" id="fDepartment"><option value="">Choose Department...</option></select></div>
    </div>
    <div class="row g-2 mb-2">
      <div class="col-md-4"><select class="form-select" id="fSlant"><option value="">Choose Slant...</option></select></div>
      <div class="col-md-4"><select class="form-select" id="fType"><option value="">Choose Type...</option></select></div>
      <div class="col-md-4"><select class="form-select" id="fMedium"><option value="">Choose Medium...</option></select></div>
    </div>
    <div class="row g-2 mb-3">
      <div class="col-md-4"><select class="form-select" id="fStation"><option value="">Choose Station...</option></select></div>
      <div class="col-md-4"><select class="form-select" id="fProgram"><option value="">Choose Program...</option></select></div>
      <div class="col-md-4"><select class="form-select" id="fReporter"><option value="">Choose Reporter...</option></select></div>
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
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
const DATA_URL = "<?= site_url('archived/data') ?>";

const OPTIONS = {
  fCategory:    <?= json_encode(['Peace And Order','Politics','Economy','Environment','Health','Sports','Entertainment']) ?>,
  fSubCategory: <?= json_encode(['#Corruption, Anomaly, Misconduct','#DILG concerns','#Iglesia Ni Cristo (INC)','#Protest/rally','#Governance','#Crime & Law Enforcement']) ?>,
  fDepartment:  <?= json_encode(['DILG','DOH','DepEd','DSWD','DOF','DND','DOLE']) ?>,
  fSlant:       <?= json_encode(['+','Neutral','Negative','Positive']) ?>,
  fType:        <?= json_encode(['News','Feature','Editorial','Opinion','Sports','Entertainment']) ?>,
  fMedium:      <?= json_encode(['Online','Print','Radio','TV']) ?>,
  fStation:     <?= json_encode(['Website','DZRH','DZMM','DWIZ','ABS-CBN','GMA-7']) ?>,
  fProgram:     <?= json_encode(['Dzrhnews.com','ABS-CBN News','GMA News','CNN Philippines','Rappler','Inquirer.net']) ?>,
  fReporter:    <?= json_encode(['Elijah Mitra','Juan dela Cruz','Maria Santos','Pedro Reyes','Ana Reyes','Carlos Bautista']) ?>,
};

let filteredResults = [];
let fp;

function formatTime(ts) {
  const d = ts ? new Date(ts) : new Date();
  let h = d.getHours(), m = d.getMinutes(), s = d.getSeconds();
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')} ${ampm}`;
}

function populateFilter(id) {
  const sel = document.getElementById(id);
  OPTIONS[id].forEach(v => sel.appendChild(new Option(v, v)));
}
['fCategory','fSubCategory','fDepartment','fSlant','fType','fMedium','fStation','fProgram','fReporter'].forEach(populateFilter);

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

  const res = await fetch(`${DATA_URL}?${params.toString()}`);
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

  const tableRows = page.map(a => `
    <tr>
      <td><strong class="id-chip">${a.id}</strong></td>
      <td>${a.news_date}</td>
      <td>${a.broadcast_end ? formatTime(a.broadcast_end) : '—'}</td>
      <td>${a.type || '—'}</td>
    </tr>
    <tr class="detail-row">
      <td colspan="4">
        <div><span class="detail-label">CATEGORY</span><span class="detail-value">${a.category || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">SUB-CATEGORY</span><span class="detail-value">${a.sub_category || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">SUMMARY</span><span class="detail-value">${a.summary || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">SLANT</span><span class="detail-value">${a.slant || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">MEDIUM</span><span class="detail-value">${a.medium || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">STATION</span><span class="detail-value">${a.station || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">PROGRAM</span><span class="detail-value">${a.program || '—'}</span></div>
        <div class="mt-1"><span class="detail-label">REPORTER</span><span class="detail-value">${a.reporter || '—'}</span></div>
      </td>
    </tr>`).join('');

  container.innerHTML = `
    <table class="table table-bordered">
      <thead><tr><th>News ID</th><th>News Date</th><th>Broadcast Time</th><th>Type</th></tr></thead>
      <tbody>${tableRows}</tbody>
    </table>`;
  document.getElementById('resultInfo').textContent = `Showing ${Math.min(size, filteredResults.length)} of ${filteredResults.length} results`;
}

function resetFilters() {
  fp.clear();
  document.getElementById('searchText').value = '';
  ['fCategory','fSubCategory','fDepartment','fSlant','fType','fMedium','fStation','fProgram','fReporter']
    .forEach(id => { document.getElementById(id).selectedIndex = 0; });
  filteredResults = [];
  document.getElementById('queryTextBox').style.display = 'none';
  document.getElementById('resultsContainer').innerHTML = '<div class="no-data"><i class="fa fa-filter me-2"></i>Use the filter above and click Query to view archived articles.</div>';
  document.getElementById('resultInfo').textContent = '';
}

function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  doc.text('Archives Data', 14, 15);
  const rows = filteredResults.map(a => [a.id, a.news_date, (a.broadcast_end ? formatTime(a.broadcast_end) : '—'), a.type || '—', a.medium || '—', a.reporter || '—']);
  doc.autoTable({ head: [['News ID','Date','Broadcast Time','Type','Medium','Reporter']], body: rows, startY: 20 });
  doc.save('archives.pdf');
}

function exportExcel() {
  const data = filteredResults.map(a => ({
    'News ID': a.id, 'News Date': a.news_date, 'Broadcast Time': (a.broadcast_end ? formatTime(a.broadcast_end) : ''),
    'Type': a.type || '', 'Category': a.category || '', 'Medium': a.medium || '', 'Reporter': a.reporter || ''
  }));
  const ws = XLSX.utils.json_to_sheet(data);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Archives');
  XLSX.writeFile(wb, 'archives.xlsx');
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