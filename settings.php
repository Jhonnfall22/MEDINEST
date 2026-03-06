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
                <span class="current">Settings</span>
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
        <div class="page-header mb-4">
            <h1 class="fw-bold mb-1">Account Settings</h1>
            <p class="text-muted">Manage your personal information and application preferences.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4">Personal Information</h5>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">First Name</label>
                                <input type="text" class="form-control rounded-3" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Last Name</label>
                                <input type="text" class="form-control rounded-3" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Email Address</label>
                                <input type="email" class="form-control rounded-3" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                                <div class="form-text small text-info"><i class="fas fa-info-circle me-1"></i>Email address cannot be changed.</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Contact Number</label>
                                <input type="text" class="form-control rounded-3" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Address</label>
                                <textarea class="form-control rounded-3" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Security</h5>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Current Password</label>
                                <input type="password" class="form-control rounded-3" placeholder="••••••••">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">New Password</label>
                                <input type="password" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                                <input type="password" class="form-control rounded-3">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-outline-primary rounded-pill px-4">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4">
                    <div class="mb-3">
                        <div class="avatar-lg bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></h5>
                    <p class="text-muted small">Registered since <?php echo date('M Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                    <button class="btn btn-light border rounded-pill btn-sm px-3">Change Avatar</button>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold mb-3">Quick Actions</h6>
                    <a href="logout.php" class="btn btn-danger-subtle text-danger w-100 rounded-pill mb-2 border-0">
                        <i class="fas fa-sign-out-alt me-2"></i>Log Out
                    </a>
                    <button class="btn btn-outline-secondary w-100 rounded-pill btn-sm border-0">
                        <i class="fas fa-trash-alt me-2"></i>Delete Account
                    </button>
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

.btn-danger-subtle {
    background: rgba(239, 68, 68, 0.1);
}

.btn-danger-subtle:hover {
    background: rgba(239, 68, 68, 0.2);
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
