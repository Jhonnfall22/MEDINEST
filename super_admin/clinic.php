<?php
require_once '../config/db.php';

// Fetch clinics
$query = "SELECT * FROM clinics ORDER BY created_at DESC";
$result = $conn->query($query);
$clinics = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | Clinic Manager</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --admin-primary: #220bef; --bg-body: #f4f7fa; --sidebar-bg: #ffffff; --card-bg: #ffffff; --text-main: #333333; --text-muted: #6c757d; --border-color: #e0e0e0; --sidebar-width: 260px; --sidebar-collapsed-width: 85px; }
        [data-bs-theme="dark"] { --bg-body: #0b0e14; --sidebar-bg: #151921; --card-bg: #151921; --text-main: #e1e1e1; --text-muted: #a0a0a0; --border-color: #2d343f; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); color: var(--text-main); transition: all 0.3s ease; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); background: var(--sidebar-bg); height: 100vh; position: fixed; top: 0; left: 0; border-right: 1px solid var(--border-color); z-index: 1050; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
        .sidebar-brand { padding: 25px; height: 80px; display: flex; align-items: center; font-weight: 700; color: var(--admin-primary); white-space: nowrap; }
        .nav-link { color: var(--text-muted); padding: 14px 25px; display: flex; align-items: center; gap: 15px; font-weight: 500; transition: 0.2s; white-space: nowrap; text-decoration: none; }
        .nav-link:hover, .nav-link.active { color: var(--admin-primary); background: rgba(34, 11, 239, 0.08); }
        .nav-link.active { border-right: 4px solid var(--admin-primary); }
        .sidebar-collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar-collapsed .sidebar-text, .sidebar-collapsed .brand-text { opacity: 0; pointer-events: none; }
        .nxl-container { margin-left: var(--sidebar-width); padding: 30px; transition: margin-left 0.3s ease; }
        .content-collapsed { margin-left: var(--sidebar-collapsed-width); }
        .card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 25px; overflow: hidden; }
        .theme-toggle { cursor: pointer; width: 40px; height: 40px; border-radius: 10px; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main); display: flex; align-items: center; justify-content: center; }
        .chart-container { position: relative; height: 280px; width: 100%; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; } .sidebar.mobile-show { transform: translateX(0); } .nxl-container { margin-left: 0 !important; padding: 20px; } }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-feather="shield"></i>
        <span class="brand-text ms-2">SUPER ADMIN</span>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link" href="index_superadmin.php"><i data-feather="grid"></i> <span class="sidebar-text">Dashboard</span></a>
        <a class="nav-link" href="client.php"><i data-feather="users"></i> <span class="sidebar-text">Clients</span></a>
        <a class="nav-link active" href="clinic.php"><i data-feather="home"></i> <span class="sidebar-text">Clinics</span></a>
        <hr class="mx-3 my-2" style="opacity: 0.1;">
        <a class="nav-link text-danger" href="login.php"><i data-feather="log-out"></i> <span class="sidebar-text">Logout</span></a>
    </nav>
</aside>

<main class="nxl-container" id="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Clinic Verification</h4>
            <p class="text-muted small mb-0">Manage and verify clinic registrations</p>
        </div>
        <div class="d-flex gap-2">
            <button class="theme-toggle" id="darkModeBtn">
                <i data-feather="moon" id="themeIcon"></i>
            </button>
            <button class="theme-toggle" id="toggleSidebar">
                <i data-feather="menu"></i>
            </button>
        </div>
    </div>

    <div class="card p-4">
        <h6 class="fw-bold text-muted mb-4">New Clinic Registrations</h6>
        <div class="chart-container">
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h6 class="fw-bold mb-0">Verification Queue</h6>
            <div class="input-group input-group-sm" style="max-width: 300px;">
                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Search clinic or doctor...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Clinic Details</th>
                        <th>Head Doctor</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clinics as $clinic): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?= htmlspecialchars($clinic['clinic_name']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($clinic['address']) ?></div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    <?= strtoupper(substr($clinic['first_name'] ?? '',0,1).substr($clinic['last_name'] ?? '',0,1)) ?>
                                </div>
                                <span>Dr. <?= htmlspecialchars(($clinic['first_name'] ?? '').' '.($clinic['last_name'] ?? '')) ?></span>
                            </div>
                        </td>
                        <td>
                            <?php
                            $status_classes = ['pending' => 'bg-warning-subtle text-warning','approved' => 'bg-success-subtle text-success','rejected' => 'bg-danger-subtle text-danger'];
                            $badge = $status_classes[$clinic['status']] ?? 'bg-secondary text-muted';
                            ?>
                            <span class="badge <?= $badge ?> px-3 rounded-pill"><?= ucfirst($clinic['status'] ?? 'N/A') ?></span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-primary btn-sm rounded-pill px-4" 
                                data-bs-toggle="modal" 
                                data-bs-target="#reviewModal" 
                                data-clinic='<?= json_encode($clinic) ?>'>
                                Review
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($clinics)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">No clinic registrations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- REVIEW MODAL WITH IMAGE & PDF PREVIEW -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Review Clinic Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small d-block mb-1">Clinic Name</label>
                    <h6 class="fw-bold" id="modalClinicName"></h6>
                </div>
                <div class="mb-4">
                    <label class="text-muted small d-block mb-2">Submitted Documents</label>

                    <!-- Verification File -->
                    <div class="p-3 border rounded bg-light-subtle mb-2">
                        <div class="small fw-bold" id="modalVerificationFile"></div>
                        <div class="text-muted" style="font-size: 0.7rem;" id="modalVerificationDate"></div>
                        <div class="mt-2" id="modalViewVerificationPreviewContainer"></div>
                    </div>

                    <!-- Face Auth File -->
                    <div class="p-3 border rounded bg-light-subtle mb-2">
                        <div class="small fw-bold" id="modalFaceAuthFile"></div>
                        <div class="text-muted" style="font-size: 0.7rem;" id="modalFaceAuthDate"></div>
                        <div class="mt-2" id="modalViewFaceAuthPreviewContainer"></div>
                    </div>

                    <!-- ID Validation File -->
                    <div class="p-3 border rounded bg-light-subtle mb-2">
                        <div class="small fw-bold" id="modalIDValidationFile"></div>
                        <div class="text-muted" style="font-size: 0.7rem;" id="modalIDValidationDate"></div>
                        <div class="mt-2" id="modalViewIDValidationPreviewContainer"></div>
                    </div>
                </div>
                <div class="alert alert-warning py-2 small border-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Please verify the validity of the documents.
                </div>
            </div>
            <div class="modal-footer bg-light-subtle">
                <form method="post" action="clinic_verify_action.php" class="d-flex gap-2 w-100">
                    <input type="hidden" name="clinic_id" id="modalClinicID">
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm px-4">Decline</button>
                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm px-4">Approve Clinic</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();

    const body = document.documentElement;
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('main-content');
    const toggleBtn = document.getElementById('toggleSidebar');
    const darkModeBtn = document.getElementById('darkModeBtn');
    const themeIcon = document.getElementById('themeIcon');
    let lineChart;

    function applyTheme(theme) {
        body.setAttribute('data-bs-theme', theme);
        localStorage.setItem('admin_theme', theme);
        themeIcon.outerHTML = theme === 'dark' ? '<i data-feather="sun" id="themeIcon"></i>' : '<i data-feather="moon" id="themeIcon"></i>';
        feather.replace();
        initChart(theme);
    }

    darkModeBtn.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-bs-theme');
        applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
    });

    toggleBtn.addEventListener('click', () => {
        if(window.innerWidth > 992) { sidebar.classList.toggle('sidebar-collapsed'); content.classList.toggle('content-collapsed'); }
        else { sidebar.classList.toggle('mobile-show'); }
    });

    function initChart(theme){
        const ctx = document.getElementById('lineChart').getContext('2d');
        if(lineChart) lineChart.destroy();
        const isDark = theme==='dark';
        lineChart = new Chart(ctx,{
            type:'line',
            data:{
                labels:['Jan','Feb','Mar','Apr','May','Jun'],
                datasets:[{
                    label:'Clinics',
                    data:[10,25,15,30,45,60],
                    borderColor:'#220bef',
                    backgroundColor:'rgba(34,11,239,0.1)',
                    fill:true,
                    tension:0.4,
                    borderWidth:3
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{
                    y:{grid:{color:isDark?'rgba(255,255,255,0.05)':'#f0f0f0'},ticks:{color:isDark?'#a0a0a0':'#6c757d'}},
                    x:{grid:{display:false},ticks:{color:isDark?'#a0a0a0':'#6c757d'}}
                }
            }
        });
    }

    function setPreview(containerId, fileName) {
        const container = document.getElementById(containerId);
        container.innerHTML = ''; // Clear previous
        if(!fileName) { container.innerHTML = '<div class="text-muted small">No file uploaded</div>'; return; }
        const ext = fileName.split('.').pop().toLowerCase();
        const safeFile = encodeURIComponent(fileName); 

        if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
            container.innerHTML = `<img src="../uploads/${safeFile}" style="width:100%; height:200px; object-fit:contain;">`;
        } else if(ext==='pdf') {
            container.innerHTML = `<iframe src="../uploads/${safeFile}" style="width:100%; height:200px; border:none;"></iframe>`;
        } else {
            container.innerHTML = `<div class="text-muted small">Cannot preview this file type</div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', ()=>{
        const savedTheme = localStorage.getItem('admin_theme') || 'light';
        applyTheme(savedTheme);

        const reviewModal = document.getElementById('reviewModal');
        reviewModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const clinic = JSON.parse(button.getAttribute('data-clinic'));

            document.getElementById('modalClinicName').textContent = clinic.clinic_name ?? 'N/A';
            document.getElementById('modalClinicID').value = clinic.id ?? '';

            document.getElementById('modalVerificationFile').textContent = clinic.verification_file ?? 'N/A';
            document.getElementById('modalVerificationDate').textContent = 'Uploaded on ' + (clinic.created_at ? new Date(clinic.created_at).toLocaleDateString() : 'N/A');
            setPreview('modalViewVerificationPreviewContainer', clinic.verification_file);

            document.getElementById('modalFaceAuthFile').textContent = clinic.face_auth_file ?? 'N/A';
            document.getElementById('modalFaceAuthDate').textContent = 'Uploaded on ' + (clinic.created_at ? new Date(clinic.created_at).toLocaleDateString() : 'N/A');
            setPreview('modalViewFaceAuthPreviewContainer', clinic.face_auth_file);

            document.getElementById('modalIDValidationFile').textContent = clinic.id_validation_file ?? 'N/A';
            document.getElementById('modalIDValidationDate').textContent = 'Uploaded on ' + (clinic.created_at ? new Date(clinic.created_at).toLocaleDateString() : 'N/A');
            setPreview('modalViewIDValidationPreviewContainer', clinic.id_validation_file);
        });
    });
</script>
</body>
</html>