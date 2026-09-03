<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MMS – Dashboard</title>
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
     display: flex;
     align-items: center;
     gap: 8px;
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
     font-size: .68rem;
     padding: 3px 8px;
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
     font-size: .82rem;
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

   h2.welcome {
     font-size: 1.55rem;
     font-weight: 800;
     color: var(--navy);
     margin-bottom: 2px;
     letter-spacing: -.2px;
   }

   .welcome-sub {
     font-size: .88rem;
     color: var(--ink-400);
     margin-bottom: 22px;
   }

   .welcome-sub #greetingIcon {
     color: var(--blue-500);
   }

   .card-stat {
     background: #fff;
     border-radius: 14px;
     box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
     padding: 20px;
     border: 1px solid var(--line);
     transition: box-shadow .15s ease, transform .15s ease;
   }

   .card-stat:hover {
     box-shadow: 0 8px 22px rgba(14, 44, 82, .10);
     transform: translateY(-1px);
   }

   .chart-card {
     background: #fff;
     border-radius: 14px;
     box-shadow: 0 2px 14px rgba(14, 44, 82, .06);
     padding: 20px;
     border: 1px solid var(--line);
   }

   .stat-icon-row {
     display: flex;
     align-items: center;
     justify-content: space-between;
     margin-bottom: 6px;
   }

   .stat-icon-chip {
     width: 34px;
     height: 34px;
     border-radius: 9px;
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: .9rem;
     background: var(--blue-100);
     color: var(--blue-700);
   }

   .stat-number {
     font-size: 1.9rem;
     font-weight: 800;
     color: var(--navy);
     line-height: 1;
   }

   .stat-label {
     font-size: .78rem;
     color: var(--ink-400);
     margin-top: 4px;
     font-weight: 600;
     letter-spacing: .2px;
   }

   .stat-chart {
     width: 100%;
     height: 40px;
   }

   .section-title {
     font-size: .8rem;
     font-weight: 700;
     color: var(--ink-400);
     letter-spacing: .4px;
     margin-bottom: 12px;
     text-transform: uppercase;
   }

   /* ============ ACTIVITY LIST ============ */
   .stats-list-item {
     display: flex;
     justify-content: space-between;
     align-items: center;
     padding: 5px 0;
     border-bottom: 1px solid var(--line);
     font-size: .86rem;
   }

   .stats-list-item:last-child {
     border-bottom: none;
   }

   .stats-name {
     color: var(--ink-700);
     font-weight: 500;
   }

   .stats-count {
     color: var(--blue-700);
     font-weight: 700;
     font-size: .9rem;
   }

   .activity-item {
     display: flex;
     align-items: center;
     gap: 10px;
     padding: 9px 4px;
     border-radius: 8px;
     font-size: .85rem;
     transition: background .15s ease;
   }

   .activity-item:hover {
     background: var(--blue-100);
   }

   .act-avatar {
     width: 34px;
     height: 34px;
     border-radius: 50%;
     background: var(--navy);
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: .74rem;
     font-weight: 700;
     color: #fff;
     flex-shrink: 0;
   }

   .act-name {
     font-weight: 600;
     color: var(--ink-700);
   }

   .act-role {
     color: var(--ink-400);
     font-size: .76rem;
   }

   .act-role-badge {
     font-size: .68rem;
     font-weight: 700;
     padding: 2px 8px;
     border-radius: 20px;
     background: var(--sky-200);
     color: var(--navy);
     text-transform: uppercase;
     letter-spacing: .3px;
   }

   .activity-empty {
     color: var(--ink-400);
     font-size: .85rem;
     text-align: center;
     padding: 24px 0;
   }

   /* ============ ACTIVITY PAGINATION ============ */
   .activity-pagination {
     display: flex;
     align-items: center;
     justify-content: space-between;
     margin-top: 10px;
     padding-top: 10px;
     border-top: 1px solid var(--line);
   }

   .activity-pagination .page-info {
     font-size: .76rem;
     color: var(--ink-400);
     font-weight: 600;
   }

   .activity-pagination .page-btns {
     display: flex;
     gap: 6px;
   }

   .activity-pagination .page-btn {
     border: 1px solid var(--line);
     background: #fff;
     color: var(--ink-700);
     font-size: .76rem;
     padding: 4px 10px;
     border-radius: 6px;
     font-weight: 600;
     transition: background .15s ease, color .15s ease, border-color .15s ease;
   }

   .activity-pagination .page-btn:hover:not(:disabled) {
     background: var(--blue-100);
     color: var(--blue-700);
     border-color: var(--blue-700);
   }

   .activity-pagination .page-btn:disabled {
     opacity: .4;
     cursor: not-allowed;
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
     font-size: .85rem;
     color: var(--ink-700);
     vertical-align: middle;
     border-color: var(--line);
   }

   .table tbody tr:hover {
     background: var(--blue-100);
   }

   .id-chip {
     color: var(--blue-700);
     font-weight: 700;
   }

   .badge-archived {
     background: var(--teal-600);
     color: #fff;
     font-size: .68rem;
     padding: 3px 10px;
     border-radius: 10px;
     font-weight: 600;
   }

   .empty-row {
     color: var(--ink-400);
     text-align: center;
     padding: 26px 0;
   }

   .empty-row i {
     display: block;
     font-size: 1.4rem;
     margin-bottom: 6px;
     color: var(--sky-200);
   }

   footer {
     font-size: .8rem;
     color: var(--ink-400);
     padding: 20px 0;
   }

   canvas {
     max-height: 180px;
   }

   /* ============ TOP NEWS ============ */
   #topNewsChart {
     max-height: none !important;
   }

   .btn-toggle {
     border: 1px solid var(--line);
     background: #fff;
     color: var(--ink-700);
     font-size: .78rem;
     padding: 5px 14px;
     font-weight: 500;
   }

   .btn-toggle:hover {
     background: var(--blue-100);
     color: var(--blue-700);
   }

   .btn-toggle.active {
     background: var(--blue-700);
     color: #fff;
     border-color: var(--blue-700);
   }

   .btn-toggle:focus {
     box-shadow: none;
   }

   /* ============ TOP NEWS SOURCES (Program) ============ */
   .source-row {
     display: flex;
     align-items: center;
     gap: 14px;
     padding: 11px 8px;
     border-bottom: 1px solid var(--line);
     border-radius: 8px;
   }

   .source-row:last-child {
     border-bottom: none;
   }

   .source-row.top {
     background: var(--blue-100);
   }

   .source-label {
     width: 130px;
     flex-shrink: 0;
     font-size: .83rem;
     font-weight: 600;
     color: var(--ink-700);
     white-space: nowrap;
     overflow: hidden;
     text-overflow: ellipsis;
   }

   .source-row.top .source-label {
     color: var(--navy);
   }

   .source-count {
     width: 40px;
     flex-shrink: 0;
     font-size: .83rem;
     color: var(--ink-700);
     font-weight: 700;
     text-align: right;
   }

   .source-bar-track {
     flex: 1;
     height: 14px;
     background: var(--bg);
     border-radius: 7px;
     overflow: hidden;
   }

   .source-bar-fill {
     height: 100%;
     background: var(--blue-700);
     border-radius: 7px;
     transition: width .4s ease;
   }

   @media (max-width:991px) {
     .source-label {
       width: 160px;
     }
   }

   @media (max-width:576px) {
     .source-label {
       width: 110px;
       font-size: .78rem;
     }
   }

   /* ============ OVERALL SLANT ============ */
   .slant-legend-item {
     display: flex;
     align-items: center;
     justify-content: space-between;
     padding: 8px 0;
     border-bottom: 1px solid var(--line);
     font-size: .86rem;
   }

   .slant-legend-item:last-child {
     border-bottom: none;
   }

   .slant-legend-dot {
     width: 12px;
     height: 12px;
     border-radius: 50%;
     display: inline-block;
     margin-right: 8px;
   }

   .slant-legend-label {
     font-weight: 600;
     color: var(--ink-700);
     display: flex;
     align-items: center;
   }

   .slant-legend-value {
     color: var(--ink-400);
     font-weight: 600;
   }

   .slant-legend-value b {
     color: var(--navy);
     margin-right: 4px;
   }

   /* ============ TOP NEWS SOURCES BY SENTIMENT ============ */
   .slant-bar-row {
     display: flex;
     align-items: center;
     gap: 12px;
     padding: 9px 8px;
     border-bottom: 1px solid var(--line);
   }

   .slant-bar-row:last-child {
     border-bottom: none;
   }

   .slant-bar-rank {
     width: 16px;
     flex-shrink: 0;
     font-size: .78rem;
     color: var(--ink-400);
     font-weight: 700;
   }

   .slant-bar-label {
     width: 120px;
     flex-shrink: 0;
     font-size: .82rem;
     font-weight: 600;
     color: var(--ink-700);
     white-space: nowrap;
     overflow: hidden;
     text-overflow: ellipsis;
   }

   .slant-bar-track {
     flex: 1;
     height: 20px;
     border-radius: 6px;
     overflow: hidden;
     display: flex;
     background: var(--bg);
   }

   .slant-seg {
     height: 100%;
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: .68rem;
     font-weight: 700;
     color: #fff;
     white-space: nowrap;
     overflow: hidden;
     transition: width .4s ease;
   }

   .slant-seg.positive {
     background: var(--teal-600);
   }

   .slant-seg.neutral {
     background: #b9c2d0;
     color: var(--navy);
   }

   .slant-seg.negative {
     background: #e0483f;
   }

   .slant-legend-mini {
     display: flex;
     gap: 14px;
     font-size: .76rem;
     color: var(--ink-400);
     margin-bottom: 10px;
     flex-wrap: wrap;
   }

   .slant-legend-mini span {
     display: flex;
     align-items: center;
     gap: 5px;
   }

   .slant-legend-mini i {
     width: 9px;
     height: 9px;
     border-radius: 50%;
     display: inline-block;
   }

   @media (max-width:576px) {
     .slant-bar-label {
       width: 90px;
       font-size: .76rem;
     }
   }

   /* ============ NEWS BY STATION ============ */
   #newsByStationChart {
     max-height: none !important;
   }

   /* ============ NEWS TYPE ============ */
   #newsTypeMonitorSelect {
     border: 1px solid var(--line);
     color: var(--ink-700);
     border-radius: 6px;
   }

   #newsTypeMonitorSelect:focus {
     box-shadow: none;
     border-color: var(--blue-700);
   }
 </style>
     <?= $this->renderSection('styles') ?>
</head>
<body>
<?= $this->renderSection('content') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>