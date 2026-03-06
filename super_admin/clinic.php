<?php
require_once '../config/db.php';

$query  = "SELECT * FROM clinics WHERE status = 'pending' ORDER BY created_at DESC";
$result = $conn->query($query);
$clinics = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$client_count  = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$pending_count = count($clinics);
$all_clinics   = $conn->query("SELECT COUNT(*) as total FROM clinics")->fetch_assoc()['total'];
$approved_count= $conn->query("SELECT COUNT(*) as total FROM clinics WHERE status='approved'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Super Admin | Clinic Manager</title>
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
      --accent-cyan: #06b6d4;
      --accent-cyan-50: #ecfeff;
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

      /* modal vars */
      --primary-blue: #2563eb;
      --primary-blue-dark: #1d4ed8;
      --primary-blue-50: #eff6ff;
      --primary-blue-100: #dbeafe;
      --primary-blue-200: #bfdbfe;
      --slate-50: #f8fafc;
      --slate-100: #f1f5f9;
      --slate-200: #e2e8f0;
      --slate-700: #334155;
      --slate-800: #1e293b;
      --success-green: #059669;
      --success-green-light: #d1fae5;
      --danger-red: #dc2626;
      --danger-red-light: #fee2e2;
      --warning-amber: #d97706;
      --warning-amber-light: #fef3c7;
      --secondary-slate: #64748b;
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
      --accent-cyan-50: rgba(6,182,212,.08);
      --slate-50: #171d28;
      --slate-100: #1e293b;
      --slate-200: #334155;
      --slate-700: #94a3b8;
      --slate-800: #e2e8f0;
      --success-green-light: rgba(5,150,105,.12);
      --danger-red-light: rgba(220,38,38,.12);
      --warning-amber-light: rgba(217,119,6,.12);
      --primary-blue-50: rgba(37,99,235,.08);
      --primary-blue-100: rgba(37,99,235,.12);
      --primary-blue-200: rgba(37,99,235,.2);
    }

    *{ box-sizing: border-box; }

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
    .sidebar::-webkit-scrollbar{ width:0; }
    .sidebar-brand {
      padding: 24px 24px 20px;
      display: flex; align-items: center; gap: 12px;
      font-weight: 900; color: var(--admin-primary);
      white-space: nowrap;
      border-bottom: 1px solid var(--border-color);
      font-size: 1rem; letter-spacing: -.02em;
    }
    .sidebar-brand .brand-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--admin-primary), var(--accent-purple));
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: .9rem; flex-shrink: 0;
    }
    .sidebar .nav{ padding: 12px 12px 24px; }
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
    .sidebar .nav-link i{ width: 20px; text-align: center; font-size: .95rem; }
    .sidebar .nav-link:hover{ color: var(--admin-primary); background: var(--admin-primary-50); }
    .sidebar .nav-link.active{
      color: #fff;
      background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
      box-shadow: 0 4px 12px rgba(37,99,235,.3);
    }
    .sidebar .nav-link.active i{ color: #fff; }
    .sidebar-collapsed{ width: var(--sidebar-collapsed-width); }
    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .brand-text,
    .sidebar-collapsed .nav-badge{ opacity:0; width:0; pointer-events:none; }
    .sidebar-collapsed .sidebar-brand{ justify-content:center; padding:24px 0 20px; }
    .nav-badge{
      margin-left: auto;
      background: var(--accent-rose);
      color: #fff; font-size: .7rem; font-weight: 700;
      padding: 2px 8px; border-radius: 999px; transition: opacity .2s;
    }

    /* ─── Main ─── */
    .nxl-container{
      margin-left: var(--sidebar-width);
      padding: 28px 32px;
      transition: margin-left .3s;
      min-height: 100vh;
    }
    .content-collapsed{ margin-left: var(--sidebar-collapsed-width); }

    /* ─── Hero (new design) ─── */
    .page-hero {
      position: relative;
      border-radius: var(--radius-2xl);
      overflow: hidden;
      margin-bottom: 28px;
      border: 1px solid var(--border-color);
    }

    /* Top band */
    .hero-band {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f2044 100%);
      padding: 32px 36px 0;
      position: relative;
      overflow: hidden;
    }
    .hero-band::before {
      content:'';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 700px 300px at 80% 0%, rgba(37,99,235,.3), transparent 60%),
        radial-gradient(ellipse 400px 250px at 10% 100%, rgba(139,92,246,.25), transparent 55%);
    }
    /* Decorative grid */
    .hero-grid-overlay {
      position: absolute; inset: 0; pointer-events: none;
      background-image:
        linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
      background-size: 40px 40px;
    }
    .hero-band-inner {
      position: relative; z-index: 1;
      display: flex; align-items: flex-start; justify-content: space-between;
      gap: 24px; flex-wrap: wrap;
    }
    .hero-icon-wrap {
      width: 64px; height: 64px;
      background: rgba(37,99,235,.25);
      border: 1px solid rgba(37,99,235,.4);
      border-radius: 18px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem; color: #93c5fd;
      backdrop-filter: blur(10px);
      flex-shrink: 0;
      box-shadow: 0 0 0 8px rgba(37,99,235,.08);
    }
    .hero-text-wrap{ flex: 1; min-width: 0; padding-top: 4px; }
    .hero-eyebrow {
      font-size: .72rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .1em;
      color: #60a5fa; margin-bottom: 8px;
      display: flex; align-items: center; gap: 8px;
    }
    .hero-eyebrow span {
      width: 24px; height: 2px;
      background: linear-gradient(90deg, #60a5fa, transparent);
      display: inline-block; border-radius: 2px;
    }
    .hero-heading {
      font-size: clamp(1.4rem, 1.8vw + .8rem, 2rem);
      font-weight: 900; letter-spacing: -.03em;
      color: #fff; margin-bottom: 10px; line-height: 1.15;
    }
    .hero-heading em {
      font-style: normal;
      background: linear-gradient(90deg, #60a5fa, #a78bfa);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .hero-desc {
      color: rgba(255,255,255,.6); font-size: .88rem;
      line-height: 1.65; max-width: 55ch; margin-bottom: 20px;
    }
    .hero-tag-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 0; }
    .hero-tag {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 5px 12px; border-radius: 999px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      color: rgba(255,255,255,.8);
      font-size: .75rem; font-weight: 600;
      backdrop-filter: blur(8px);
    }
    .hero-tag i { font-size: .7rem; }

    .hero-right-actions {
      display: flex; flex-direction: column; align-items: flex-end;
      gap: 10px; flex-shrink: 0; padding-top: 4px;
    }
    .hero-btn {
      padding: 9px 16px; border-radius: 12px;
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(255,255,255,.08);
      backdrop-filter: blur(12px);
      color: #fff; cursor: pointer;
      display: flex; align-items: center; gap: 8px;
      font-size: .82rem; font-weight: 600;
      transition: all .2s; text-decoration: none;
    }
    .hero-btn:hover{ background: rgba(255,255,255,.18); color: #fff; }
    .hero-btn-row{ display: flex; gap: 8px; }

    /* Stat strip at bottom of hero */
    .hero-stats-strip {
      background: rgba(255,255,255,.04);
      border-top: 1px solid rgba(255,255,255,.07);
      padding: 0 36px;
      display: flex; gap: 0;
      position: relative; z-index: 1;
    }
    .hero-stat-item {
      padding: 18px 0;
      padding-right: 40px;
      margin-right: 40px;
      border-right: 1px solid rgba(255,255,255,.07);
      display: flex; align-items: center; gap: 14px;
    }
    .hero-stat-item:last-child{ border-right: none; margin-right: 0; padding-right: 0; }
    .hsi-icon {
      width: 38px; height: 38px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: .85rem; flex-shrink: 0;
    }
    .hsi-icon.blue  { background: rgba(37,99,235,.2);  color: #60a5fa; }
    .hsi-icon.green { background: rgba(16,185,129,.2); color: #34d399; }
    .hsi-icon.orange{ background: rgba(245,158,11,.2); color: #fbbf24; }
    .hsi-icon.purple{ background: rgba(139,92,246,.2); color: #c4b5fd; }
    .hsi-val {
      font-weight: 900; font-size: 1.35rem; line-height: 1;
      color: #fff; letter-spacing: -.02em; margin-bottom: 2px;
    }
    .hsi-label { font-size: .72rem; font-weight: 600; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .05em; }

    /* ─── Cards ─── */
    .card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      margin-bottom: 24px;
    }
    .card-header-custom {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border-color);
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 12px;
    }
    .card-title-txt { font-weight: 800; font-size: 1rem; letter-spacing: -.01em; }
    .card-sub-txt { font-size: .78rem; color: var(--text-muted); margin: 2px 0 0; }

    .chart-container{ position: relative; height: 260px; width: 100%; }

    /* Search */
    .search-wrap{ position: relative; width: 260px; }
    .search-wrap input{
      padding: 9px 14px 9px 40px;
      border-radius: 10px;
      border: 1px solid var(--border-color);
      background: var(--bg-body);
      color: var(--text-main);
      font-size: .85rem; font-weight: 500;
      width: 100%; outline: none; transition: border .2s;
    }
    .search-wrap input:focus{ border-color: var(--admin-primary); }
    .search-wrap i{ position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: .85rem; }

    /* Table */
    .table{ margin: 0; }
    .table thead th{
      background: var(--bg-body);
      font-size: .72rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: .06em;
      color: var(--text-muted);
      padding: 14px 20px;
      border-bottom: 1px solid var(--border-color);
      border-top: none; white-space: nowrap;
    }
    .table tbody td{
      padding: 16px 20px;
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
    }
    .table tbody tr:last-child td{ border-bottom: none; }
    .table tbody tr{ transition: background .15s; }
    .table tbody tr:hover{ background: var(--admin-primary-50); }

    /* Clinic avatar */
    .clinic-avatar-sm {
      width: 40px; height: 40px; border-radius: 11px;
      background: linear-gradient(135deg, var(--admin-primary-100), var(--admin-primary-50));
      display: flex; align-items: center; justify-content: center;
      color: var(--admin-primary); font-size: .85rem; flex-shrink: 0;
    }
    .doctor-avatar-sm {
      width: 34px; height: 34px; border-radius: 10px;
      background: linear-gradient(135deg, var(--admin-primary), var(--accent-purple));
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: .75rem; font-weight: 800; flex-shrink: 0;
    }

    /* Status badges */
    .s-badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 4px 12px; border-radius: 999px;
      font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
    }
    .s-badge::before{ content:''; width:6px; height:6px; border-radius:50%; flex-shrink:0; }
    .s-pending { background: var(--accent-orange-50); color: var(--accent-orange); }
    .s-pending::before { background: var(--accent-orange); }
    .s-approved { background: var(--accent-green-50); color: var(--accent-green); }
    .s-approved::before { background: var(--accent-green); }
    .s-rejected { background: var(--accent-rose-50); color: var(--accent-rose); }
    .s-rejected::before { background: var(--accent-rose); }

    /* Review button */
    .btn-review {
      padding: 8px 18px; border-radius: 10px;
      background: var(--admin-primary-50); border: 1px solid var(--admin-primary-100);
      color: var(--admin-primary); font-weight: 700; font-size: .82rem;
      cursor: pointer; transition: all .2s;
      display: inline-flex; align-items: center; gap: 7px;
    }
    .btn-review:hover{
      background: var(--admin-primary); color: #fff;
      border-color: var(--admin-primary);
      box-shadow: 0 4px 12px rgba(37,99,235,.3);
    }

    /* Empty */
    .empty-state{ padding: 60px 24px; text-align: center; }
    .empty-state i{ font-size: 2.5rem; color: var(--text-muted); opacity:.35; margin-bottom: 16px; }
    .empty-state p{ color: var(--text-muted); font-weight: 500; }

    /* ─── Review Modal ─── */
    #reviewModal .modal-content {
      border-radius: 18px; overflow: hidden;
      border: 1px solid var(--border-color);
      background: var(--card-bg);
      box-shadow: 0 25px 60px rgba(0,0,0,.18);
    }
    #reviewModal .modal-header {
      background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
      color: white; padding: 1.5rem 1.75rem;
      border-bottom: none; position: relative; overflow: hidden;
    }
    #reviewModal .modal-header::before {
      content:''; position:absolute; top:-50%; right:-20%;
      width:300px; height:300px; background:rgba(255,255,255,.08); border-radius:50%;
    }
    #reviewModal .modal-header::after {
      content:''; position:absolute; bottom:-60%; left:-10%;
      width:200px; height:200px; background:rgba(255,255,255,.04); border-radius:50%;
    }
    #reviewModal .header-content {
      position: relative; z-index: 1;
      display: flex; align-items: center; gap: 1rem;
    }
    #reviewModal .header-icon {
      width: 48px; height: 48px;
      background: rgba(255,255,255,.2); border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem; backdrop-filter: blur(10px);
    }
    #reviewModal .modal-title{ font-size:1.15rem; font-weight:700; margin-bottom:.2rem; }
    #reviewModal .modal-subtitle{ font-size:.82rem; opacity:.85; }
    #reviewModal .btn-close{ filter:brightness(0) invert(1); opacity:.8; position:relative; z-index:1; }
    #reviewModal .btn-close:hover{ opacity:1; }

    #reviewModal .modal-body{
      padding: 1.75rem;
      background: var(--slate-50);
    }
    .clinic-info-card {
      background: var(--card-bg);
      border-radius: 12px; padding: 1.25rem;
      margin-bottom: 1.5rem;
      border: 1px solid var(--slate-200);
      display: flex; align-items: center; gap: 1rem;
    }
    .clinic-info-card .clinic-avatar {
      width: 56px; height: 56px;
      background: linear-gradient(135deg, var(--primary-blue-100), var(--primary-blue-200));
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      color: var(--primary-blue); font-size: 1.5rem;
    }
    .clinic-info-card .clinic-details label{
      font-size:.72rem; color:var(--secondary-slate);
      text-transform:uppercase; letter-spacing:.5px;
      font-weight:700; margin-bottom:.2rem; display:block;
    }
    .clinic-info-card .clinic-details h6{
      font-size:1.05rem; font-weight:800; color:var(--slate-800); margin:0;
    }

    .documents-section{
      background: var(--card-bg);
      border-radius: 12px; border: 1px solid var(--slate-200); overflow: hidden;
    }
    .documents-header {
      padding: 1rem 1.25rem; border-bottom: 1px solid var(--slate-200);
      display: flex; align-items: center; gap: .75rem;
    }
    .documents-header .docs-icon{
      width:36px; height:36px; background:var(--primary-blue-50);
      border-radius:8px; display:flex; align-items:center; justify-content:center;
      color:var(--primary-blue);
    }
    .documents-header h6{ font-weight:700; color:var(--slate-800); margin:0; }
    .documents-header span{ font-size:.75rem; color:var(--secondary-slate); }

    .doc-row{
      padding:1.25rem; border-bottom:1px solid var(--slate-100);
      transition:background .2s;
    }
    .doc-row:last-child{ border-bottom:none; }
    .doc-row:hover{ background:var(--slate-50); }
    .doc-row .doc-icon{
      width:44px; height:44px; border-radius:10px;
      display:flex; align-items:center; justify-content:center;
      font-size:1.1rem; flex-shrink:0;
    }
    .doc-row .doc-icon.verification{ background:linear-gradient(135deg,#dbeafe,#bfdbfe); color:#2563eb; }
    .doc-row .doc-icon.face-auth   { background:linear-gradient(135deg,#fce7f3,#fbcfe8); color:#db2777; }
    .doc-row .doc-icon.id-validation{ background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#059669; }
    .doc-row .doc-title{ font-weight:700; color:var(--slate-800); font-size:.9rem; margin-bottom:.25rem; }
    .doc-row .doc-meta{ font-size:.78rem; color:var(--secondary-slate); display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
    .doc-row .file-chip{
      display:inline-flex; align-items:center; gap:.375rem;
      padding:.25rem .625rem; background:var(--slate-100);
      border-radius:6px; font-size:.73rem; color:var(--slate-700); font-weight:600; margin-top:.5rem;
    }
    .doc-row .file-chip i{ color:var(--primary-blue); }

    .doc-actions{ display:flex; gap:.5rem; flex-shrink:0; }
    .doc-actions .btn{ border-radius:8px; font-size:.8rem; font-weight:600; padding:.45rem .85rem; transition:all .2s; }
    .doc-actions .btn-outline-blue{
      border:1.5px solid var(--primary-blue-200); color:var(--primary-blue); background:transparent;
    }
    .doc-actions .btn-outline-blue:hover{ background:var(--primary-blue-50); border-color:var(--primary-blue); }
    .doc-actions .btn-blue{ background:var(--primary-blue); color:white; border:none; }
    .doc-actions .btn-blue:hover{ background:var(--primary-blue-dark); box-shadow:0 4px 12px rgba(37,99,235,.3); }

    .preview-box{
      margin-top:1rem; padding:1rem; background:var(--slate-50);
      border-radius:10px; border:1px dashed var(--slate-200); min-height:120px;
    }
    .preview-box img{ max-width:100%; border-radius:8px; }
    .preview-box iframe{ width:100%; height:300px; border:none; border-radius:8px; }

    .verification-alert{
      background:linear-gradient(135deg,var(--warning-amber-light),#fde68a);
      border:none; border-radius:10px; padding:1rem 1.25rem;
      display:flex; align-items:center; gap:.75rem; margin-top:1.5rem;
    }
    .verification-alert .alert-icon{
      width:36px; height:36px; background:rgba(217,119,6,.2);
      border-radius:8px; display:flex; align-items:center; justify-content:center;
      color:var(--warning-amber); flex-shrink:0;
    }
    .verification-alert .alert-content{ font-size:.85rem; color:#92400e; }
    .verification-alert .alert-content strong{ display:block; margin-bottom:.1rem; }

    #reviewModal .modal-footer{
      background:var(--card-bg);
      border-top:1px solid var(--slate-200); padding:1.25rem 1.75rem; gap:.75rem;
    }
    #reviewModal .modal-footer .btn{ border-radius:10px; font-weight:700; padding:.7rem 1.5rem; font-size:.875rem; transition:all .2s; }
    .btn-decline{ background:transparent; border:2px solid var(--danger-red); color:var(--danger-red); }
    .btn-decline:hover{ background:var(--danger-red); color:white; box-shadow:0 4px 12px rgba(220,38,38,.3); }
    .btn-approve{ background:linear-gradient(135deg,var(--success-green),#047857); border:none; color:white; }
    .btn-approve:hover{ box-shadow:0 4px 12px rgba(5,150,105,.4); transform:translateY(-1px); }

    /* File preview modal */
    #filePreviewModal .modal-content{ border-radius:16px; overflow:hidden; border:1px solid var(--border-color); background:var(--card-bg); box-shadow:0 25px 60px rgba(0,0,0,.2); }
    #filePreviewModal .modal-header{ background:linear-gradient(135deg,var(--primary-blue),var(--primary-blue-dark)); color:white; padding:1.25rem 1.5rem; border-bottom:none; }
    #filePreviewModal .preview-header-icon{ width:42px; height:42px; background:rgba(255,255,255,.2); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; backdrop-filter:blur(10px); margin-right:.875rem; }
    #filePreviewModal .btn-close{ filter:brightness(0) invert(1); opacity:.8; }
    #filePreviewModal .modal-body{ background:var(--slate-100); padding:1.5rem; min-height:400px; }
    #filePreviewModal #filePreviewBody{ background:var(--card-bg); border-radius:12px; padding:1rem; min-height:350px; display:flex; align-items:center; justify-content:center; border:1px solid var(--slate-200); }
    #filePreviewModal #filePreviewBody img{ max-width:100%; max-height:500px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.1); }
    #filePreviewModal #filePreviewBody iframe{ width:100%; height:500px; border:none; border-radius:8px; }
    #filePreviewModal .modal-footer{ background:var(--card-bg); border-top:1px solid var(--slate-200); padding:1rem 1.5rem; }
    .btn-outline-blue-f{ border:1.5px solid var(--primary-blue); color:var(--primary-blue); background:transparent; border-radius:8px; font-weight:600; padding:.5rem 1rem; cursor:pointer; transition:all .2s; }
    .btn-outline-blue-f:hover{ background:var(--primary-blue); color:#fff; }
    .btn-blue-f{ background:var(--primary-blue); color:white; border:none; border-radius:8px; font-weight:600; padding:.5rem 1.25rem; cursor:pointer; transition:all .2s; }
    .btn-blue-f:hover{ background:var(--primary-blue-dark); }

    @keyframes modalIn{ from{ opacity:0; transform:scale(.95) translateY(10px); } to{ opacity:1; transform:scale(1) translateY(0); } }
    #reviewModal.show .modal-content,
    #filePreviewModal.show .modal-content{ animation:modalIn .25s ease; }

    /* Scrollbar */
    ::-webkit-scrollbar{ width:6px; }
    ::-webkit-scrollbar-track{ background:transparent; }
    ::-webkit-scrollbar-thumb{ background:var(--border-color); border-radius:99px; }

    @media(max-width:992px){
      .sidebar{ transform:translateX(-100%); width:var(--sidebar-width) !important; }
      .sidebar.mobile-show{ transform:translateX(0); }
      .nxl-container{ margin-left:0 !important; padding:20px 16px; }
      .hero-stats-strip{ flex-wrap:wrap; padding:0 20px; }
      .hero-stat-item{ padding-right:20px; margin-right:20px; }
      .hero-band{ padding:24px 20px 0; }
      .hero-band-inner{ flex-direction:column; }
      .hero-right-actions{ flex-direction:row; align-items:flex-start; }
    }
    @media(max-width:600px){
      .hero-stat-item{ flex:1 1 calc(50% - 20px); border-right:none; margin-right:0; border-bottom:1px solid rgba(255,255,255,.07); }
      .hero-stats-strip{ flex-wrap:wrap; }
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
    <a class="nav-link" href="client.php">
      <i class="fas fa-users"></i>
      <span class="sidebar-text">Clients</span>
      <span class="nav-badge"><?= $client_count ?></span>
    </a>
    <a class="nav-link active" href="clinic.php">
      <i class="fas fa-hospital"></i>
      <span class="sidebar-text">Clinics</span>
      <?php if($pending_count > 0): ?>
        <span class="nav-badge"><?= $pending_count ?></span>
      <?php endif; ?>
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

  <!-- ─── HERO (new design) ─── -->
  <section class="page-hero">

    <!-- Top band -->
    <div class="hero-band">
      <div class="hero-grid-overlay"></div>
      <div class="hero-band-inner">

        <div style="display:flex; gap:20px; align-items:flex-start; flex:1; min-width:0;">
          <div class="hero-icon-wrap">
            <i class="fas fa-hospital-user"></i>
          </div>
          <div class="hero-text-wrap">
            <div class="hero-eyebrow">
              <span></span> Clinic Verification Center
            </div>
            <h1 class="hero-heading">
              Review &amp; <em>Approve</em> Clinics
            </h1>
            <p class="hero-desc">
              Validate submitted documents, inspect credentials, and approve or decline
              clinic registrations to keep the platform trusted and secure.
            </p>
            <div class="hero-tag-row">
              <span class="hero-tag"><i class="fas fa-shield-check"></i> Secure Review</span>
              <span class="hero-tag"><i class="fas fa-file-magnifying-glass"></i> Document Check</span>
              <span class="hero-tag"><i class="fas fa-clock-rotate-left"></i> Real-time Queue</span>
            </div>
          </div>
        </div>

        <div class="hero-right-actions">
          <div class="hero-btn-row">
            <button class="hero-btn" id="darkModeBtn" type="button">
              <i id="themeIcon" class="fas fa-moon"></i>
              <span id="themeLabel">Dark</span>
            </button>
            <button class="hero-btn d-lg-none" id="toggleSidebar" type="button">
              <i class="fas fa-bars"></i>
            </button>
          </div>
          <a href="index_superadmin.php" class="hero-btn" style="margin-top:4px;">
            <i class="fas fa-arrow-left"></i>
            <span>Dashboard</span>
          </a>
        </div>

      </div>

      <!-- Stats strip inside hero band -->
      <div class="hero-stats-strip" style="margin-top:28px;">
        <div class="hero-stat-item">
          <div class="hsi-icon blue"><i class="fas fa-hospital"></i></div>
          <div>
            <div class="hsi-val"><?= $all_clinics ?></div>
            <div class="hsi-label">Total Clinics</div>
          </div>
        </div>
        <div class="hero-stat-item">
          <div class="hsi-icon green"><i class="fas fa-circle-check"></i></div>
          <div>
            <div class="hsi-val"><?= $approved_count ?></div>
            <div class="hsi-label">Approved</div>
          </div>
        </div>
        <div class="hero-stat-item">
          <div class="hsi-icon orange"><i class="fas fa-clock"></i></div>
          <div>
            <div class="hsi-val"><?= $pending_count ?></div>
            <div class="hsi-label">Pending Review</div>
          </div>
        </div>
        <div class="hero-stat-item">
          <div class="hsi-icon purple"><i class="fas fa-users"></i></div>
          <div>
            <div class="hsi-val"><?= $client_count ?></div>
            <div class="hsi-label">Clients</div>
          </div>
        </div>
      </div>
    </div><!-- /hero-band -->

  </section><!-- /page-hero -->

  <!-- Chart Card -->
  <div class="card">
    <div class="card-header-custom">
      <div>
        <div class="card-title-txt">Registration Trend</div>
        <div class="card-sub-txt">New clinic registrations — sample overview</div>
      </div>
      <div style="display:flex; gap:12px; align-items:center;">
        <div style="display:flex; align-items:center; gap:6px; font-size:.78rem; font-weight:600; color:var(--text-muted);">
          <span style="width:10px;height:10px;border-radius:50%;background:var(--admin-primary);display:inline-block;"></span> Clinics
        </div>
      </div>
    </div>
    <div style="padding:20px 24px;">
      <div class="chart-container">
        <canvas id="lineChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Table Card -->
  <div class="card">
    <div class="card-header-custom">
      <div>
        <div class="card-title-txt">Verification Queue</div>
        <div class="card-sub-txt"><?= $pending_count ?> clinic<?= $pending_count !== 1 ? 's' : '' ?> awaiting review</div>
      </div>
      <div class="search-wrap">
        <i class="fas fa-magnifying-glass"></i>
        <input type="text" id="tableSearch" placeholder="Search clinic or doctor…">
      </div>
    </div>
    <div class="table-responsive">
      <table class="table" id="clinicTable">
        <thead>
          <tr>
            <th style="padding-left:24px;">Clinic</th>
            <th>Head Doctor</th>
            <th>Submitted</th>
            <th>Status</th>
            <th style="text-align:right; padding-right:24px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($clinics as $clinic): ?>
          <tr>
            <td style="padding-left:24px;">
              <div style="display:flex; align-items:center; gap:12px;">
                <div class="clinic-avatar-sm"><i class="fas fa-hospital"></i></div>
                <div>
                  <div style="font-weight:700; font-size:.9rem;"><?= htmlspecialchars($clinic['clinic_name']) ?></div>
                  <div style="font-size:.75rem; color:var(--text-muted);">
                    <i class="fas fa-location-dot" style="font-size:.68rem;"></i>
                    <?= htmlspecialchars($clinic['address'] ?? '—') ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="doctor-avatar-sm">
                  <?= strtoupper(substr($clinic['first_name']??'',0,1).substr($clinic['last_name']??'',0,1)) ?>
                </div>
                <span style="font-weight:600; font-size:.88rem;">
                  Dr. <?= htmlspecialchars(($clinic['first_name']??'').' '.($clinic['last_name']??'')) ?>
                </span>
              </div>
            </td>
            <td>
              <div style="font-size:.85rem; font-weight:600;"><?= date('M d, Y', strtotime($clinic['created_at'])) ?></div>
              <div style="font-size:.75rem; color:var(--text-muted);"><?= date('h:i A', strtotime($clinic['created_at'])) ?></div>
            </td>
            <td>
              <?php
                $sc = $clinic['status'] ?? 'pending';
                $cls = ['pending'=>'s-pending','approved'=>'s-approved','rejected'=>'s-rejected'][$sc] ?? 's-pending';
              ?>
              <span class="s-badge <?= $cls ?>"><?= ucfirst($sc) ?></span>
            </td>
            <td style="text-align:right; padding-right:24px;">
              <button class="btn-review"
                data-bs-toggle="modal"
                data-bs-target="#reviewModal"
                data-clinic='<?= htmlspecialchars(json_encode($clinic), ENT_QUOTES, "UTF-8") ?>'>
                <i class="fas fa-clipboard-check"></i> Review
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($clinics)): ?>
          <tr><td colspan="5">
            <div class="empty-state">
              <i class="fas fa-hospital-circle-check d-block"></i>
              <p>No pending clinics — all clear!</p>
            </div>
          </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- ─── Review Modal ─── -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="header-content">
          <div class="header-icon"><i class="fas fa-clipboard-check"></i></div>
          <div>
            <h5 class="modal-title">Review Clinic Documents</h5>
            <div class="modal-subtitle">Verify authenticity before approval</div>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="clinic-info-card">
          <div class="clinic-avatar"><i class="fas fa-hospital"></i></div>
          <div class="clinic-details">
            <label>Clinic Name</label>
            <h6 id="modalClinicName">—</h6>
          </div>
          <div class="ms-auto">
            <span class="s-badge s-pending"><i class="fas fa-clock" style="font-size:.7rem;"></i> Pending Review</span>
          </div>
        </div>

        <div class="documents-section">
          <div class="documents-header">
            <div class="docs-icon"><i class="fas fa-folder-open"></i></div>
            <div>
              <h6>Submitted Documents</h6>
              <span>3 documents require verification</span>
            </div>
          </div>

          <!-- Verification Doc -->
          <div class="doc-row">
            <div class="d-flex align-items-start gap-3 flex-wrap">
              <div class="doc-icon verification"><i class="fas fa-file-certificate"></i></div>
              <div class="doc-content">
                <div class="doc-title">Verification Document</div>
                <div class="doc-meta"><i class="fas fa-calendar"></i> <span id="modalVerificationDate">—</span></div>
                <div class="file-chip"><i class="fas fa-file-pdf"></i><span id="modalVerificationFile">—</span></div>
              </div>
              <div class="doc-actions">
                <button type="button" class="btn btn-outline-blue" id="btnInlineVerification"><i class="fas fa-eye me-1"></i>Inline</button>
                <button type="button" class="btn btn-blue" id="btnViewVerification"><i class="fas fa-expand me-1"></i>Full View</button>
              </div>
            </div>
            <div class="preview-box d-none" id="modalViewVerificationPreviewContainer"></div>
          </div>

          <!-- Face Auth -->
          <div class="doc-row">
            <div class="d-flex align-items-start gap-3 flex-wrap">
              <div class="doc-icon face-auth"><i class="fas fa-face-viewfinder"></i></div>
              <div class="doc-content">
                <div class="doc-title">Face Authentication</div>
                <div class="doc-meta"><i class="fas fa-calendar"></i> <span id="modalFaceAuthDate">—</span></div>
                <div class="file-chip"><i class="fas fa-image"></i><span id="modalFaceAuthFile">—</span></div>
              </div>
              <div class="doc-actions">
                <button type="button" class="btn btn-outline-blue" id="btnInlineFaceAuth"><i class="fas fa-eye me-1"></i>Inline</button>
                <button type="button" class="btn btn-blue" id="btnViewFaceAuth"><i class="fas fa-expand me-1"></i>Full View</button>
              </div>
            </div>
            <div class="preview-box d-none" id="modalViewFaceAuthPreviewContainer"></div>
          </div>

          <!-- ID Validation -->
          <div class="doc-row">
            <div class="d-flex align-items-start gap-3 flex-wrap">
              <div class="doc-icon id-validation"><i class="fas fa-id-card"></i></div>
              <div class="doc-content">
                <div class="doc-title">ID Validation</div>
                <div class="doc-meta"><i class="fas fa-calendar"></i> <span id="modalIDValidationDate">—</span></div>
                <div class="file-chip"><i class="fas fa-image"></i><span id="modalIDValidationFile">—</span></div>
              </div>
              <div class="doc-actions">
                <button type="button" class="btn btn-outline-blue" id="btnInlineIDValidation"><i class="fas fa-eye me-1"></i>Inline</button>
                <button type="button" class="btn btn-blue" id="btnViewIDValidation"><i class="fas fa-expand me-1"></i>Full View</button>
              </div>
            </div>
            <div class="preview-box d-none" id="modalViewIDValidationPreviewContainer"></div>
          </div>
        </div>

        <div class="verification-alert">
          <div class="alert-icon"><i class="fas fa-triangle-exclamation"></i></div>
          <div class="alert-content">
            <strong>Verification Required</strong>
            Review all documents carefully to confirm validity before making a decision.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <form method="post" action="clinic_verify_action.php" class="d-flex gap-2 w-100 justify-content-end">
          <input type="hidden" name="clinic_id" id="modalClinicID">
          <button type="submit" name="action" value="reject"  class="btn btn-decline"><i class="fas fa-xmark me-2"></i>Decline</button>
          <button type="submit" name="action" value="approve" class="btn btn-approve"><i class="fas fa-check me-2"></i>Approve Clinic</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ─── File Preview Modal ─── -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center">
          <div class="preview-header-icon"><i class="fas fa-file-lines"></i></div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="filePreviewTitle">Document Preview</h5>
            <div class="text-white-50 small" id="filePreviewSubtitle">Viewing full document</div>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="filePreviewBody">
          <div class="text-center text-muted">
            <i class="fas fa-file-circle-question fa-3x mb-3 opacity-50"></i>
            <p>No document loaded</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn-outline-blue-f" id="fileDownloadBtn" href="#" target="_blank" rel="noopener">
          <i class="fas fa-arrow-up-right-from-square me-1"></i> Open in New Tab
        </a>
        <button type="button" class="btn-blue-f" data-bs-dismiss="modal">
          <i class="fas fa-check me-1"></i> Done
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const root        = document.documentElement;
  const sidebar     = document.getElementById('sidebar');
  const mainContent = document.getElementById('main-content');
  const darkModeBtn = document.getElementById('darkModeBtn');
  const themeIcon   = document.getElementById('themeIcon');
  const themeLabel  = document.getElementById('themeLabel');
  let lineChart;

  // ─── Theme ───
  function applyTheme(theme) {
    root.setAttribute('data-bs-theme', theme);
    localStorage.setItem('admin_theme', theme);
    const isDark = theme === 'dark';
    themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    themeLabel.textContent = isDark ? 'Light' : 'Dark';
    initChart(theme);
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

  // ─── Chart ───
  function initChart(theme) {
    const ctx = document.getElementById('lineChart').getContext('2d');
    if (lineChart) lineChart.destroy();
    const isDark = theme === 'dark';
    const grad = ctx.createLinearGradient(0, 0, 0, 260);
    grad.addColorStop(0, 'rgba(37,99,235,.18)');
    grad.addColorStop(1, 'rgba(37,99,235,.01)');
    lineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
          label: 'Clinics',
          data: [3,7,5,12,9,18,14,22,19,28,24,32],
          borderColor: '#2563eb',
          backgroundColor: grad,
          fill: true, tension: 0.4, borderWidth: 2.5,
          pointRadius: 4, pointBackgroundColor: '#2563eb',
          pointBorderColor: '#fff', pointBorderWidth: 2, pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: isDark ? '#1e293b' : '#fff',
            titleColor: isDark ? '#e2e8f0' : '#1e293b',
            bodyColor: isDark ? '#94a3b8' : '#64748b',
            borderColor: isDark ? '#334155' : '#e2e8f0',
            borderWidth: 1, padding: 12, cornerRadius: 10,
            titleFont:{ weight:'700', size:12 },
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: isDark ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.04)', drawBorder: false },
            border: { display: false },
            ticks: { color: isDark ? '#64748b' : '#94a3b8', font:{ size:11 }, padding:8 }
          },
          x: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: isDark ? '#64748b' : '#94a3b8', font:{ size:11, weight:'600' }, padding:6 }
          }
        }
      }
    });
  }

  // ─── Table Search ───
  document.getElementById('tableSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#clinicTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // ─── File helpers ───
  function buildFilePath(f) { return `../admin_clinic/uploads/${encodeURIComponent(f)}`; }

  function renderInlinePreview(el, fileName) {
    el.innerHTML = '';
    if (!fileName) { el.innerHTML = `<div class="p-3 text-muted small">No file uploaded.</div>`; return; }
    const ext = fileName.split('.').pop().toLowerCase();
    const fp  = buildFilePath(fileName);
    if (['jpg','jpeg','png','webp','gif'].includes(ext))
      el.innerHTML = `<img src="${fp}" alt="Preview" style="max-width:100%;border-radius:8px;">`;
    else if (ext === 'pdf')
      el.innerHTML = `<iframe src="${fp}" style="width:100%;height:300px;border:none;border-radius:8px;" title="PDF"></iframe>`;
    else
      el.innerHTML = `<div class="p-3 text-muted small">Cannot preview this file type.</div>`;
  }

  function openFilePreviewModal(title, fileName) {
    const bEl   = document.getElementById('filePreviewBody');
    const dlBtn = document.getElementById('fileDownloadBtn');
    document.getElementById('filePreviewTitle').textContent   = title;
    document.getElementById('filePreviewSubtitle').textContent = fileName || 'No file';
    bEl.innerHTML = '';
    if (!fileName) {
      bEl.innerHTML = `<div class="text-muted small">No file uploaded.</div>`;
      dlBtn.classList.add('disabled'); dlBtn.href = '#';
    } else {
      const ext = fileName.split('.').pop().toLowerCase();
      const fp  = buildFilePath(fileName);
      dlBtn.classList.remove('disabled'); dlBtn.href = fp;
      if (['jpg','jpeg','png','webp','gif'].includes(ext))
        bEl.innerHTML = `<img src="${fp}" alt="Preview" style="max-width:100%;max-height:500px;border-radius:8px;">`;
      else if (ext === 'pdf')
        bEl.innerHTML = `<iframe src="${fp}" style="width:100%;height:500px;border:none;border-radius:8px;" title="PDF"></iframe>`;
      else
        bEl.innerHTML = `<div class="text-muted small">Cannot preview this file type.</div>`;
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('filePreviewModal')).show();
  }

  // ─── Review Modal ───
  document.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem('admin_theme') || 'light');

    document.getElementById('reviewModal').addEventListener('show.bs.modal', e => {
      const clinic = JSON.parse(e.relatedTarget.getAttribute('data-clinic'));
      document.getElementById('modalClinicName').textContent = clinic.clinic_name ?? '—';
      document.getElementById('modalClinicID').value         = clinic.id ?? '';
      const created = clinic.created_at ? new Date(clinic.created_at).toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'}) : '—';
      document.getElementById('modalVerificationDate').textContent = `Uploaded on ${created}`;
      document.getElementById('modalFaceAuthDate').textContent     = `Uploaded on ${created}`;
      document.getElementById('modalIDValidationDate').textContent = `Uploaded on ${created}`;
      document.getElementById('modalVerificationFile').textContent = clinic.verification_file  ?? 'N/A';
      document.getElementById('modalFaceAuthFile').textContent     = clinic.face_auth_file     ?? 'N/A';
      document.getElementById('modalIDValidationFile').textContent = clinic.id_validation_file ?? 'N/A';

      const verBox  = document.getElementById('modalViewVerificationPreviewContainer');
      const faceBox = document.getElementById('modalViewFaceAuthPreviewContainer');
      const idBox   = document.getElementById('modalViewIDValidationPreviewContainer');
      [verBox, faceBox, idBox].forEach(b => { b.classList.add('d-none'); b.innerHTML = ''; });

      document.getElementById('btnInlineVerification').onclick = () => {
        verBox.classList.toggle('d-none');
        if (!verBox.classList.contains('d-none')) renderInlinePreview(verBox, clinic.verification_file);
      };
      document.getElementById('btnInlineFaceAuth').onclick = () => {
        faceBox.classList.toggle('d-none');
        if (!faceBox.classList.contains('d-none')) renderInlinePreview(faceBox, clinic.face_auth_file);
      };
      document.getElementById('btnInlineIDValidation').onclick = () => {
        idBox.classList.toggle('d-none');
        if (!idBox.classList.contains('d-none')) renderInlinePreview(idBox, clinic.id_validation_file);
      };
      document.getElementById('btnViewVerification').onclick  = () => openFilePreviewModal('Verification Document', clinic.verification_file);
      document.getElementById('btnViewFaceAuth').onclick      = () => openFilePreviewModal('Face Authentication',   clinic.face_auth_file);
      document.getElementById('btnViewIDValidation').onclick  = () => openFilePreviewModal('ID Validation',          clinic.id_validation_file);
    });
  });
</script>
</body>
</html>