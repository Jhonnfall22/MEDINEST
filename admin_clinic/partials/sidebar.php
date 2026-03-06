<?php
// Get the current page filename to handle active states if needed
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!--! ================================================================ !-->
<!--! [Start] Navigation Menu !-->
<!--! ================================================================ !-->
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="index.php" class="b-brand">
                <!-- ========   clinic admin logo   ============ -->
                <img src="assets/images/logo-abbr.png" alt="" class="logo logo-lg" style="max-height: 40px;" />
                <img src="assets/images/logo-abbr.png" alt="" class="logo logo-sm" />
            </a>
        </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Clinic Admin</label>
                </li>

                <!-- Dashboard -->
                <li class="nxl-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <a href="index.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <!-- Receptionist -->
                <li class="nxl-item <?php echo ($current_page == 'receptionist.php') ? 'active' : ''; ?>">
                    <a href="receptionist.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-calendar"></i></span>
                        <span class="nxl-mtext">Receptionist</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Management</label>
                </li>

                <!-- Users -->
                <li class="nxl-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                    <a href="users.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user-check"></i></span>
                        <span class="nxl-mtext">Users</span>
                    </a>
                </li>

                <!-- Patients -->
                <li class="nxl-item <?php echo ($current_page == 'client_list.php') ? 'active' : ''; ?>">
                    <a href="client_list.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Patient List</span>
                    </a>
                </li>

                <!-- Inventory -->
                <li class="nxl-item <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
                    <a href="inventory.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-package"></i></span>
                        <span class="nxl-mtext">Inventory</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Tools & Reports</label>
                </li>

                <!-- Messages -->
                <li class="nxl-item <?php echo ($current_page == 'message.php') ? 'active' : ''; ?>">
                    <a href="message.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-mail"></i></span>
                        <span class="nxl-mtext">Messages</span>
                    </a>
                </li>

                <!-- Reports -->
                <li class="nxl-item <?php echo ($current_page == 'report.php') ? 'active' : ''; ?>">
                    <a href="report.php" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-file-text"></i></span>
                        <span class="nxl-mtext">Reports</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <!-- Sign out -->
                <li class="nxl-item">
                    <a href="logout.php" class="nxl-link text-danger">
                        <span class="nxl-micon"><i class="feather-power"></i></span>
                        <span class="nxl-mtext">Sign out</span>
                    </a>
                </li>
            </ul>

            <div class="card text-center mx-3 mt-4">
                <div class="card-body p-3">
                    <i class="feather-sunrise fs-4 text-primary"></i>
                    <h6 class="mt-3 text-dark fw-bolder">Clinic Admin</h6>
                    <p class="fs-11 text-muted">Management Panel</p>
                </div>
            </div>
        </div>
    </div>
</nav>
<!--! ================================================================ !-->
<!--! [End]  Navigation Menu !-->
<!--! ================================================================ !-->
