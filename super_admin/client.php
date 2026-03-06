<?php
require_once '../config/db.php';

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM users WHERE role = 'user'";
if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $query .= " AND (first_name LIKE '%$search_safe%' OR last_name LIKE '%$search_safe%' OR email LIKE '%$search_safe%')";
}
$query .= " ORDER BY created_at DESC";
$result = $conn->query($query);
$clients = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch pets per client
$pet_counts = [];
$pc = $conn->query("SELECT user, COUNT(*) as c FROM pets GROUP BY user");
if ($pc) { while($row = $pc->fetch_assoc()) $pet_counts[$row['user']] = $row['c']; }

$client_count = count($clients);
$verified_count = count(array_filter($clients, fn($c) => $c['is_verified']));
$pending_count  = $client_count - $verified_count;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Super Admin | Client Management</title>
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
      --shadow-md: 0 4px 6px -1px rgba(0,0,0,.07),0 2px 4px -2px rgba(0,0,0,.05);
      --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.08),0 4px 6px -4px rgba(0,0,0,.05);
      --shadow-xl: 0 20px 25px -5px rgba(0,0,0,.08),0 8px 10px -6px rgba(0,0,0,.04);
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
      display: flex; align-items: center; gap: 14px;
      font-weight: 600; font-size: .88rem;
      transition: all .2s;
      white-space: nowrap;
      text-decoration: none;
      border-radius: 10px;
      margin-bottom: 4px;
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
      font-size: .7rem; font-weight: 700;
      padding: 2px 8px; border-radius: 999px;
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
      min-height: 220px;
      margin-bottom: 28px;
      border: 1px solid rgba(255,255,255,.08);
    }
    .hero-bg {
      position: absolute;
      inset: -60px;
      background:
        radial-gradient(ellipse 800px 350px at 15% 50%, rgba(16,185,129,.4), transparent 60%),
        radial-gradient(ellipse 600px 300px at 85% 40%, rgba(37,99,235,.35), transparent 55%),
        radial-gradient(ellipse 500px 280px at 50% 100%, rgba(139,92,246,.2), transparent 50%),
        linear-gradient(160deg, #0f172a 0%, #1e293b 100%);
      background-size: cover;
      transform: translate3d(0,0,0) scale(1.08);
      will-change: transform;
    }
    .hero-particles { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .hero-particles span {
      position: absolute; border-radius: 50%;
      background: rgba(255,255,255,.05);
      animation: float 6s ease-in-out infinite;
    }
    .hero-particles span:nth-child(1) { width: 280px; height: 280px; top: -70px; right: -50px; animation-delay: 0s; }
    .hero-particles span:nth-child(2) { width: 180px; height: 180px; bottom: -50px; left: 8%; animation-delay: 2s; }
    .hero-particles span:nth-child(3) { width: 120px; height: 120px; top: 20%; right: 28%; animation-delay: 4s; }
    .hero-particles span:nth-child(4) { width: 70px; height: 70px; bottom: 25%; right: 12%; animation-delay: 1s; background: rgba(16,185,129,.12); }
    @keyframes float {
      0%,100% { transform: translateY(0) rotate(0deg); }
      50%      { transform: translateY(-18px) rotate(5deg); }
    }
    .hero-content {
      position: relative;
      padding: 40px 40px 36px;
      color: #fff;
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 24px;
      min-height: 220px;
    }
    .hero-greeting { font-size: .8rem; font-weight: 600; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
    .hero-title { font-weight: 900; letter-spacing: -.03em; font-size: clamp(1.5rem,2vw + 1rem,2.3rem); margin-bottom: 8px; line-height: 1.15; }
    .hero-subtitle { color: rgba(255,255,255,.7); font-size: .95rem; margin: 0; }
    .hero-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
    .hero-btn {
      padding: 10px 16px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(255,255,255,.08);
      backdrop-filter: blur(12px);
      color: #fff; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      font-size: .85rem; font-weight: 600;
      transition: all .2s; text-decoration: none;
    }
    .hero-btn:hover { background: rgba(255,255,255,.18); color: #fff; }

    /* ─── Stat Mini Cards ─── */
    .mini-stat {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      padding: 20px 22px;
      display: flex; align-items: center; gap: 16px;
      box-shadow: var(--shadow-sm);
      transition: all .3s;
      position: relative; overflow: hidden;
    }
    .mini-stat::before {
      content: ''; position: absolute;
      top: 0; left: 0; width: 100%; height: 3px; border-radius: 3px 3px 0 0;
    }
    .mini-stat.blue::before { background: linear-gradient(90deg, var(--admin-primary), var(--admin-primary-light)); }
    .mini-stat.green::before { background: linear-gradient(90deg, #059669, var(--accent-green)); }
    .mini-stat.orange::before { background: linear-gradient(90deg, #d97706, var(--accent-orange)); }
    .mini-stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-xl); }
    .mini-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
    }
    .mini-icon.blue  { background: var(--admin-primary-50); color: var(--admin-primary); }
    .mini-icon.green { background: var(--accent-green-50);  color: var(--accent-green); }
    .mini-icon.orange{ background: var(--accent-orange-50); color: var(--accent-orange); }
    .mini-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin-bottom: 4px; }
    .mini-value { font-weight: 900; font-size: 1.6rem; letter-spacing: -.02em; line-height: 1; }

    /* ─── Card ─── */
    .card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
    }
    .card-header-custom {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border-color);
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 12px;
    }
    .card-title { font-weight: 800; margin: 0; font-size: 1rem; letter-spacing: -.01em; }
    .card-subtitle { font-size: .8rem; color: var(--text-muted); margin: 2px 0 0; }

    /* ─── Search ─── */
    .search-wrap { position: relative; width: 280px; }
    .search-wrap input {
      padding: 9px 14px 9px 40px;
      border-radius: 10px;
      border: 1px solid var(--border-color);
      background: var(--bg-body);
      color: var(--text-main);
      font-size: .85rem; font-weight: 500;
      width: 100%; outline: none; transition: border .2s;
    }
    .search-wrap input:focus { border-color: var(--admin-primary); }
    .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: .85rem; }

    /* ─── Table ─── */
    .table { margin: 0; }
    .table thead th {
      background: var(--bg-body);
      font-size: .72rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .06em;
      color: var(--text-muted);
      padding: 14px 20px;
      border-bottom: 1px solid var(--border-color);
      border-top: none; white-space: nowrap;
    }
    .table tbody td {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
    }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr { transition: background .15s; }
    .table tbody tr:hover { background: var(--admin-primary-50); }

    /* Avatar */
    .avatar {
      width: 42px; height: 42px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: .88rem; flex-shrink: 0;
      background: linear-gradient(135deg, var(--admin-primary), var(--accent-purple));
      color: #fff;
    }

    /* Status Badge */
    .status-badge {
      padding: 4px 12px; border-radius: 999px;
      font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
      display: inline-flex; align-items: center; gap: 5px;
    }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-verified  { background: var(--accent-green-50);  color: var(--accent-green); }
    .badge-verified::before  { background: var(--accent-green); }
    .badge-pending   { background: var(--accent-orange-50); color: var(--accent-orange); }
    .badge-pending::before   { background: var(--accent-orange); }

    /* Action Buttons */
    .btn-action {
      width: 34px; height: 34px; border-radius: 9px;
      display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--border-color);
      background: var(--card-bg);
      color: var(--text-muted);
      transition: all .2s; cursor: pointer; font-size: .82rem;
    }
    .btn-action:hover.view-btn   { background: var(--admin-primary-50); color: var(--admin-primary); border-color: var(--admin-primary); }
    .btn-action:hover.delete-btn { background: var(--accent-rose-50);   color: var(--accent-rose);   border-color: var(--accent-rose); }

    /* ─── Modal ─── */
    .modal-content {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-2xl);
      overflow: hidden;
      box-shadow: 0 25px 60px rgba(0,0,0,.18);
    }
    .modal-header-custom {
      padding: 0;
      position: relative;
    }
    .modal-hero {
      min-height: 130px;
      background:
        radial-gradient(ellipse 600px 200px at 20% 50%, rgba(16,185,129,.4), transparent 60%),
        radial-gradient(ellipse 500px 180px at 80% 50%, rgba(37,99,235,.35), transparent 55%),
        linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      display: flex; align-items: flex-end;
      padding: 24px 28px 0;
      gap: 18px;
    }
    .modal-avatar-wrap {
      position: relative; flex-shrink: 0;
    }
    .modal-avatar {
      width: 72px; height: 72px; border-radius: 18px;
      display: flex; align-items: center; justify-content: center;
      font-weight: 900; font-size: 1.4rem;
      background: linear-gradient(135deg, var(--admin-primary), var(--accent-purple));
      color: #fff;
      border: 3px solid rgba(255,255,255,.15);
      transform: translateY(28px);
      box-shadow: 0 8px 24px rgba(37,99,235,.35);
    }
    .modal-avatar-status {
      position: absolute; bottom: 28px; right: -2px;
      width: 16px; height: 16px; border-radius: 50%;
      border: 2px solid var(--card-bg);
    }
    .modal-hero-text {
      color: #fff; padding-bottom: 14px;
    }
    .modal-hero-name  { font-weight: 900; font-size: 1.15rem; letter-spacing: -.02em; margin-bottom: 2px; }
    .modal-hero-email { font-size: .82rem; color: rgba(255,255,255,.65); }
    .modal-close-btn {
      position: absolute; top: 16px; right: 16px;
      width: 32px; height: 32px; border-radius: 8px;
      background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);
      color: #fff; display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all .2s; font-size: .8rem;
    }
    .modal-close-btn:hover { background: rgba(255,255,255,.2); }

    .modal-body-custom { padding: 52px 28px 28px; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .info-item {
      padding: 14px 16px; border-radius: 12px;
      background: var(--bg-body);
      border: 1px solid var(--border-color);
    }
    .info-item.full { grid-column: 1 / -1; }
    .info-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
    .info-label i { font-size: .75rem; }
    .info-value { font-weight: 700; font-size: .9rem; color: var(--text-main); }
    .info-value.muted { color: var(--text-muted); font-weight: 500; }

    .modal-footer-custom {
      padding: 16px 28px 24px;
      display: flex; gap: 10px; justify-content: flex-end;
      border-top: 1px solid var(--border-color);
    }
    .btn-modal-close {
      padding: 9px 20px; border-radius: 10px;
      background: var(--bg-body); border: 1px solid var(--border-color);
      color: var(--text-muted); font-weight: 600; font-size: .88rem;
      cursor: pointer; transition: all .2s;
    }
    .btn-modal-close:hover { border-color: var(--admin-primary); color: var(--admin-primary); }
    .btn-modal-delete {
      padding: 9px 20px; border-radius: 10px;
      background: var(--accent-rose-50); border: 1px solid var(--accent-rose);
      color: var(--accent-rose); font-weight: 700; font-size: .88rem;
      cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 7px;
    }
    .btn-modal-delete:hover { background: var(--accent-rose); color: #fff; }

    /* Empty state */
    .empty-state { padding: 60px 24px; text-align: center; }
    .empty-state i { font-size: 2.5rem; color: var(--text-muted); opacity: .4; margin-bottom: 16px; }
    .empty-state p { color: var(--text-muted); font-weight: 500; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 99px; }

    @media (max-width: 992px) {
      .sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
      .sidebar.mobile-show { transform: translateX(0); }
      .nxl-container { margin-left: 0 !important; padding: 20px 16px; }
      .hero-content { flex-direction: column; align-items: flex-start; padding: 28px 24px 24px; }
      .info-grid { grid-template-columns: 1fr; }
      .info-item.full { grid-column: 1; }
    }
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
    <a class="nav-link" href="dashboard.php">
      <i class="fas fa-chart-pie"></i>
      <span class="sidebar-text">Dashboard</span>
    </a>
    <a class="nav-link active" href="client.php">
      <i class="fas fa-users"></i>
      <span class="sidebar-text">Clients</span>
      <span class="nav-badge"><?= $client_count ?></span>
    </a>
    <a class="nav-link" href="clinic.php">
      <i class="fas fa-hospital"></i>
      <span class="sidebar-text">Clinics</span>
    </a>

    <div style="flex:1;"></div>
    <hr class="mx-3 my-2" style="border-color:var(--border-color); opacity:.5;">
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

<!-- ─── Main ─── -->
<main class="nxl-container" id="main-content">

  <!-- Hero -->
  <section class="page-hero">
    <div class="hero-bg" id="heroBg"></div>
    <div class="hero-particles">
      <span></span><span></span><span></span><span></span>
    </div>
    <div class="hero-content">
      <div>
        <div class="hero-greeting">Super Admin · Client Management</div>
        <h1 class="hero-title">Manage Clients</h1>
        <p class="hero-subtitle">View, search, and oversee all registered users on the platform.</p>
      </div>
      <div class="hero-actions">
        <a href="index_superadmin.php" class="hero-btn">
          <i class="fas fa-arrow-left"></i>
          <span>Back to Dashboard</span>
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

  <!-- Mini Stats -->
  <div class="row g-3 mb-4">
    <div class="col-sm-4">
      <div class="mini-stat blue">
        <div class="mini-icon blue"><i class="fas fa-users"></i></div>
        <div>
          <div class="mini-label">Total Clients</div>
          <div class="mini-value"><?= $client_count ?></div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="mini-stat green">
        <div class="mini-icon green"><i class="fas fa-circle-check"></i></div>
        <div>
          <div class="mini-label">Verified</div>
          <div class="mini-value"><?= $verified_count ?></div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="mini-stat orange">
        <div class="mini-icon orange"><i class="fas fa-clock"></i></div>
        <div>
          <div class="mini-label">Unverified</div>
          <div class="mini-value"><?= $pending_count ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Card -->
  <div class="card">
    <div class="card-header-custom">
      <div>
        <h5 class="card-title">User Database</h5>
        <p class="card-subtitle"><?= $client_count ?> registered client<?= $client_count !== 1 ? 's' : '' ?></p>
      </div>
      <form method="GET" action="">
        <div class="search-wrap">
          <i class="fas fa-magnifying-glass"></i>
          <input type="text" name="search" placeholder="Search name or email…"
                 value="<?= htmlspecialchars($search) ?>"
                 onchange="this.form.submit()">
        </div>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th style="padding-left:24px;">ID</th>
            <th>Client</th>
            <th>Contact</th>
            <th>Pets</th>
            <th>Registered</th>
            <th>Status</th>
            <th style="text-align:right; padding-right:24px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($clients as $client): ?>
          <?php
            $initials = strtoupper(substr($client['first_name'],0,1).substr($client['last_name'],0,1));
            $pet_c    = $pet_counts[$client['id']] ?? 0;
            $verified = (bool)($client['is_verified'] ?? 0);
          ?>
          <tr>
            <td style="padding-left:24px;">
              <span style="font-size:.78rem; font-weight:700; color:var(--text-muted);">
                #<?= str_pad($client['id'],4,'0',STR_PAD_LEFT) ?>
              </span>
            </td>
            <td>
              <div class="d-flex align-items-center gap-12" style="gap:12px;">
                <div class="avatar"><?= $initials ?></div>
                <div>
                  <div style="font-weight:700; font-size:.9rem;"><?= htmlspecialchars($client['first_name'].' '.$client['last_name']) ?></div>
                  <div style="font-size:.78rem; color:var(--text-muted);"><?= htmlspecialchars($client['email']) ?></div>
                </div>
              </div>
            </td>
            <td>
              <div style="font-size:.85rem; font-weight:600;"><?= htmlspecialchars($client['contact'] ?: '—') ?></div>
              <div style="font-size:.75rem; color:var(--text-muted); max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($client['address'] ?: 'No address') ?></div>
            </td>
            <td>
              <div style="display:flex; align-items:center; gap:6px;">
                <i class="fas fa-paw" style="color:var(--accent-orange); font-size:.8rem;"></i>
                <span style="font-weight:700; font-size:.88rem;"><?= $pet_c ?></span>
              </div>
            </td>
            <td>
              <div style="font-size:.85rem; font-weight:600;"><?= date('M d, Y', strtotime($client['created_at'])) ?></div>
              <div style="font-size:.75rem; color:var(--text-muted);"><?= date('h:i A', strtotime($client['created_at'])) ?></div>
            </td>
            <td>
              <?php if($verified): ?>
                <span class="status-badge badge-verified"><i class="fas fa-check-circle" style="font-size:.7rem;"></i> Verified</span>
              <?php else: ?>
                <span class="status-badge badge-pending"><i class="fas fa-clock" style="font-size:.7rem;"></i> Pending</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right; padding-right:24px;">
              <div style="display:inline-flex; gap:6px;">
                <button class="btn-action view-btn"
                        onclick="openModal(<?= htmlspecialchars(json_encode($client)) ?>, <?= $pet_c ?>)"
                        title="View Details">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn-action delete-btn" title="Delete Client">
                  <i class="fas fa-trash-can"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($clients)): ?>
          <tr>
            <td colspan="7">
              <div class="empty-state">
                <i class="fas fa-users-slash d-block"></i>
                <p>No clients found<?= $search ? ' for "'.$search.'"' : '.' ?></p>
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- ─── View Client Modal ─── -->
<div id="clientModal" style="
  display:none; position:fixed; inset:0; z-index:9999;
  background:rgba(0,0,0,.45); backdrop-filter:blur(4px);
  align-items:center; justify-content:center; padding:16px;
">
  <div style="width:100%; max-width:520px; animation: modalIn .25s ease;">
    <div class="modal-content">

      <!-- Hero Header -->
      <div class="modal-header-custom">
        <div class="modal-hero">
          <div class="modal-avatar-wrap">
            <div class="modal-avatar" id="mAvatar"></div>
            <div class="modal-avatar-status" id="mStatus"></div>
          </div>
          <div class="modal-hero-text">
            <div class="modal-hero-name" id="mName"></div>
            <div class="modal-hero-email" id="mEmail"></div>
          </div>
        </div>
        <button class="modal-close-btn" onclick="closeModal()">
          <i class="fas fa-xmark"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body-custom">
        <div class="info-grid">
          <div class="info-item">
            <div class="info-label"><i class="fas fa-id-badge"></i> Client ID</div>
            <div class="info-value" id="mId"></div>
          </div>
          <div class="info-item">
            <div class="info-label"><i class="fas fa-circle-check"></i> Status</div>
            <div class="info-value" id="mVerified"></div>
          </div>
          <div class="info-item">
            <div class="info-label"><i class="fas fa-phone"></i> Contact</div>
            <div class="info-value" id="mContact"></div>
          </div>
          <div class="info-item">
            <div class="info-label"><i class="fas fa-paw"></i> Pets Registered</div>
            <div class="info-value" id="mPets"></div>
          </div>
          <div class="info-item full">
            <div class="info-label"><i class="fas fa-location-dot"></i> Address</div>
            <div class="info-value" id="mAddress"></div>
          </div>
          <div class="info-item full">
            <div class="info-label"><i class="fas fa-calendar-plus"></i> Registered On</div>
            <div class="info-value" id="mCreated"></div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer-custom">
        <button class="btn-modal-close" onclick="closeModal()">
          <i class="fas fa-xmark" style="margin-right:6px;"></i>Close
        </button>
        <button class="btn-modal-delete">
          <i class="fas fa-trash-can"></i> Delete Client
        </button>
      </div>

    </div>
  </div>
</div>

<style>
@keyframes modalIn {
  from { opacity:0; transform: scale(.95) translateY(10px); }
  to   { opacity:1; transform: scale(1)  translateY(0); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const root       = document.documentElement;
const sidebar    = document.getElementById('sidebar');
const mainContent= document.getElementById('main-content');
const heroBg     = document.getElementById('heroBg');
const darkModeBtn= document.getElementById('darkModeBtn');
const themeIcon  = document.getElementById('themeIcon');
const themeLabel = document.getElementById('themeLabel');

// ─── Theme ───
function applyTheme(theme) {
  root.setAttribute('data-bs-theme', theme);
  localStorage.setItem('admin_theme', theme);
  const isDark = theme === 'dark';
  themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
  themeLabel.textContent = isDark ? 'Light' : 'Dark';
}
darkModeBtn.addEventListener('click', () => {
  applyTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
});

// ─── Sidebar ───
function toggleSidebar() {
  if (window.innerWidth > 992) {
    sidebar.classList.toggle('sidebar-collapsed');
    mainContent.classList.toggle('content-collapsed');
  } else {
    sidebar.classList.toggle('mobile-show');
  }
}
document.getElementById('toggleSidebar')?.addEventListener('click', toggleSidebar);
document.getElementById('toggleSidebarLink')?.addEventListener('click', e => { e.preventDefault(); toggleSidebar(); });

// ─── Parallax ───
window.addEventListener('scroll', () => {
  if (!heroBg) return;
  heroBg.style.transform = `translate3d(0,${window.scrollY*.22}px,0) scale(1.08)`;
}, { passive: true });

// ─── Modal ───
const modal = document.getElementById('clientModal');

function fmt(v) {
  return v || '<span style="color:var(--text-muted);font-weight:500;">—</span>';
}
function fmtDate(d) {
  if (!d) return '—';
  const dt = new Date(d);
  return dt.toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'})
    + ' · ' + dt.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
}

function openModal(client, petCount) {
  const initials = (client.first_name[0]||'') + (client.last_name[0]||'');
  const verified = !!parseInt(client.is_verified);

  document.getElementById('mAvatar').textContent = initials.toUpperCase();
  document.getElementById('mName').textContent = client.first_name + ' ' + client.last_name;
  document.getElementById('mEmail').textContent = client.email;
  document.getElementById('mId').textContent = '#' + String(client.id).padStart(4,'0');
  document.getElementById('mContact').innerHTML = fmt(client.contact);
  document.getElementById('mAddress').innerHTML = fmt(client.address);
  document.getElementById('mPets').textContent = petCount + ' pet' + (petCount !== 1 ? 's' : '');
  document.getElementById('mCreated').textContent = fmtDate(client.created_at);

  const statusEl = document.getElementById('mStatus');
  statusEl.style.background = verified ? 'var(--accent-green)' : 'var(--accent-orange)';

  const verEl = document.getElementById('mVerified');
  verEl.innerHTML = verified
    ? '<span class="status-badge badge-verified"><i class="fas fa-check-circle" style="font-size:.7rem;margin-right:4px;"></i>Verified</span>'
    : '<span class="status-badge badge-pending"><i class="fas fa-clock" style="font-size:.7rem;margin-right:4px;"></i>Pending</span>';

  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// ─── Init ───
document.addEventListener('DOMContentLoaded', () => {
  applyTheme(localStorage.getItem('admin_theme') || 'light');
});
</script>
</body>
</html>