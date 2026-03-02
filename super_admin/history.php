<?php
session_start();
require_once "../config/db.php";

$approved_sql = "SELECT * FROM clinics WHERE status='Approved'";
$approved_result = $conn->query($approved_sql);

/*
DO NOT CHANGE YOUR DESIGN   
ONLY added PHP to fetch approved and rejected clinics
*/

/* ADD THIS BLOCK */
$rejected_sql = "SELECT * FROM clinics WHERE status='Rejected'";
$rejected_result = $conn->query($rejected_sql);

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic History | Super Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --admin-primary: #220bef;
            --bg-body: #f4f7fa;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-main: #333333;
            --text-muted: #6c757d;
            --border-color: #e0e0e0;
            --sidebar-width: 260px;
        }

        [data-bs-theme="dark"] {
            --bg-body: #0b0e14;
            --sidebar-bg: #151921;
            --card-bg: #151921;
            --text-main: #e1e1e1;
            --text-muted: #a0a0a0;
            --border-color: #2d343f;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: 0.3s ease;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            border-right: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 14px 25px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(34,11,239,0.08);
            color: var(--admin-primary);
        }

        .main {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .theme-toggle {
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <a href="index_superadmin.php"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="client.php"><i class="fas fa-users"></i> Clients</a>
    <a href="clinic.php"><i class="fas fa-home"></i> Clinics</a>
    <a href="history.php" class="active">
        <i class="fas fa-clock-rotate-left"></i> Clinic History
    </a>
    <a href="logout.php" class="text-danger">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Clinic Verification History</h4>
            <p class="text-muted small mb-0">Approved and Rejected Clinics</p>
        </div>
        <div class="theme-toggle" id="darkModeBtn">
            <i class="fas fa-moon" id="themeIcon"></i>
        </div>
    </div>

    <div class="card p-4">
        <ul class="nav nav-tabs mb-4" id="historyTab">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#approved">
                    Approved Clinics
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected">
                    Rejected Clinics
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- Approved -->
            <div class="tab-pane fade show active" id="approved">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Clinic Name</th>
                                <th>Head Doctor</th>
                                <th>Location</th>
                                <th>Approved Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                        if($approved_result && $approved_result->num_rows > 0)
                        {
                            while($row = $approved_result->fetch_assoc())
                            {
                                echo "<tr>";
                                echo "<td>".$row['clinic_name']."</td>";
                                echo "<td>".$row['first_name']." ".$row['last_name']."</td>";
                                echo "<td>".$row['address']."</td>";
                                echo "<td>".$row['approved_date']."</td>";
                                echo "<td><span class='badge bg-success rounded-pill px-3'>Approved</span></td>";
                                echo "</tr>";
                            }
                        }
                        else
                        {
                            echo "<tr><td colspan='5' class='text-center'>No approved clinics</td></tr>";
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rejected -->
            <div class="tab-pane fade" id="rejected">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Clinic Name</th>
                                <th>Head Doctor</th>
                                <th>Location</th>
                                <th>Rejected Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                        if($rejected_result && $rejected_result->num_rows > 0)
                        {
                            while($row = $rejected_result->fetch_assoc())
                            {
                                echo "<tr>";
                                echo "<td>".$row['clinic_name']."</td>";
                                echo "<td>".$row['first_name']." ".$row['last_name']."</td>";
                                echo "<td>".$row['address']."</td>";
                                echo "<td>".$row['rejected_date']."</td>";
                                echo "<td><span class='badge bg-danger rounded-pill px-3'>Rejected</span></td>";
                                echo "</tr>";
                            }
                        }
                        else
                        {
                            echo "<tr><td colspan='5' class='text-center'>No rejected clinics</td></tr>";
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const body = document.documentElement;
const darkModeBtn = document.getElementById('darkModeBtn');
const themeIcon = document.getElementById('themeIcon');

function applyTheme(theme) {
    body.setAttribute('data-bs-theme', theme);
    localStorage.setItem('admin_theme', theme);
    themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

darkModeBtn.addEventListener('click', () => {
    const current = body.getAttribute('data-bs-theme');
    applyTheme(current === 'dark' ? 'light' : 'dark');
});

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('admin_theme') || 'light';
    applyTheme(saved);
});
</script>

</body>
</html>