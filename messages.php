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
                <span class="current">Messages</span>
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
                <h1 class="fw-bold mb-1">Messages</h1>
                <p class="text-muted">Chat with your veterinary doctors and clinics.</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>New Message
            </button>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="search-box position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" class="form-control rounded-pill ps-5 border-light-subtle" placeholder="Search conversations...">
                        </div>
                    </div>
                    <div class="card-body p-0 conversation-list">
                        <div class="p-4 border-bottom active-conversation" style="cursor: pointer; background: #f8fafc;">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-user-md text-primary"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0 fw-bold">Dr. Sarah Wilson</h6>
                                        <small class="text-muted">10:45 AM</small>
                                    </div>
                                    <p class="text-muted mb-0 text-truncate small">Milo's prescription is ready for pickup.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-bottom" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-success-subtle rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <h6 class="text-success mb-0">CP</h6>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0 fw-bold">City Vet Center</h6>
                                        <small class="text-muted">Yesterday</small>
                                    </div>
                                    <p class="text-muted mb-0 text-truncate small">Your appointment has been confirmed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Dr. Sarah Wilson</h6>
                                <small class="text-success fw-medium"><i class="fas fa-circle me-1 small"></i>Online</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" style="min-height: 400px; display: flex; flex-direction: column; justify-content: flex-end;">
                        <div class="text-center mb-4">
                            <span class="badge bg-light text-muted rounded-pill px-3">March 15, 2024</span>
                        </div>
                        
                        <div class="d-flex mb-4">
                            <div class="bg-light p-3 rounded-4 rounded-start-0 shadow-sm" style="max-width: 70%;">
                                <p class="mb-0 small">Hello! I just reviewed Milo's test results. Everything looks great! The prescription is ready for pickup at the front desk when you're available.</p>
                                <small class="text-muted mt-1 d-block text-end">10:45 AM</small>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse mb-4">
                            <div class="bg-primary text-white p-3 rounded-4 rounded-end-0 shadow-sm" style="max-width: 70%;">
                                <p class="mb-0 small">Thank you so much, Dr. Wilson! I'll drop by this afternoon to get it.</p>
                                <small class="text-light mt-1 d-block text-end opacity-75">10:50 AM</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-4">
                        <div class="input-group">
                            <input type="text" class="form-control rounded-pill-start border-light-subtle px-4" placeholder="Type your message...">
                            <button class="btn btn-primary rounded-pill-end px-4">
                                <i class="fas fa-paper-plane"></i>
                            </button>
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

.rounded-pill-start {
    border-top-left-radius: 50px !important;
    border-bottom-left-radius: 50px !important;
}

.rounded-pill-end {
    border-top-right-radius: 50px !important;
    border-bottom-right-radius: 50px !important;
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
