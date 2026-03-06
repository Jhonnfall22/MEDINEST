<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/MEDINEST/';
$document_root = $_SERVER['DOCUMENT_ROOT'];

// Load db.php correctly depending on the included file's location
if (file_exists($document_root . $base_url . "config/db.php")) {
    require_once $document_root . $base_url . "config/db.php"; 
}

// Redirect to login if not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: " . $base_url . "index.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vet Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-header-top d-flex align-items-center justify-content-between mb-4">
            <a href="<?php echo $base_url; ?>dashboard.php" class="btn-sidebar-back" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <a href="<?php echo $base_url; ?>profile.php" class="sidebar-logo">


            <div class="logo-icon">
                <i class="fas fa-paw"></i>
            </div>
            <span class="logo-text">Vet<span>Clinic</span></span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">Main Menu</span>
            <ul class="nav-menu">
                <li>
                    <a href="<?php echo $base_url; ?>profile.php" class="nav-link <?php echo ($current_page == 'profile.php' || $current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i>
                        Profile
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>users/mypets.php" class="nav-link <?php echo ($current_page == 'mypets.php' || $current_page == 'pets.php' || $current_page == 'pet.php') ? 'active' : ''; ?>">
                        <i class="fas fa-dog"></i>
                        My Pets
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>users/search_clinic.php" class="nav-link <?php echo ($current_page == 'search_clinic.php') ? 'active' : ''; ?>">
                        <i class="fas fa-hospital"></i>
                        Find Clinic
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>users/appointments.php" class="nav-link <?php echo ($current_page == 'appointments.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i>
                        Appointments
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>medical-records.php" class="nav-link <?php echo ($current_page == 'medical-records.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-medical"></i>
                        Medical Records
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="nav-section">
            <span class="nav-section-title">Communication</span>
            <ul class="nav-menu">
                <li>
                    <a href="<?php echo $base_url; ?>messages.php" class="nav-link <?php echo ($current_page == 'messages.php') ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i>
                        Messages
                        <span class="badge">3</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>notifications.php" class="nav-link <?php echo ($current_page == 'notifications.php') ? 'active' : ''; ?>">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="nav-section">
            <span class="nav-section-title">Account</span>
            <ul class="nav-menu">
                <li>
                    <a href="<?php echo $base_url; ?>settings.php" class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#logoutModalSidebar">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </div>
    </div>
</aside>

<style>
    
:root {
    --primary: #2563eb;
    --primary-light: #3b82f6;
    --primary-lighter: #60a5fa;
    --primary-dark: #1d4ed8;
    --primary-darker: #1e40af;
    --secondary: #f1f5f9;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
    --dark: #0f172a;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    --sidebar-width: 280px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: var(--gray-50);
    color: var(--gray-700);
    font-size: 14px;
    line-height: 1.6;
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
}

::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--gray-400);
}

/* Sidebar */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: var(--sidebar-width);
    background: white;
    border-right: 1px solid var(--gray-200);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.sidebar-header {
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.logo-icon {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.logo-text {
    font-size: 22px;
    font-weight: 700;
    color: var(--gray-900);
}

.logo-text span {
    color: var(--primary);
}

.sidebar-nav {
    flex: 1;
    padding: 20px 16px;
    overflow-y: auto;
}

.nav-section {
    margin-bottom: 28px;
}

.nav-section-title {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--gray-400);
    padding: 0 12px;
    margin-bottom: 10px;
}

.nav-menu {
    list-style: none;
}

.nav-menu li {
    margin-bottom: 4px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 12px;
    color: var(--gray-600);
    text-decoration: none;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.nav-link:hover {
    background: var(--gray-100);
    color: var(--gray-900);
}

.nav-link.active {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}

.nav-link i {
    width: 20px;
    font-size: 15px;
    text-align: center;
}

.nav-link .badge {
    margin-left: auto;
    background: var(--danger);
    color: white;
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 50px;
    font-weight: 600;
}

.nav-link.active .badge {
    background: rgba(255,255,255,0.25);
}

/* Sidebar Footer */
.sidebar-footer {
    padding: 16px;
    border-top: 1px solid var(--gray-100);
}

.user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--gray-50);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.user-card:hover {
    background: var(--gray-100);
    border-color: var(--gray-200);
}

.user-avatar {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: 12px;
    color: var(--gray-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-card .dropdown-icon {
    color: var(--gray-400);
    font-size: 10px;
}

/* Main Content */
.main-content {
    margin-left: var(--sidebar-width);
    min-height: 100vh;
}

/* Top Header */
.top-header {
    background: white;
    border-bottom: 1px solid var(--gray-200);
    padding: 16px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}

.mobile-toggle {
    display: none;
    width: 40px;
    height: 40px;
    border: none;
    background: var(--gray-100);
    border-radius: 10px;
    color: var(--gray-600);
    cursor: pointer;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
}

.search-box {
    position: relative;
    width: 320px;
}

.search-box input {
    width: 100%;
    padding: 10px 16px 10px 44px;
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    background: var(--gray-50);
    transition: all 0.2s ease;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-box input::placeholder {
    color: var(--gray-400);
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.header-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--gray-100);
    border-radius: 10px;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}

.header-btn:hover {
    background: var(--primary);
    color: white;
}

.header-btn .notification-dot {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 8px;
    height: 8px;
    background: var(--danger);
    border-radius: 50%;
    border: 2px solid white;
}

/* Page Content */
.page-content {
    padding: 32px;
}

.page-title {
    margin-bottom: 32px;
}

.page-title h1 {
    font-size: 26px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 6px;
}

.page-title p {
    color: var(--gray-500);
    font-size: 15px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    border-radius: 16px 0 0 16px;
}

.stat-card.blue::before {
    background: var(--primary);
}

.stat-card.green::before {
    background: var(--success);
}

.stat-card.orange::before {
    background: var(--warning);
}

.stat-card.cyan::before {
    background: var(--info);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: transparent;
}

.stat-card .stat-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-card.blue .stat-icon {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.stat-card.green .stat-icon {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stat-card.orange .stat-icon {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.stat-card.cyan .stat-icon {
    background: rgba(6, 182, 212, 0.1);
    color: var(--info);
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
    line-height: 1;
}

.stat-card .stat-label {
    color: var(--gray-500);
    font-size: 14px;
    font-weight: 500;
}

.stat-card .stat-trend {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 12px;
    padding: 4px 10px;
    border-radius: 6px;
}

.stat-card .stat-trend.up {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stat-card .stat-trend.down {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
}

/* Cards */
.card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
}

.card-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h3 i {
    color: var(--primary);
}

.card-body {
    padding: 24px;
}

/* Profile Card */
.profile-card .profile-banner {
    height: 130px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-lighter) 50%, #0ea5e9 100%);
    position: relative;
}

.profile-card .profile-banner::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60%;
    background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
}

.profile-card .banner-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.profile-card .profile-avatar-wrapper {
    position: absolute;
    bottom: -50px;
    left: 24px;
    z-index: 10;
}

.profile-card .profile-avatar {
    width: 100px;
    height: 100px;
    background: white;
    border-radius: 20px;
    padding: 4px;
    box-shadow: var(--shadow-lg);
}

.profile-card .profile-avatar .avatar-inner {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 36px;
    font-weight: 700;
}

.profile-card .profile-content {
    padding: 60px 24px 24px;
}

.profile-card .profile-name {
    font-size: 22px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.profile-card .profile-role {
    color: var(--gray-500);
    font-size: 14px;
    margin-bottom: 16px;
}

.profile-card .profile-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.profile-card .profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.profile-card .profile-badge.verified {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.profile-card .profile-badge.member {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.profile-info-list {
    margin-top: 24px;
}

.profile-info-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid var(--gray-100);
}

.profile-info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.profile-info-item .info-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 16px;
}

.profile-info-item .info-content {
    flex: 1;
}

.profile-info-item .info-label {
    font-size: 11px;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
    font-weight: 500;
}

.profile-info-item .info-value {
    font-weight: 500;
    color: var(--gray-800);
    font-size: 14px;
}

.profile-actions {
    display: flex;
    gap: 12px;
    padding: 20px 24px;
    border-top: 1px solid var(--gray-100);
    background: var(--gray-50);
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    color: white;
}

.btn-secondary {
    background: white;
    border: 1px solid var(--gray-200);
    color: var(--gray-700);
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
}

.btn-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    color: var(--gray-900);
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--gray-200);
    color: var(--gray-600);
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
}

.btn-outline:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 20px 16px;
    background: var(--gray-50);
    border: 1px solid var(--gray-100);
    border-radius: 14px;
    text-decoration: none;
    color: var(--gray-700);
    transition: all 0.2s ease;
}

.quick-action-btn:hover {
    background: white;
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.quick-action-btn .action-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--primary);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-100);
    transition: all 0.2s ease;
}

.quick-action-btn:hover .action-icon {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.quick-action-btn span {
    font-weight: 600;
    font-size: 13px;
}

/* Activity List */
.activity-list {
    list-style: none;
}

.activity-item {
    display: flex;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--gray-100);
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.activity-item .activity-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
}

.activity-item .activity-icon.green {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.activity-item .activity-icon.blue {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.activity-item .activity-icon.orange {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.activity-item .activity-content {
    flex: 1;
    min-width: 0;
}

.activity-item .activity-content h5 {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.activity-item .activity-content p {
    font-size: 12px;
    color: var(--gray-500);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.activity-item .activity-time {
    font-size: 11px;
    color: var(--gray-400);
    white-space: nowrap;
    font-weight: 500;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}

.modal-header {
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
    background: white;
}

.modal-header .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-header .modal-title i {
    color: var(--primary);
}

.modal-header .btn-close {
    opacity: 0.5;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid var(--gray-100);
    background: var(--gray-50);
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: white;
    color: var(--gray-800);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control::placeholder {
    color: var(--gray-400);
}

.input-group {
    position: relative;
}

.input-group .form-control {
    padding-right: 48px;
}

.input-group .input-icon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    cursor: pointer;
    transition: color 0.2s ease;
}

.input-group .input-icon:hover {
    color: var(--primary);
}

/* Logout Modal */
.logout-modal .modal-body {
    text-align: center;
    padding: 68px 52px;
}

.logout-modal .logout-icon {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.2) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: var(--danger);
    font-size: 36px;
}

.logout-modal h4 {
    font-size: 22px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.logout-modal p {
    color: var(--gray-500);
    font-size: 15px;
    line-height: 1.6;
}

.btn-danger {
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    color: white;
}

/* Right Column */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Responsive */
@media (max-width: 1400px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.active {
        transform: translateX(0);
        box-shadow: var(--shadow-xl);
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .mobile-toggle {
        display: flex;
    }
    
    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }
    
    .overlay.active {
        display: block;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .page-content {
        padding: 20px;
    }
    
    .top-header {
        padding: 16px 20px;
    }
    
    .search-box {
        display: none;
    }
    
    .profile-actions {
        flex-direction: column;
    }
    
    .profile-actions .btn-primary,
    .profile-actions .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Logout Modal Sidebar -->
<div class="modal fade logout-modal" id="logoutModalSidebar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="logout-icon text-center text-danger mb-3" style="font-size: 3rem;">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h4 class="text-center">Logout?</h4>
                <p class="text-center text-muted">Are you sure you want to logout from your account?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <a href="<?php echo $base_url; ?>logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>