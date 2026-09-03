<?= $this->extend('Layout/UserDashboard-Layout') ?>

<?= $this->section('content')?>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-fire"></i>MMS</a>
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
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fa fa-cog me-1"></i>Maintenance
                </a>
                
                <ul class="dropdown-menu">
                    <?php if ($isAdmin): ?>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('admin/users') ?>">
                                <i class="fa fa-users me-2 text-muted">User Management</i>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    <?php endif; ?>
                    
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/slants') ?>">
                            <i class="fa fa-tag me-2 text-muted"></i> Slant
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/categories') ?>">
                            <i class="fa fa-list me-2 text-muted"></i> Category
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/subcategories') ?>">
                            <i class="fa fa-list-ul me-2 text-muted"></i>Sub-Category
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/departments') ?>">
                            <i class="fa fa-building me-2 text-muted"></i>
                            Government Offices
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/types') ?>">
                            <i class="fa fa-file-alt me-2 text-muted"></i>Type
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/mediums') ?>">
                            <i class="fa fa-broadcast-tower me-2 text-muted"></i> Medium
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/programs') ?>">
                            <i class="fa fa-tv me-2 text-muted"></i>Program
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/stations') ?>">
                            <i class="fa fa-satellite-dish me-2 text-muted"></i>Station
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('maintenance/reporters') ?>">
                            <i class="fa fa-user-tie me-2 text-muted"></i> Reporter
                        </a>
                    </li>
                </ul>
            </li>        
            
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-rotate me-1"></i>Revision <span class="badge badge-version">V4.1</span></a>
            </li>
        </ul>
        <ul class="navbar-nav align-items-center gap-2">
            <li class="nav-item bell-badge">
                <a class="nav-link" href="#">
                    <i class="fa fa-bell fa-lg"></i><span class="badge">3</span></a></li>
                    <li class="nav-item"><div class="user-avatar" id="userInitials">AD</div></li>
                    <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown" id="userName">ADMIN</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <?php if ($isAdmin): ?>
              <li><a class="dropdown-item" href="<?= site_url('admin/users') ?>"><i class="fa fa-users me-2"></i>User Management</a></li>
              <li><a class="dropdown-item" href="<?= site_url('admin/maintenance') ?>"><i class="fa fa-cog me-2"></i>Maintenance</a></li>
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

<div class="container-fluid px-4 py-4">
  <h2 class="welcome">Welcome to MMS</h2>
  <div class="welcome-sub"><i class="fa fa-circle-info me-1" id="greetingIcon"></i><span id="greetingText">Here's what's happening today.</span></div>

  <!-- TOP ROW: Donut + Today / 2025 / 2026 totals (each with its own trend sparkline) -->
  <div class="row g-3 mb-3">
    <div class="col-xl-3 col-md-6">
      <div class="card-stat h-100">
        <div class="section-title">MMS News Stats</div>
        <div class="d-flex justify-content-center mb-2">
          <canvas id="donutChart" style="max-height:130px;max-width:130px;"></canvas>
        </div>
        <div class="d-flex justify-content-center gap-3" style="font-size:.78rem;">
          <span><span style="display:inline-block;width:10px;height:10px;background:#1c5fc4;border-radius:50%;margin-right:4px;"></span>Today</span>
          <span><span style="display:inline-block;width:10px;height:10px;background:#cfe2fb;border-radius:50%;margin-right:4px;"></span>Yesterday</span>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card-stat h-100">
        <div class="stat-icon-row">
          <div class="stat-icon-chip"><i class="fa fa-calendar-day"></i></div>
        </div>
        <div class="stat-number" id="totalToday">—</div>
        <div class="stat-label">As of Today Total News</div>
        <canvas id="sparkline1" style="height:56px;margin-top:8px;"></canvas>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card-stat h-100">
        <div class="stat-icon-row">
          <div class="stat-icon-chip"><i class="fa fa-newspaper"></i></div>
        </div>
        <div class="stat-number" id="total2025">—</div>
        <div class="stat-label">2025 Total News</div>
        <canvas id="sparkline2025" style="height:56px;margin-top:8px;"></canvas>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card-stat h-100">
        <div class="stat-icon-row">
          <div class="stat-icon-chip"><i class="fa fa-chart-line"></i></div>
        </div>
        <div class="stat-number" id="total2026">—</div>
        <div class="stat-label">2026 Total News</div>
        <canvas id="sparkline2026" style="height:56px;margin-top:8px;"></canvas>
      </div>
    </div>
  </div>

  <!-- LOWER SECTION: Systems Activity (+ News Type below it) | Weekly chart + Recent Articles -->
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card-stat mb-3">
        <div class="section-title">Systems Activity</div>
        <div id="activityList"></div>
        <div class="activity-pagination" id="activityPagination" style="display:none;">
          <span class="page-info" id="activityPageInfo"></span>
          <div class="page-btns">
            <button type="button" class="page-btn" id="activityPrevBtn"><i class="fa fa-chevron-left"></i></button>
            <button type="button" class="page-btn" id="activityNextBtn"><i class="fa fa-chevron-right"></i></button>
          </div>
        </div>
      </div>

      <?php if ($role === 'EDITOR'): ?>
      <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div class="section-title mb-0">News Type</div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-group btn-group-sm" role="group" id="newsTypeScopeToggle">
              <button type="button" class="btn btn-toggle active" data-scope="today">Today</button>
              <button type="button" class="btn btn-toggle" data-scope="all">All-time</button>
            </div>
            <select class="form-select form-select-sm" id="newsTypeMonitorSelect" style="width:auto;font-size:.8rem;">
              <option value="">All Monitors</option>
            </select>
          </div>
        </div>

        <div id="newsTypeWrapper" class="d-flex align-items-center gap-4 flex-wrap">
          <canvas id="newsTypeChart" style="max-height:170px;max-width:170px;"></canvas>
          <div id="newsTypeLegend" class="flex-grow-1"></div>
        </div>

        <div class="empty-row" id="newsTypeEmpty" style="display:none;">
          <i class="fa fa-chart-pie"></i>
          No news type data yet.
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-8">
      <div class="chart-card mb-3">
        <div class="section-title" id="weekLabel">Stats This Week</div>
        <canvas id="barChart" style="height:180px;"></canvas>
      </div>

      <div class="chart-card">
        <div class="section-title mb-2">Recent Articles</div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr><th>ID</th><th>Date</th><th>Type</th><th>Medium</th><th>Status</th></tr>
            </thead>
            <tbody id="recentTable"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- TOP NEWS (by Category / Sub-Category) | TOP NEWS SOURCES (Program) -->
  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="chart-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div class="section-title mb-0">Top News</div>
          <div class="btn-group btn-group-sm" role="group" id="topNewsToggle">
            <button type="button" class="btn btn-toggle active" data-mode="categories">By Category</button>
            <button type="button" class="btn btn-toggle" data-mode="subcategories">By Sub-Category</button>
          </div>
        </div>

        <div id="topNewsChartWrapper" style="position:relative;">
          <canvas id="topNewsChart"></canvas>
        </div>

        <div class="empty-row" id="topNewsEmpty" style="display:none;">
          <i class="fa fa-chart-bar"></i>
          No category data yet.
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="chart-card h-100">
        <div class="section-title mb-3">Top News Sources</div>

        <div id="topSourcesList"></div>

        <div class="empty-row" id="topSourcesEmpty" style="display:none;">
          <i class="fa fa-tv"></i>
          No program data yet.
        </div>
      </div>
    </div>
  </div>

  <!-- OVERALL SLANT | TOP NEWS SOURCES (BY SENTIMENT / SLANT) -->
  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="chart-card h-100">
        <div class="section-title mb-3">Overall Slant</div>

        <div id="overallSlantWrapper" class="d-flex align-items-center gap-4 flex-wrap">
          <canvas id="overallSlantChart" style="max-height:170px;max-width:170px;"></canvas>
          <div id="overallSlantLegend" class="flex-grow-1"></div>
        </div>

        <div class="empty-row" id="overallSlantEmpty" style="display:none;">
          <i class="fa fa-chart-pie"></i>
          No slant data yet.
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="chart-card h-100">
        <div class="section-title mb-2">Top News Sources (By Sentiment)</div>

        <div class="slant-legend-mini">
          <span><i style="background:#0f9d8c;"></i>Positive</span>
          <span><i style="background:#b9c2d0;"></i>Neutral</span>
          <span><i style="background:#e0483f;"></i>Negative</span>
        </div>

        <div id="topSourcesSlantList"></div>

        <div class="empty-row" id="topSourcesSlantEmpty" style="display:none;">
          <i class="fa fa-tv"></i>
          No program sentiment data yet.
        </div>
      </div>
    </div>
  </div>

  <!-- NEWS BY STATION (platform breakdown) -->
  <div class="row g-3 mt-1">
    <div class="col-12">
      <div class="chart-card">
        <div class="section-title mb-3">News by Station</div>

        <div id="newsByStationWrapper" style="position:relative;">
          <canvas id="newsByStationChart"></canvas>
        </div>

        <div class="empty-row" id="newsByStationEmpty" style="display:none;">
          <i class="fa fa-satellite-dish"></i>
          No station data yet.
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid px-4">
  <footer class="d-flex justify-content-between">
    <span>Indulged by MISD © 2020</span>
    <span>Follow us &nbsp;<i class="fab fa-facebook"></i>&nbsp;<i class="fab fa-twitter"></i>&nbsp;<i class="fab fa-google-plus-g"></i></span>
  </footer>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<?= $this->include('JS/UserDashboard') ?>
<?= $this->endSection() ?>