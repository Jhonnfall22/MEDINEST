
<?php include 'userSidebar.php'; ?> 
<title>Vet Clinic - My Appointments</title>
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

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}

.breadcrumb-nav a {
    color: var(--gray-500);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.2s;
}

.breadcrumb-nav a:hover {
    color: var(--primary);
}

.breadcrumb-nav span {
    color: var(--gray-400);
}

.breadcrumb-nav .current {
    color: var(--gray-900);
    font-weight: 500;
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

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 16px;
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

.btn-sm {
    padding: 8px 16px;
    font-size: 13px;
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

.btn-success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
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

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    color: white;
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

.stat-card.blue::before { background: var(--primary); }
.stat-card.green::before { background: var(--success); }
.stat-card.orange::before { background: var(--warning); }
.stat-card.red::before { background: var(--danger); }

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: transparent;
}

.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 16px;
}

.stat-card.blue .stat-icon { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
.stat-card.green .stat-icon { background: rgba(16, 185, 129, 0.1); color: var(--success); }
.stat-card.orange .stat-icon { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
.stat-card.red .stat-icon { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

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

/* Cards */
.card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    flex-wrap: wrap;
    gap: 16px;
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

/* Tabs */
.tabs {
    display: flex;
    gap: 8px;
    background: var(--gray-100);
    padding: 4px;
    border-radius: 10px;
}

.tab-btn {
    padding: 8px 20px;
    border: none;
    background: transparent;
    color: var(--gray-600);
    font-weight: 500;
    font-size: 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tab-btn:hover {
    color: var(--gray-900);
}

.tab-btn.active {
    background: white;
    color: var(--primary);
    box-shadow: var(--shadow-sm);
}

/* Filter Bar */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.search-input {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-input input {
    width: 100%;
    padding: 10px 16px 10px 42px;
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    background: var(--gray-50);
    transition: all 0.2s ease;
}

.search-input input:focus {
    outline: none;
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-input i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}

.filter-select {
    padding: 10px 16px;
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    background: white;
    color: var(--gray-700);
    cursor: pointer;
    min-width: 150px;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary);
}

/* Appointment Cards */
.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.appointment-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 14px;
    padding: 20px;
    transition: all 0.3s ease;
    position: relative;
}

.appointment-card:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.appointment-card .status-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.appointment-card .status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.appointment-card .status-badge.confirmed {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.appointment-card .status-badge.completed {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.appointment-card .status-badge.cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.appointment-card .pet-info {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}

.appointment-card .pet-avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.appointment-card .pet-details h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.appointment-card .pet-details p {
    font-size: 13px;
    color: var(--gray-500);
    margin: 0;
}

.appointment-card .appointment-info {
    background: var(--gray-50);
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 16px;
}

.appointment-card .info-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-200);
}

.appointment-card .info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.appointment-card .info-row:first-child {
    padding-top: 0;
}

.appointment-card .info-row i {
    width: 18px;
    color: var(--primary);
    font-size: 14px;
}

.appointment-card .info-row span {
    color: var(--gray-700);
    font-size: 13px;
}

.appointment-card .info-row strong {
    color: var(--gray-900);
    font-weight: 600;
}

.appointment-card .card-actions {
    display: flex;
    gap: 10px;
}

.appointment-card .card-actions .btn-outline {
    flex: 1;
    justify-content: center;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: var(--gray-50);
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--gray-200);
}

.data-table td {
    padding: 16px;
    border-bottom: 1px solid var(--gray-100);
    font-size: 14px;
    color: var(--gray-700);
}

.data-table tbody tr {
    transition: background 0.2s ease;
}

.data-table tbody tr:hover {
    background: var(--gray-50);
}

.data-table .pet-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.data-table .pet-cell .pet-img {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.data-table .pet-cell .pet-name {
    font-weight: 600;
    color: var(--gray-900);
}

.data-table .pet-cell .pet-type {
    font-size: 12px;
    color: var(--gray-500);
}

.data-table .status-badge {
    display: inline-flex;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.data-table .status-badge.completed {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.data-table .status-badge.cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.data-table .status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.data-table .status-badge.confirmed {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.data-table .action-btns {
    display: flex;
    gap: 8px;
}

.data-table .action-btn {
    width: 34px;
    height: 34px;
    border: 1px solid var(--gray-200);
    background: white;
    border-radius: 8px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.data-table .action-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}

.data-table .action-btn.delete:hover {
    border-color: var(--danger);
    color: var(--danger);
    background: rgba(239, 68, 68, 0.05);
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-top: 1px solid var(--gray-100);
}

.pagination-info {
    font-size: 14px;
    color: var(--gray-500);
}

.pagination {
    display: flex;
    gap: 6px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination .page-link {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--gray-200);
    background: white;
    border-radius: 8px;
    color: var(--gray-600);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: var(--gray-300);
    cursor: not-allowed;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 24px;
}

.empty-state .empty-icon {
    width: 100px;
    height: 100px;
    background: var(--gray-100);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: var(--gray-400);
    font-size: 40px;
}

.empty-state h4 {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.empty-state p {
    color: var(--gray-500);
    margin-bottom: 24px;
    font-size: 15px;
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

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    background: white;
    color: var(--gray-800);
    cursor: pointer;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Cancel Modal */
.cancel-modal .modal-body {
    text-align: center;
    padding: 40px 24px;
}

.cancel-modal .cancel-icon {
    width: 80px;
    height: 80px;
    background: rgba(239, 68, 68, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: var(--danger);
    font-size: 32px;
}

.cancel-modal h4 {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.cancel-modal p {
    color: var(--gray-500);
    font-size: 15px;
}

/* View Appointment Details */
.appointment-details .detail-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--gray-100);
    margin-bottom: 20px;
}

.appointment-details .detail-header .pet-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
}

.appointment-details .detail-header h3 {
    font-size: 22px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.appointment-details .detail-header p {
    color: var(--gray-500);
    margin: 0;
}

.appointment-details .detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.appointment-details .detail-item {
    background: var(--gray-50);
    border-radius: 12px;
    padding: 16px;
}

.appointment-details .detail-item label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.appointment-details .detail-item p {
    margin: 0;
    font-weight: 500;
    color: var(--gray-900);
    font-size: 14px;
}

.appointment-details .notes-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-100);
}

.appointment-details .notes-section h5 {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 10px;
}

.appointment-details .notes-section p {
    background: var(--gray-50);
    padding: 14px;
    border-radius: 10px;
    color: var(--gray-600);
    font-size: 14px;
    line-height: 1.6;
}

/* Logout Modal */
.logout-modal .modal-body {
    text-align: center;
    padding: 48px 32px;
}

.logout-modal .logout-icon {
    width: 90px;
    height: 90px;
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

/* Responsive */
@media (max-width: 1400px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 1200px) {
    .appointments-grid {
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
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .page-content {
        padding: 20px;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .top-header {
        padding: 16px 20px;
    }
    
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-bar .search-input {
        width: 100%;
    }
    
    .tabs {
        width: 100%;
        overflow-x: auto;
    }
    
    .appointment-details .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="overlay" id="overlay"></div>
<!-- Main Content -->
<main class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="breadcrumb-nav">
                <a href="dashboard.php">Dashboard</a>
                <span>/</span>
                <span class="current">Appointments</span>
            </nav>
        </div>
        
        <div class="header-actions">
            <button class="header-btn" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notification-dot"></span>
            </button>
            <button class="header-btn" title="Settings">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </header>
    
    <!-- Page Content -->
    <div class="page-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h1>My Appointments</h1>
                <p>Manage and track all your pet appointments</p>
            </div>
            <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">
                <i class="fas fa-plus"></i>
                Book Appointment
            </button>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-label">Total Appointments</div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Pending</div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value">8</div>
                <div class="stat-label">Completed</div>
            </div>
            
            <div class="stat-card red">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value">1</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
        
        <!-- Current/Upcoming Appointments -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-day"></i> Upcoming Appointments</h3>
                <div class="tabs">
                    <button class="tab-btn active" data-tab="all">All</button>
                    <button class="tab-btn" data-tab="pending">Pending</button>
                    <button class="tab-btn" data-tab="confirmed">Confirmed</button>
                </div>
            </div>
            <div class="card-body">
                <div class="appointments-grid">
                    <!-- Appointment Card 1 - Pending -->
                    <div class="appointment-card">
                        <span class="status-badge pending">Pending</span>
                        <div class="pet-info">
                            <div class="pet-avatar">
                                <i class="fas fa-dog"></i>
                            </div>
                            <div class="pet-details">
                                <h4>Max</h4>
                                <p>Golden Retriever • Male</p>
                            </div>
                        </div>
                        <div class="appointment-info">
                            <div class="info-row">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Date:</strong> December 20, 2024</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-clock"></i>
                                <span><strong>Time:</strong> 10:00 AM</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-stethoscope"></i>
                                <span><strong>Service:</strong> General Checkup</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-user-md"></i>
                                <span><strong>Vet:</strong> Dr. Sarah Johnson</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                    
                    <!-- Appointment Card 2 - Confirmed -->
                    <div class="appointment-card">
                        <span class="status-badge confirmed">Confirmed</span>
                        <div class="pet-info">
                            <div class="pet-avatar">
                                <i class="fas fa-cat"></i>
                            </div>
                            <div class="pet-details">
                                <h4>Bella</h4>
                                <p>Persian Cat • Female</p>
                            </div>
                        </div>
                        <div class="appointment-info">
                            <div class="info-row">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Date:</strong> December 22, 2024</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-clock"></i>
                                <span><strong>Time:</strong> 2:30 PM</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-stethoscope"></i>
                                <span><strong>Service:</strong> Vaccination</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-user-md"></i>
                                <span><strong>Vet:</strong> Dr. Michael Chen</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                <i class="fas fa-edit"></i> Reschedule
                            </button>
                        </div>
                    </div>
                    
                    <!-- Appointment Card 3 - Pending -->
                    <div class="appointment-card">
                        <span class="status-badge pending">Pending</span>
                        <div class="pet-info">
                            <div class="pet-avatar">
                                <i class="fas fa-dog"></i>
                            </div>
                            <div class="pet-details">
                                <h4>Charlie</h4>
                                <p>Beagle • Male</p>
                            </div>
                        </div>
                        <div class="appointment-info">
                            <div class="info-row">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Date:</strong> December 28, 2024</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-clock"></i>
                                <span><strong>Time:</strong> 11:00 AM</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-stethoscope"></i>
                                <span><strong>Service:</strong> Dental Cleaning</span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-user-md"></i>
                                <span><strong>Vet:</strong> Dr. Emily Brown</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Appointment History -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Appointment History</h3>
                <div class="filter-bar">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search appointments...">
                    </div>
                    <select class="filter-select">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select class="filter-select">
                        <option value="">All Pets</option>
                        <option value="max">Max</option>
                        <option value="bella">Bella</option>
                        <option value="charlie">Charlie</option>
                    </select>
                </div>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Veterinarian</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="pet-cell">
                                    <div class="pet-img"><i class="fas fa-dog"></i></div>
                                    <div>
                                        <div class="pet-name">Max</div>
                                        <div class="pet-type">Golden Retriever</div>
                                    </div>
                                </div>
                            </td>
                            <td>Annual Checkup</td>
                            <td>
                                <div>Dec 10, 2024</div>
                                <small class="text-muted">9:00 AM</small>
                            </td>
                            <td>Dr. Sarah Johnson</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn" title="View Details" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" title="Download Report">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="pet-cell">
                                    <div class="pet-img"><i class="fas fa-cat"></i></div>
                                    <div>
                                        <div class="pet-name">Bella</div>
                                        <div class="pet-type">Persian Cat</div>
                                    </div>
                                </div>
                            </td>
                            <td>Vaccination</td>
                            <td>
                                <div>Dec 5, 2024</div>
                                <small class="text-muted">2:00 PM</small>
                            </td>
                            <td>Dr. Michael Chen</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn" title="View Details" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" title="Download Report">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="pet-cell">
                                    <div class="pet-img"><i class="fas fa-dog"></i></div>
                                    <div>
                                        <div class="pet-name">Charlie</div>
                                        <div class="pet-type">Beagle</div>
                                    </div>
                                </div>
                            </td>
                            <td>Grooming</td>
                            <td>
                                <div>Nov 28, 2024</div>
                                <small class="text-muted">11:30 AM</small>
                            </td>
                            <td>Dr. Emily Brown</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn" title="View Details" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" title="Download Report">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="pet-cell">
                                    <div class="pet-img"><i class="fas fa-dog"></i></div>
                                    <div>
                                        <div class="pet-name">Max</div>
                                        <div class="pet-type">Golden Retriever</div>
                                    </div>
                                </div>
                            </td>
                            <td>Dental Cleaning</td>
                            <td>
                                <div>Nov 15, 2024</div>
                                <small class="text-muted">3:00 PM</small>
                            </td>
                            <td>Dr. Sarah Johnson</td>
                            <td><span class="status-badge cancelled">Cancelled</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn" title="View Details" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" title="Rebook">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="pet-cell">
                                    <div class="pet-img"><i class="fas fa-cat"></i></div>
                                    <div>
                                        <div class="pet-name">Bella</div>
                                        <div class="pet-type">Persian Cat</div>
                                    </div>
                                </div>
                            </td>
                            <td>General Checkup</td>
                            <td>
                                <div>Nov 8, 2024</div>
                                <small class="text-muted">10:00 AM</small>
                            </td>
                            <td>Dr. Michael Chen</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn" title="View Details" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" title="Download Report">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Showing 1 to 5 of 12 entries
                </div>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>

<!-- Book Appointment Modal -->
<div class="modal fade" id="bookAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus"></i> Book New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="book_appointment.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Select Pet <span class="text-danger">*</span></label>
                                <select class="form-select" name="pet_id" required>
                                    <option value="">Choose a pet...</option>
                                    <option value="1">Max - Golden Retriever</option>
                                    <option value="2">Bella - Persian Cat</option>
                                    <option value="3">Charlie - Beagle</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Service Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="service_type" required>
                                    <option value="">Choose a service...</option>
                                    <option value="checkup">General Checkup</option>
                                    <option value="vaccination">Vaccination</option>
                                    <option value="grooming">Grooming</option>
                                    <option value="dental">Dental Cleaning</option>
                                    <option value="surgery">Surgery</option>
                                    <option value="emergency">Emergency Care</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="appointment_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Preferred Time <span class="text-danger">*</span></label>
                                <select class="form-select" name="appointment_time" required>
                                    <option value="">Choose a time slot...</option>
                                    <option value="09:00">9:00 AM</option>
                                    <option value="09:30">9:30 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="10:30">10:30 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="11:30">11:30 AM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="14:30">2:30 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="15:30">3:30 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="16:30">4:30 PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Preferred Veterinarian</label>
                        <select class="form-select" name="veterinarian">
                            <option value="">No preference</option>
                            <option value="1">Dr. Sarah Johnson</option>
                            <option value="2">Dr. Michael Chen</option>
                            <option value="3">Dr. Emily Brown</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-label">Notes / Symptoms</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Describe any symptoms or concerns about your pet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-calendar-check"></i>
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-alt"></i> Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="appointment-details">
                    <div class="detail-header">
                        <div class="pet-avatar">
                            <i class="fas fa-dog"></i>
                        </div>
                        <div>
                            <h3>Max</h3>
                            <p>Golden Retriever • Male • 3 years old</p>
                        </div>
                    </div>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Appointment ID</label>
                            <p>#APT-2024-001</p>
                        </div>
                        <div class="detail-item">
                            <label>Status</label>
                            <p><span class="status-badge pending">Pending</span></p>
                        </div>
                        <div class="detail-item">
                            <label>Date</label>
                            <p>December 20, 2024</p>
                        </div>
                        <div class="detail-item">
                            <label>Time</label>
                            <p>10:00 AM</p>
                        </div>
                        <div class="detail-item">
                            <label>Service</label>
                            <p>General Checkup</p>
                        </div>
                        <div class="detail-item">
                            <label>Veterinarian</label>
                            <p>Dr. Sarah Johnson</p>
                        </div>
                    </div>
                    
                    <div class="notes-section">
                        <h5><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                        <p>Annual checkup and vaccination update. Pet has been scratching ears frequently.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-outline" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                    <i class="fas fa-edit"></i> Reschedule
                </button>
                <button type="button" class="btn-danger" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-alt"></i> Reschedule Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="reschedule_appointment.php" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info" style="background: rgba(37, 99, 235, 0.1); border: none; border-radius: 10px; color: var(--primary);">
                        <i class="fas fa-info-circle me-2"></i>
                        You are rescheduling appointment <strong>#APT-2024-001</strong> for <strong>Max</strong>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="new_date" required>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-label">New Time <span class="text-danger">*</span></label>
                        <select class="form-select" name="new_time" required>
                            <option value="">Choose a time slot...</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="09:30">9:30 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="10:30">10:30 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="11:30">11:30 AM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="14:30">2:30 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="15:30">3:30 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="16:30">4:30 PM</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i>
                        Confirm Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div class="modal fade cancel-modal" id="cancelAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="cancel-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h4>Cancel Appointment?</h4>
                <p>Are you sure you want to cancel this appointment? This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                    No, Keep It
                </button>
                <a href="cancel_appointment.php" class="btn-danger">
                    <i class="fas fa-times"></i>
                    Yes, Cancel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Logout Modal -->
<div class="modal fade logout-modal" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="logout-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h4>Logout?</h4>
                <p>Are you sure you want to logout from your account?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <a href="logout.php" class="btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mobile sidebar toggle
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const mobileToggle = document.getElementById('mobileToggle');

mobileToggle.addEventListener('click', function() {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
});

overlay.addEventListener('click', function() {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

// Close sidebar when clicking a link on mobile
document.querySelectorAll('.sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
});

// Tab functionality
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const tab = this.dataset.tab;
        // Filter appointments based on tab
        filterAppointments(tab);
    });
});

function filterAppointments(status) {
    const cards = document.querySelectorAll('.appointment-card');
    cards.forEach(card => {
        const badge = card.querySelector('.status-badge');
        if (status === 'all') {
            card.style.display = 'block';
        } else if (badge.classList.contains(status)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Set minimum date for appointment booking
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        input.setAttribute('min', today);
    });
});

// Search functionality
document.querySelector('.search-input input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filter functionality
document.querySelectorAll('.filter-select').forEach(select => {
    select.addEventListener('change', function() {
        // Implement filter logic based on selection
        console.log('Filter changed:', this.value);
    });
});
</script>
</body>
</html>