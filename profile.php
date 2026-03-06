
<?php include 'users/userSidebar.php'; ?> 
<div class="overlay" id="overlay"></div>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Header -->
    <header class="top-header">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for anything...">
            </div>
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
        <div class="page-title">
            <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h1>
            <p>Here's what's happening with your pets today.</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Total Pets</div>
                <span class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 1 new this month
                </span>
            </div>
            
            <div class="stat-card green">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value">2</div>
                <div class="stat-label">Upcoming Appointments</div>
                <span class="stat-trend up">
                    <i class="fas fa-clock"></i> Next: Tomorrow
                </span>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                </div>
                <div class="stat-value">5</div>
                <div class="stat-label">Vaccinations Due</div>
                <span class="stat-trend down">
                    <i class="fas fa-exclamation"></i> 2 overdue
                </span>
            </div>
            
            <div class="stat-card cyan">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                </div>
                <div class="stat-value">12</div>
                <div class="stat-label">Medical Records</div>
                <span class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 3 new records
                </span>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Profile Card -->
            <div class="card profile-card">
                <div class="profile-banner">
                    <div class="banner-pattern"></div>
                </div>
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        <div class="avatar-inner">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
                
                <div class="profile-content">
                    <h2 class="profile-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']); ?></h2>
                    <p class="profile-role">Pet Owner</p>
                    <div class="profile-badges">
                        <span class="profile-badge verified">
                            <i class="fas fa-check-circle"></i>
                            Verified
                        </span>
                        <span class="profile-badge member">
                            <i class="fas fa-star"></i>
                            Premium Member
                        </span>
                    </div>
                    
                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['contact']); ?></div>
                            </div>
                        </div>
                        
                        <div class="profile-info-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['address']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                    <button class="btn-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-lock"></i>
                        Change Password
                    </button>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="right-column">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="appointments.php" class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <span>Book Appointment</span>
                            </a>
                            <a href="pet.php" class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span>Add New Pet</span>
                            </a>
                            <a href="medical-records.php" class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-file-medical-alt"></i>
                                </div>
                                <span>View Records</span>
                            </a>
                            <a href="messages.php" class="quick-action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-comment-dots"></i>
                                </div>
                                <span>Contact Vet</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Recent Activity</h3>
                        <a href="#" class="btn-outline">View All</a>
                    </div>
                    <div class="card-body">
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="activity-icon green">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="activity-content">
                                    <h5>Appointment Completed</h5>
                                    <p>Max's vaccination was successful</p>
                                </div>
                                <span class="activity-time">2d ago</span>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon blue">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="activity-content">
                                    <h5>New Appointment</h5>
                                    <p>Scheduled checkup for Bella</p>
                                </div>
                                <span class="activity-time">3d ago</span>
                            </li>
                            <li class="activity-item">
                                <div class="activity-icon orange">
                                    <i class="fas fa-paw"></i>
                                </div>
                                <div class="activity-content">
                                    <h5>Pet Added</h5>
                                    <p>Added Charlie to your pets</p>
                                </div>
                                <span class="activity-time">1w ago</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="update_profile.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-lock"></i> Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="change_password.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="current_password" id="currentPassword" placeholder="Enter current password" required>
                            <span class="input-icon" onclick="togglePassword('currentPassword')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="new_password" id="newPassword" placeholder="Enter new password" required>
                            <span class="input-icon" onclick="togglePassword('newPassword')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="confirm_password" id="confirmPassword" placeholder="Confirm new password" required>
                            <span class="input-icon" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-key"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
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
// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

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
</script>
</body>
</html>