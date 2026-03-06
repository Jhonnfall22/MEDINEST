<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config/db.php";

// Fetch user data for sidebar
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

include 'users/userSidebar.php';
?>
<div class="overlay" id="overlay"></div>

<main class="main-content">
    <header class="top-header">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="breadcrumb-nav">
                <a href="dashboard.php">Dashboard</a>
                <span>/</span>
                <span class="current">Medical Records</span>
            </nav>
        </div>
        
        <div class="header-actions">
            <button class="header-btn" title="Notifications">
                <i class="fas fa-bell"></i>
            </button>
            <button class="header-btn" title="Settings">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </header>

    <div class="page-content">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Medical Records</h1>
                <p class="text-muted">History of treatments and diagnostics for your pets.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-file-export me-2"></i>Export Records
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">Pet Name</th>
                                    <th class="py-3 border-0">Condition</th>
                                    <th class="py-3 border-0">Treatment</th>
                                    <th class="py-3 border-0">Clinic</th>
                                    <th class="py-3 border-0">Date</th>
                                    <th class="px-4 py-3 border-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="align-middle">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                                <i class="fas fa-dog"></i>
                                            </div>
                                            <span class="fw-medium">Milo</span>
                                        </div>
                                    </td>
                                    <td class="py-3">Routine Checkup</td>
                                    <td class="py-3">Vaccination (Rabies)</td>
                                    <td class="py-3 text-muted">Friendly Paws Clinic</td>
                                    <td class="py-3 text-muted">Mar 15, 2024</td>
                                    <td class="px-4 py-3">
                                        <button class="btn btn-sm btn-light border">View Details</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                                <i class="fas fa-cat"></i>
                                            </div>
                                            <span class="fw-medium">Luna</span>
                                        </div>
                                    </td>
                                    <td class="py-3">Skin Allergy</td>
                                    <td class="py-3">Topical Ointment</td>
                                    <td class="py-3 text-muted">City Vet Center</td>
                                    <td class="py-3 text-muted">Feb 28, 2024</td>
                                    <td class="px-4 py-3">
                                        <button class="btn btn-sm btn-light border">View Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="p-4 text-center border-top">
                            <p class="text-muted mb-0 small">Showing 2 of 2 records</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.main-content {
    margin-left: 280px;
    padding: 32px;
    background: #f8fafc;
    min-height: 100vh;
}

@media (max-width: 992px) {
    .main-content { margin-left: 0; padding: 20px; }
}

.table thead th {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    color: #64748b;
}

.table tbody td {
    border-bottom: 1px solid #f1f5f9;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mobile sidebar toggle
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const mobileToggle = document.getElementById('mobileToggle');

if (mobileToggle) {
    mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });
}

if (overlay) {
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
}

// Close sidebar when clicking a link on mobile
document.querySelectorAll('.sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
});
</script>
</body>
</html>
