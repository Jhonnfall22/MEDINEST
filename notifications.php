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
                <span class="current">Notifications</span>
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
                <h1 class="fw-bold mb-1">Notifications</h1>
                <p class="text-muted">Stay updated with your pet's health and appointments.</p>
            </div>
            <button class="btn btn-light rounded-pill px-4 border">
                Mark all as read
            </button>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <!-- Notification Item -->
                        <div class="p-4 border-bottom unread-notification" style="background: rgba(37, 99, 235, 0.03);">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-calendar-check mt-0"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold">Appointment Confirmed</h6>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">Your appointment with Dr. Sarah Wilson at Friendly Paws Clinic for <strong>Milo</strong> has been confirmed for Mar 20th.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Item -->
                        <div class="p-4 border-bottom">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-exclamation-triangle mt-0"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold">Vaccination Reminder</h6>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small"><strong>Luna</strong> is due for her annual Rabies vaccination next week. Book an appointment today!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Item -->
                        <div class="p-4 border-bottom">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-check-circle mt-0"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold">Review Success</h6>
                                        <small class="text-muted">3 days ago</small>
                                    </div>
                                    <p class="text-muted mb-0 small">Your review for City Vet Center has been published. Thank you for sharing your experience!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-4 text-center">
                        <button class="btn btn-link text-primary text-decoration-none fw-medium p-0">View older notifications</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold mb-3">Notification Settings</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="small text-muted mb-0">Email Notifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" checked>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="small text-muted mb-0">Push Notifications</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" checked>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="small text-muted mb-0">Appointment Alerts</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" checked>
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

.unread-notification {
    position: relative;
}

.unread-notification::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary);
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
