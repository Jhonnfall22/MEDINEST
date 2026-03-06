<?php
require_once '../config/db.php';

// Fetch stats
$clinic_count    = $conn->query("SELECT COUNT(*) as total FROM clinics")->fetch_assoc()['total'];
$client_count    = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$pet_count       = $conn->query("SELECT COUNT(*) as total FROM pets")->fetch_assoc()['total'];
$pending_clinics = $conn->query("SELECT COUNT(*) as total FROM clinics WHERE status = 'pending'")->fetch_assoc()['total'];

// Monthly registration data for chart (last 12 months)
$monthly_data = [];
for ($i = 11; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end   = date('Y-m-t', strtotime("-$i months"));
    $label       = date('M', strtotime("-$i months"));

    $clinic_q = $conn->query("SELECT COUNT(*) as c FROM clinics WHERE created_at BETWEEN '$month_start' AND '$month_end 23:59:59'")->fetch_assoc()['c'];
    $client_q = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user' AND created_at BETWEEN '$month_start' AND '$month_end 23:59:59'")->fetch_assoc()['c'];
    $pet_q    = $conn->query("SELECT COUNT(*) as c FROM pets WHERE created_at BETWEEN '$month_start' AND '$month_end 23:59:59'")->fetch_assoc()['c'];

    $monthly_data[] = [
        'label'   => $label,
        'clinics' => (int)$clinic_q,
        'clients' => (int)$client_q,
        'pets'    => (int)$pet_q,
    ];
}

// Recent registrations (limit 6)
$recent_clinics = $conn->query("SELECT clinic_name, status, created_at FROM clinics ORDER BY created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// Quick activity feed
$recent_users = $conn->query("SELECT CONCAT(first_name,' ',last_name) as name, created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 4")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Super Admin | Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --admin-primary: #2563eb;
      --admin-primary-dark: #1d4ed8;
      --admin-primary-light: #3b82f6;
      --admin-primary-50: #eff6ff;
      --admin-primary-100: #dbeafe;

      --accent-green: #10b981;
      --accent-green-50: #ecfdf5;
      --accent-orange: #f59e0b;
      --accent-orange-50: #fffbeb;
      --accent-purple: #8b5cf6;
      --accent-purple-50: #f5f3ff;
      --accent-rose: #f43f5e;
      --accent-rose-50: #fff1f2;

      --bg-body: #f1f5f9;
      --sidebar-bg: #ffffff;
      --card-bg: #ffffff;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --sidebar-width: 272px;
      --sidebar-collapsed-width: 88px;

      --radius-2xl: 20px;
      --radius-xl: 16px;
      --radius-lg: 14px;
      --radius-md: 12px;
      --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
      --shadow-md: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.05);
      --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.05);
      --shadow-xl: 0 20px 25px -5px rgba(0,0,0,.08), 0 8px 10px -6px rgba(0,0,0,.04);
      --shadow-2xl: 0 25px 50px -12px rgba(0,0,0,.15);
    }

    [data-bs-theme="dark"] {
      --bg-body: #0c1017;
      --sidebar-bg: #131820;
      --card-bg: #171d28;
      --text-main: #e2e8f0;
      --text-muted: #94a3b8;
      --border-color: #1e293b;
      --admin-primary-50: rgba(37,99,235,.08);
      --accent-green-50: rgba(16,185,129,.08);
      --accent-orange-50: rgba(245,158,11,.08);
      --accent-purple-50: rgba(139,92,246,.08);
      --accent-rose-50: rgba(244,63,94,.08);
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-body);
      color: var(--text-main);
      transition: background .3s, color .3s;
      overflow-x: hidden;
    }

    /* ─── Sidebar ─── */
    .sidebar {
      width: var(--sidebar-width);
      background: var(--sidebar-bg);
      height: 100vh;
      position: fixed;
      top: 0; left: 0;
      border-right: 1px solid var(--border-color);
      z-index: 1050;
      transition: width .3s cubic-bezier(.4,0,.2,1);
      overflow-x: hidden;
      overflow-y: auto;
    }
    .sidebar::-webkit-scrollbar { width: 0; }

    .sidebar-brand {
      padding: 24px 24px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 900;
      color: var(--admin-primary);
      white-space: nowrap;
      border-bottom: 1px solid var(--border-color);
      font-size: 1rem;
      letter-spacing: -.02em;
    }
    .sidebar-brand .brand-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--admin-primary), var(--accent-purple));
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: .9rem; flex-shrink: 0;
    }

    .sidebar .nav { padding: 12px 12px 24px; }

    .sidebar .nav-link {
      color: var(--text-muted);
      padding: 11px 16px;
      display: flex;
      align-items: center;
      gap: 14px;
      font-weight: 600;
      font-size: .88rem;
      transition: all .2s;
      white-space: nowrap;
      text-decoration: none;
      border-radius: 10px;
      margin-bottom: 4px;
      position: relative;
    }
    .sidebar .nav-link i { width: 20px; text-align: center; font-size: .95rem; }
    .sidebar .nav-link:hover { color: var(--admin-primary); background: var(--admin-primary-50); }
    .sidebar .nav-link.active {
      color: #fff;
      background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
      box-shadow: 0 4px 12px rgba(37,99,235,.3);
    }
    .sidebar .nav-link.active i { color: #fff; }

    .sidebar-collapsed { width: var(--sidebar-collapsed-width); }
    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .brand-text,
    .sidebar-collapsed .nav-badge { opacity: 0; width: 0; pointer-events: none; }
    .sidebar-collapsed .sidebar-brand { justify-content: center; padding: 24px 0 20px; }

    .nav-badge {
      margin-left: auto;
      background: var(--accent-rose);
      color: #fff;
      font-size: .7rem;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 999px;
      transition: opacity .2s;
    }

    /* ─── Main ─── */
    .nxl-container {
      margin-left: var(--sidebar-width);
      padding: 28px 32px;
      transition: margin-left .3s;
      min-height: 100vh;
    }
    .content-collapsed { margin-left: var(--sidebar-collapsed-width); }

    /* ─── Hero ─── */
    .page-hero {
      position: relative;
      border-radius: var(--radius-2xl);
      overflow: hidden;
      min-height: 260px;
      margin-bottom: 28px;
      border: 1px solid rgba(255,255,255,.08);
    }
    .hero-bg {
      position: absolute;
      inset: -60px;
      background:
        radial-gradient(ellipse 900px 400px at 10% 50%, rgba(37,99,235,.45), transparent 60%),
        radial-gradient(ellipse 600px 350px at 90% 40%, rgba(139,92,246,.35), transparent 55%),
        radial-gradient(ellipse 500px 300px at 50% 100%, rgba(16,185,129,.25), transparent 50%),
        linear-gradient(160deg, #0f172a 0%, #1e293b 100%);
      background-size: cover;
      background-position: center;
      transform: translate3d(0,0,0) scale(1.08);
      will-change: transform;
    }
    .hero-particles {
      position: absolute;
      inset: 0;
      overflow: hidden;
      pointer-events: none;
    }
    .hero-particles span {
      position: absolute;
      border-radius: 50%;
      background: rgba(255,255,255,.06);
      animation: float 6s ease-in-out infinite;
    }
    .hero-particles span:nth-child(1) { width: 300px; height: 300px; top: -80px; right: -60px; animation-delay: 0s; }
    .hero-particles span:nth-child(2) { width: 200px; height: 200px; bottom: -60px; left: 10%; animation-delay: 2s; }
    .hero-particles span:nth-child(3) { width: 150px; height: 150px; top: 20%; right: 30%; animation-delay: 4s; }
    .hero-particles span:nth-child(4) { width: 80px; height: 80px; bottom: 20%; right: 15%; animation-delay: 1s; background: rgba(139,92,246,.12); }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50%      { transform: translateY(-20px) rotate(5deg); }
    }

    .hero-content {
      position: relative;
      padding: 44px 40px 40px;
      color: #fff;
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 24px;
      min-height: 260px;
    }
    .hero-greeting { font-size: .85rem; font-weight: 600; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
    .hero-title { font-weight: 900; letter-spacing: -.03em; font-size: clamp(1.6rem, 2.2vw + 1rem, 2.6rem); margin-bottom: 10px; line-height: 1.15; }
    .hero-subtitle { color: rgba(255,255,255,.7); font-size: 1rem; margin: 0; max-width: 55ch; line-height: 1.6; }

    .hero-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
    .hero-btn {
      padding: 10px 14px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(255,255,255,.08);
      backdrop-filter: blur(12px);
      color: #fff;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      gap: 8px;
      font-size: .85rem;
      font-weight: 600;
      transition: all .2s;
      text-decoration: none;
    }
    .hero-btn:hover { background: rgba(255,255,255,.18); color: #fff; }
    .hero-btn i { font-size: .9rem; }

    /* ��── Stat Cards ─── */
    .stat-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      padding: 24px;
      height: 100%;
      transition: all .3s cubic-bezier(.4,0,.2,1);
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: flex-start;
      gap: 18px;
      position: relative;
      overflow: hidden;
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 3px;
      border-radius: 3px 3px 0 0;
    }
    .stat-card.blue::before  { background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-light)); }
    .stat-card.green::before { background: linear-gradient(90deg, #059669, var(--accent-green)); }
    .stat-card.orange::before{ background: linear-gradient(90deg, #d97706, var(--accent-orange)); }
    .stat-card.purple::before{ background: linear-gradient(90deg, #7c3aed, var(--accent-purple)); }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl); }

    .stat-icon {
      width: 52px; height: 52px;
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }
    .stat-icon.blue   { background: var(--admin-primary-50); color: var(--admin-primary); }
    .stat-icon.green  { background: var(--accent-green-50); color: var(--accent-green); }
    .stat-icon.orange { background: var(--accent-orange-50); color: var(--accent-orange); }
    .stat-icon.purple { background: var(--accent-purple-50); color: var(--accent-purple); }

    .stat-info { flex: 1; min-width: 0; }
    .stat-label { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin-bottom: 6px; }
    .stat-value { font-weight: 900; font-size: 1.8rem; letter-spacing: -.02em; line-height: 1; margin-bottom: 4px; }
    .stat-change { font-size: .78rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 999px; }
    .stat-change.up   { background: var(--accent-green-50); color: var(--accent-green); }
    .stat-change.down { background: var(--accent-rose-50); color: var(--accent-rose); }

    /* ─── Cards ─── */
    .card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
      overflow: hidden;
    }
    .card-header-custom {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }
    .card-title { font-weight: 800; margin: 0; font-size: 1rem; letter-spacing: -.01em; }
    .card-subtitle { font-size: .8rem; color: var(--text-muted); margin: 0; }

    /* Chart */
    .chart-wrapper { padding: 24px; position: relative; height: 360px; }

    /* Chart Legend */
    .chart-legend { display: flex; gap: 20px; flex-wrap: wrap; }
    .chart-legend-item { display: flex; align-items: center; gap: 8px; font-size: .82rem; font-weight: 600; color: var(--text-muted); }
    .chart-legend-dot { width: 10px; height: 10px; border-radius: 50%; }

    /* Activity Feed */
    .activity-item {
      padding: 16px 24px;
      display: flex;
      align-items: center;
      gap: 14px;
      border-bottom: 1px solid var(--border-color);
      transition: background .15s;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: var(--admin-primary-50); }

    .activity-icon {
      width: 42px; height: 42px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: .9rem;
      flex-shrink: 0;
    }
    .activity-info { flex: 1; min-width: 0; }
    .activity-name { font-weight: 700; font-size: .88rem; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .activity-meta { font-size: .78rem; color: var(--text-muted); }

    .status-badge {
      padding: 4px 12px;
      border-radius: 999px;
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
    .status-pending  { background: var(--accent-orange-50); color: var(--accent-orange); }
    .status-approved { background: var(--accent-green-50); color: var(--accent-green); }
    .status-rejected { background: var(--accent-rose-50); color: var(--accent-rose); }

    /* Quick Actions */
    .quick-action {
      display: flex; align-items: center; gap: 14px;
      padding: 14px 20px;
      border-radius: 12px;
      border: 1px solid var(--border-color);
      background: var(--card-bg);
      text-decoration: none;
      color: var(--text-main);
      font-weight: 600;
      font-size: .88rem;
      transition: all .2s;
      margin-bottom: 10px;
    }
    .quick-action:hover { border-color: var(--admin-primary); background: var(--admin-primary-50); color: var(--admin-primary); transform: translateX(4px); }
    .quick-action .qa-icon {
      width: 38px; height: 38px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: .85rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
      .sidebar.mobile-show { transform: translateX(0); }
      .nxl-container { margin-left: 0 !important; padding: 20px 16px; }
      .hero-content { padding: 28px 24px 24px; flex-direction: column; align-items: flex-start; }
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 99px; }
  </style>
</head>

<body>

<!-- ─── Sidebar ─── -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="fas fa-shield-halved"></i></div>
    <span class="brand-text">PetCare Admin</span>
  </div>

  <nav class="nav flex-column">
    <a class="nav-link active" href="dashboard.php">
      <i class="fas fa-chart-pie"></i>
      <span class="sidebar-text">Dashboard</span>
    </a>
    <a class="nav-link" href="client.php">
      <i class="fas fa-users"></i>
      <span class="sidebar-text">Clients</span>
      <span class="nav-badge"><?= $client_count ?></span>
    </a>
    <a class="nav-link" href="clinic.php">
      <i class="fas fa-hospital"></i>
      <span class="sidebar-text">Clinics</span>
      <?php if($pending_clinics > 0): ?>
        <span class="nav-badge"><?= $pending_clinics ?></span>
      <?php endif; ?>
    </a>

    <div style="flex:1;"></div>

    <hr class="mx-3 my-2" style="border-color: var(--border-color); opacity:.5;">

    <a class="nav-link" href="#" id="toggleSidebarLink">
      <i class="fas fa-arrows-left-right"></i>
      <span class="sidebar-text">Collapse</span>
    </a>
    <a class="nav-link text-danger" href="logout.php">
      <i class="fas fa-right-from-bracket"></i>
      <span class="sidebar-text">Logout</span>
    </a>
  </nav>
</aside>

<!-- ─── Main Content ─── -->
<main class="nxl-container" id="main-content">

  <!-- Hero Section with Parallax -->
  <section class="page-hero">
    <div class="hero-bg" id="heroBg"></div>
    <div class="hero-particles">
      <span></span><span></span><span></span><span></span>
    </div>
    <div class="hero-content">
      <div>
        <div class="hero-greeting" id="heroGreeting"></div>
        <h1 class="hero-title">Your Dashboard Overview</h1>
        <p class="hero-subtitle">Monitor registrations, review pending clinics, and keep the ecosystem running smoothly.</p>
      </div>
      <div class="hero-actions">
        <a href="clinic.php" class="hero-btn">
          <i class="fas fa-hospital"></i>
          <span>Review Clinics</span>
        </a>
        <button class="hero-btn" id="darkModeBtn" type="button">
          <i id="themeIcon"></i>
          <span id="themeLabel">Dark</span>
        </button>
        <button class="hero-btn d-lg-none" id="toggleSidebar" type="button">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </section>

  <!-- Stat Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-hospital"></i></div>
        <div class="stat-info">
          <div class="stat-label">Total Clinics</div>
          <div class="stat-value"><?= $clinic_count ?></div>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> Active</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-info">
          <div class="stat-label">Total Clients</div>
          <div class="stat-value"><?= $client_count ?></div>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> Growing</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-paw"></i></div>
        <div class="stat-info">
          <div class="stat-label">Registered Pets</div>
          <div class="stat-value"><?= $pet_count ?></div>
          <span class="stat-change up"><i class="fas fa-arrow-up"></i> Active</span>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card purple">
        <div class="stat-icon purple"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
          <div class="stat-label">Pending</div>
          <div class="stat-value"><?= $pending_clinics ?></div>
          <span class="stat-change down"><i class="fas fa-exclamation"></i> Review</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts + Recent -->
  <div class="row g-3 mb-4">
    <!-- Main Chart -->
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header-custom">
          <div>
            <h5 class="card-title">Registration Analytics</h5>
            <p class="card-subtitle">Monthly overview — last 12 months</p>
          </div>
          <div class="chart-legend">
            <div class="chart-legend-item"><div class="chart-legend-dot" style="background:var(--admin-primary);"></div> Clinics</div>
            <div class="chart-legend-item"><div class="chart-legend-dot" style="background:var(--accent-green);"></div> Clients</div>
            <div class="chart-legend-item"><div class="chart-legend-dot" style="background:var(--accent-orange);"></div> Pets</div>
          </div>
        </div>
        <div class="chart-wrapper">
          <canvas id="mainChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Doughnut -->
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header-custom">
          <div>
            <h5 class="card-title">Distribution</h5>
            <p class="card-subtitle">Current totals</p>
          </div>
        </div>
        <div style="padding: 24px; display:flex; align-items:center; justify-content:center; height: calc(100% - 75px);">
          <canvas id="doughnutChart" style="max-height:280px;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Registrations + Quick Actions + Activity -->
  <div class="row g-3">
    <!-- Recent Clinics -->
    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header-custom">
          <div>
            <h5 class="card-title">Recent Clinics</h5>
            <p class="card-subtitle">Latest registrations</p>
          </div>
          <a href="clinic.php" class="text-decoration-none" style="font-size:.82rem; font-weight:700; color:var(--admin-primary);">View All →</a>
        </div>
        <div>
          <?php foreach($recent_clinics as $rc): ?>
            <div class="activity-item">
              <div class="activity-icon" style="background: var(--admin-primary-50); color: var(--admin-primary);">
                <i class="fas fa-hospital"></i>
              </div>
              <div class="activity-info">
                <div class="activity-name"><?= htmlspecialchars($rc['clinic_name']) ?></div>
                <div class="activity-meta"><?= date('M d, Y · h:i A', strtotime($rc['created_at'])) ?></div>
              </div>
              <?php
                $sc = $rc['status'] ?? 'pending';
                $scClass = 'status-pending';
                if ($sc === 'approved') $scClass = 'status-approved';
                elseif ($sc === 'rejected') $scClass = 'status-rejected';
              ?>
              <span class="status-badge <?= $scClass ?>"><?= ucfirst($sc) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if(empty($recent_clinics)): ?>
            <div class="p-4 text-center text-muted">No recent registrations</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-3">
      <div class="card h-100">
        <div class="card-header-custom">
          <div>
            <h5 class="card-title">Quick Actions</h5>
            <p class="card-subtitle">Common tasks</p>
          </div>
        </div>
        <div style="padding: 16px;">
          <a href="clinic.php" class="quick-action">
            <div class="qa-icon" style="background:var(--admin-primary-50); color:var(--admin-primary);">
              <i class="fas fa-clipboard-check"></i>
            </div>
            Review Clinics
          </a>
          <a href="client.php" class="quick-action">
            <div class="qa-icon" style="background:var(--accent-green-50); color:var(--accent-green);">
              <i class="fas fa-user-group"></i>
            </div>
            Manage Clients
          </a>
          <a href="#" class="quick-action">
            <div class="qa-icon" style="background:var(--accent-orange-50); color:var(--accent-orange);">
              <i class="fas fa-chart-bar"></i>
            </div>
            View Reports
          </a>
          <a href="#" class="quick-action">
            <div class="qa-icon" style="background:var(--accent-purple-50); color:var(--accent-purple);">
              <i class="fas fa-cog"></i>
            </div>
            Settings
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header-custom">
          <div>
            <h5 class="card-title">New Users</h5>
            <p class="card-subtitle">Latest client registrations</p>
          </div>
          <a href="client.php" class="text-decoration-none" style="font-size:.82rem; font-weight:700; color:var(--admin-primary);">View All →</a>
        </div>
        <div>
          <?php foreach($recent_users as $ru): ?>
            <div class="activity-item">
              <div class="activity-icon" style="background: var(--accent-green-50); color: var(--accent-green);">
                <i class="fas fa-user"></i>
              </div>
              <div class="activity-info">
                <div class="activity-name"><?= htmlspecialchars($ru['name']) ?></div>
                <div class="activity-meta"><?= date('M d, Y · h:i A', strtotime($ru['created_at'])) ?></div>
              </div>
              <span class="status-badge status-approved">Joined</span>
            </div>
          <?php endforeach; ?>
          <?php if(empty($recent_users)): ?>
            <div class="p-4 text-center text-muted">No recent users</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ─── Data from PHP ───
const chartLabels  = <?= json_encode(array_column($monthly_data, 'label')) ?>;
const chartClinics = <?= json_encode(array_column($monthly_data, 'clinics')) ?>;
const chartClients = <?= json_encode(array_column($monthly_data, 'clients')) ?>;
const chartPets    = <?= json_encode(array_column($monthly_data, 'pets')) ?>;

const totalClinics = <?= (int)$clinic_count ?>;
const totalClients = <?= (int)$client_count ?>;
const totalPets    = <?= (int)$pet_count ?>;

// ─── DOM ───
const root        = document.documentElement;
const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');
const heroBg      = document.getElementById('heroBg');
const darkModeBtn = document.getElementById('darkModeBtn');
const themeIcon   = document.getElementById('themeIcon');
const themeLabel  = document.getElementById('themeLabel');

let mainChart, doughnutChart;

// ─── Greeting ───
function setGreeting() {
  const h = new Date().getHours();
  let g = 'Good Evening';
  if (h < 12) g = 'Good Morning';
  else if (h < 18) g = 'Good Afternoon';
  document.getElementById('heroGreeting').textContent = g + ', Super Admin 👋';
}
setGreeting();

// ─── Theme ───
function applyTheme(theme) {
  root.setAttribute('data-bs-theme', theme);
  localStorage.setItem('admin_theme', theme);

  const isDark = theme === 'dark';
  themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
  themeLabel.textContent = isDark ? 'Light' : 'Dark';

  initMainChart(theme);
  initDoughnutChart(theme);
}

darkModeBtn.addEventListener('click', () => {
  applyTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
});

// ─── Sidebar Toggle ───
function toggleSidebar() {
  if (window.innerWidth > 992) {
    sidebar.classList.toggle('sidebar-collapsed');
    mainContent.classList.toggle('content-collapsed');
  } else {
    sidebar.classList.toggle('mobile-show');
  }
}

document.getElementById('toggleSidebar')?.addEventListener('click', toggleSidebar);
document.getElementById('toggleSidebarLink')?.addEventListener('click', (e) => { e.preventDefault(); toggleSidebar(); });

// ─── Parallax ───
window.addEventListener('scroll', () => {
  if (!heroBg) return;
  const y = window.scrollY || 0;
  heroBg.style.transform = `translate3d(0, ${y * 0.22}px, 0) scale(1.08)`;
}, { passive: true });

// ─── Main Chart ───
function initMainChart(theme) {
  const ctx = document.getElementById('mainChart').getContext('2d');
  if (mainChart) mainChart.destroy();

  const isDark = theme === 'dark';

  const gradientBlue = ctx.createLinearGradient(0, 0, 0, 360);
  gradientBlue.addColorStop(0, 'rgba(37,99,235,.18)');
  gradientBlue.addColorStop(1, 'rgba(37,99,235,.01)');

  const gradientGreen = ctx.createLinearGradient(0, 0, 0, 360);
  gradientGreen.addColorStop(0, 'rgba(16,185,129,.15)');
  gradientGreen.addColorStop(1, 'rgba(16,185,129,.01)');

  const gradientOrange = ctx.createLinearGradient(0, 0, 0, 360);
  gradientOrange.addColorStop(0, 'rgba(245,158,11,.15)');
  gradientOrange.addColorStop(1, 'rgba(245,158,11,.01)');

  mainChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [
        {
          label: 'Clinics',
          data: chartClinics,
          borderColor: '#2563eb',
          backgroundColor: gradientBlue,
          fill: true,
          tension: 0.4,
          borderWidth: 2.5,
          pointRadius: 4,
          pointBackgroundColor: '#2563eb',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverRadius: 7,
        },
        {
          label: 'Clients',
          data: chartClients,
          borderColor: '#10b981',
          backgroundColor: gradientGreen,
          fill: true,
          tension: 0.4,
          borderWidth: 2.5,
          pointRadius: 4,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverRadius: 7,
        },
        {
          label: 'Pets',
          data: chartPets,
          borderColor: '#f59e0b',
          backgroundColor: gradientOrange,
          fill: true,
          tension: 0.4,
          borderWidth: 2.5,
          pointRadius: 4,
          pointBackgroundColor: '#f59e0b',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverRadius: 7,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: isDark ? '#1e293b' : '#fff',
          titleColor: isDark ? '#e2e8f0' : '#1e293b',
          bodyColor: isDark ? '#94a3b8' : '#64748b',
          borderColor: isDark ? '#334155' : '#e2e8f0',
          borderWidth: 1,
          padding: 14,
          cornerRadius: 10,
          titleFont: { weight: '700', size: 13 },
          bodyFont: { size: 12 },
          boxPadding: 6,
          usePointStyle: true,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: isDark ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.04)', drawBorder: false },
          border: { display: false },
          ticks: { color: isDark ? '#64748b' : '#94a3b8', font: { size: 11, weight: '500' }, padding: 10 }
        },
        x: {
          grid: { display: false },
          border: { display: false },
          ticks: { color: isDark ? '#64748b' : '#94a3b8', font: { size: 11, weight: '600' }, padding: 8 }
        }
      }
    }
  });
}

// ─── Doughnut Chart ───
function initDoughnutChart(theme) {
  const ctx = document.getElementById('doughnutChart').getContext('2d');
  if (doughnutChart) doughnutChart.destroy();

  const isDark = theme === 'dark';

  doughnutChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Clinics', 'Clients', 'Pets'],
      datasets: [{
        data: [totalClinics, totalClients, totalPets],
        backgroundColor: ['#2563eb', '#10b981', '#f59e0b'],
        borderColor: isDark ? '#171d28' : '#ffffff',
        borderWidth: 4,
        hoverOffset: 8,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      cutout: '68%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: isDark ? '#94a3b8' : '#64748b',
            font: { size: 12, weight: '600' },
            padding: 16,
            usePointStyle: true,
            pointStyleWidth: 10,
          }
        },
        tooltip: {
          backgroundColor: isDark ? '#1e293b' : '#fff',
          titleColor: isDark ? '#e2e8f0' : '#1e293b',
          bodyColor: isDark ? '#94a3b8' : '#64748b',
          borderColor: isDark ? '#334155' : '#e2e8f0',
          borderWidth: 1,
          padding: 12,
          cornerRadius: 10,
        }
      }
    }
  });
}

// ─── Init ───
document.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('admin_theme') || 'light';
  applyTheme(saved);
});
</script>

</body>
</html>