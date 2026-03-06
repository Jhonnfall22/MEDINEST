<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php";

// Fetch user data for sidebar
$user_id = $_SESSION['user_id'] ?? null;
$username_reg = $_SESSION['username'] ?? 'Guest';

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Ensure the registration identifier is the user's email for reliable connection
$username_reg = $_SESSION['email'] ?? ($user['email'] ?? 'Guest');

// Handle Form Submission
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_pet'])) {
    $name   = trim($_POST['pet_name'] ?? '');
    $type   = trim($_POST['pet_type'] ?? '');
    $breed  = trim($_POST['breed'] ?? '');
    $age    = (int)($_POST['age'] ?? 0);
    $clinic = trim($_POST['clinic'] ?? '');
    $concern = trim($_POST['concern'] ?? '');
    if (!empty($name) && !empty($type) && !empty($clinic)) {
        try {
            $status = 'Approved';
            $sql = "INSERT INTO pets (name, type, breed, age, clinic, status, user, created_at, concern) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssissss", $name, $type, $breed, $age, $clinic, $status, $username_reg, $concern);
                if ($stmt->execute()) {
                    $success_msg = "Pet registered successfully! Your pet is now ready.";
                } else {
                    throw new Exception($stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            $status = 'Approved';
            $fallback_sql = "INSERT INTO pets (name, type, breed, age, clinic, status, user, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($fallback_sql);
            if ($stmt) {
                $stmt->bind_param("sssisss", $name, $type, $breed, $age, $clinic, $status, $username_reg);
                if ($stmt->execute()) {
                    $success_msg = "Pet registered successfully! Your pet is now ready.";
                } else {
                    $error_msg = "Error registering pet: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}

// Fetch Clinics for Dropdown
$clinics = [];
$clinic_res = $conn->query("SELECT clinic_name FROM clinics WHERE status = 'Approved' ORDER BY clinic_name ASC");
if ($clinic_res) {
    while ($row = $clinic_res->fetch_assoc()) {
        $clinics[] = $row['clinic_name'];
    }
}

include 'userSidebar.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    :root { 
        --admin-primary: #2563eb; 
        --admin-primary-dark: #1e40af;
        --bg-body: #f8fafc; 
        --border-color: #e2e8f0; 
        --premium-gradient: linear-gradient(135deg, #1a56db 0%, #3b82f6 100%);
    }
    
    /* Overlay managed by sidebar toggle */
    .overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
    }
        .overlay.active { display: block; }

        .main-content {
            margin-left: 280px; 
            padding: 0; /* Reset for top-header */
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Header styling to match appointments.php */
        .top-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
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
            background: #f1f5f9;
            border-radius: 10px;
            color: #64748b;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
        }

        .breadcrumb-nav { display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .breadcrumb-nav a { color: #64748b; text-decoration: none; transition: color 0.2s; }
        .breadcrumb-nav a:hover { color: var(--admin-primary); }
        .breadcrumb-nav span { color: #cbd5e1; }
        .breadcrumb-nav .current { color: #0f172a; font-weight: 500; }

        .header-actions { display: flex; align-items: center; gap: 8px; }
        .header-btn {
            width: 40px; height: 40px; border: none; background: #f1f5f9;
            border-radius: 10px; color: #64748b; display: flex;
            align-items: center; justify-content: center;
            cursor: pointer; position: relative; transition: all 0.2s ease;
        }
        .header-btn:hover { background: var(--admin-primary); color: white; }

        .page-content { padding: 32px; }

        /* Pet Card Styles */
        .stat-card { 
            background:#fff; 
            border:1px solid var(--border-color); 
            border-radius:16px; 
            padding:24px; 
            text-align:center; 
            transition: all 0.3s ease; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--admin-primary);
        }
        .stat-card small { font-size:0.75rem; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:1px; }
        .stat-card h3 { font-weight:700; margin:8px 0 0; color:#1e293b; font-size: 1.75rem; }

        .pet-card { 
            background:#fff; 
            border:1px solid var(--border-color); 
            border-radius:20px; 
            padding:30px; 
            margin-bottom:20px; 
            display:flex; 
            justify-content:space-between; 
            align-items:center; 
            transition:all 0.3s ease; 
        }
        .pet-card:hover { border-color:var(--admin-primary); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); transform: translateY(-2px); }
        .pet-info h5 { font-weight:700; margin-bottom:15px; color:var(--admin-primary); font-size: 1.25rem; }
        .pet-details-grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:12px 40px; }
        .pet-details-grid span { font-size:0.95rem; color: #475569; }
        .pet-details-grid strong { color:#1e293b; font-weight:600; margin-right: 8px; }

        .status-badge { padding:10px 20px; border-radius:50px; font-weight:700; font-size:0.8rem; text-transform:uppercase; letter-spacing: 0.5px; }
        .status-completed, .status-approved { background-color:rgba(25, 135, 84, 0.1); color:#198754; border: 1px solid rgba(25, 135, 84, 0.2); }
        .status-pending { background-color:rgba(255, 193, 7, 0.1); color:#9a6d00; border: 1px solid rgba(255, 193, 7, 0.2); }
        .status-rejected { background-color:rgba(220, 53, 69, 0.1); color:#dc3545; border: 1px solid rgba(220, 53, 69, 0.2); }

        /* MODAL PREMIUM STYLING */
        .modal-content { border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .modal-header { background: var(--premium-gradient); color: white; padding: 30px; border: none; position: relative; }
        .modal-header .btn-close { filter: brightness(0) invert(1); position: absolute; top: 20px; right: 20px; }
        .modal-title { font-weight: 700; font-size: 1.5rem; }
        .modal-subtitle { opacity: 0.8; font-size: 0.9rem; }
        
        .modal-body { padding: 40px; }
        .btn-register-submit {
            background: var(--premium-gradient); border: none; border-radius: 12px;
            padding: 14px; font-size: 1rem; font-weight: 600; color: white;
            width: 100%; transition: all 0.3s ease; margin-top: 10px;
        }

        @media (max-width:992px) {
            .main-content { margin-left: 0; }
            .mobile-toggle { display: flex; }
        }

        @media (max-width:768px){ 
            .pet-card{ flex-direction:column; align-items:flex-start; gap: 20px; } 
            .pet-details-grid { grid-template-columns: 1fr; }
            .page-content { padding: 20px; }
            .top-header { padding: 16px 20px; }
        }
    </style>
</head>
<body>

<div class="overlay" id="overlay"></div>

<main class="main-content">
    <!-- Top Header Integration -->
    <header class="top-header">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="breadcrumb-nav">
                <a href="../dashboard.php">Dashboard</a>
                <span>/</span>
                <span class="current">My Pets</span>
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
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="fw-bold m-0" style="color: #0f172a; font-size: 26px;">My Pets</h1>
                <p class="text-muted mb-0">Track your pet registration and appointment status below.</p>
            </div>
            <div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#registerPetModal">
                    <i class="bi bi-plus-lg me-2"></i>Register New Pet
                </button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <small>Total Pets Managed</small>
                    <h3 id="totalPets">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-bottom:4px solid #198754;">
                    <small>Records Completed</small>
                    <h3 id="completedPets" class="text-success">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-bottom:4px solid #ffc107;">
                    <small>Pending Checks</small>
                    <h3 id="pendingPets" class="text-warning">0</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12" id="petList"></div>
        </div>
    </div>
</main>

<!-- REGISTER PET MODAL (same as before) -->
<div class="modal fade" id="registerPetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title"><i class="bi bi-paw-fill me-2"></i>Register My Pet</h5>
                <p class="modal-subtitle mb-0">Fill in the details to register your pet with a clinic.</p>
            </div>
            <div class="modal-body">
                <form action="mypets.php" method="POST">
                    <input type="hidden" name="register_pet" value="1">
                    <div class="mb-3">
                        <label class="form-label">Pet Name</label>
                        <input type="text" name="pet_name" class="form-control" placeholder="Enter pet name" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Pet Type</label>
                            <select name="pet_type" class="form-select" required>
                                <option value="" selected disabled>Select Type</option>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                                <option value="Bird">Bird</option>
                                <option value="Rabbit">Rabbit</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Breed</label>
                            <input type="text" name="breed" class="form-control" placeholder="e.g. Shih Tzu">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Age (Years)</label>
                            <input type="number" name="age" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Target Clinic</label>
                            <select name="clinic" class="form-select" required>
                                <option value="" selected disabled>Choose Clinic</option>
                                <?php foreach ($clinics as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Reason for Registration / Concern</label>
                        <textarea name="concern" class="form-control" rows="3" placeholder="Describe any health concerns..."></textarea>
                    </div>
                    <button type="submit" class="btn-register-submit">
                        <i class="bi bi-check2-circle me-2"></i>Submit Registration
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar Toggle Logic
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const mobileToggle = document.getElementById('mobileToggle');

if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });
}

if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
}

// Pet List Functionality
const petList = document.getElementById('petList');
function renderPets(data){
    petList.innerHTML = '';
    if (!data.pets || data.pets.length === 0) {
        petList.innerHTML = `<div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
            <h4 class="mt-3 fw-bold">No Pets Found</h4>
            <p class="text-muted">You haven't registered any pets yet.</p>
            <button class="btn btn-primary rounded-pill px-4 mt-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#registerPetModal">Register Your First Pet</button>
        </div>`;
        return;
    }
    data.pets.forEach(pet=>{
        const status = pet.status.toLowerCase();
        let statusClass = 'status-pending';
        if (status === 'completed' || status === 'approved') statusClass = 'status-approved';
        if (status === 'rejected') statusClass = 'status-rejected';

        const card = document.createElement('div');
        card.classList.add('pet-card');
        card.innerHTML=`<div class="pet-info">
            <h5><i class="bi bi-paw-fill me-2"></i>${pet.name}</h5>
            <div class="pet-details-grid">
                <span><strong>Type:</strong> ${pet.type}</span>
                <span><strong>Breed:</strong> ${pet.breed}</span>
                <span><strong>Age:</strong> ${pet.age} Years</span>
                <span><strong>Clinic:</strong> ${pet.clinic}</span>
            </div>
        </div>
        <div class="status-badge ${statusClass}">
            <i class="bi ${status === 'approved' || status === 'completed' ? 'bi-check-circle-fill' : (status === 'rejected' ? 'bi-x-circle-fill' : 'bi-clock-history')} me-1"></i>
            ${pet.status}
        </div>`;
        petList.appendChild(card);
    });
    document.getElementById('totalPets').innerText = data.total;
    document.getElementById('completedPets').innerText = data.completed;
    document.getElementById('pendingPets').innerText = data.pending;
}

async function fetchPets(){
    try{
        const response = await fetch('get_pets.php');
        const data = await response.json();
        renderPets(data);
    } catch(e){
        console.error('Error fetching pets:',e);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    fetchPets();
    <?php if ($success_msg): ?>
        Swal.fire({ icon: 'success', title: 'Registration Sent!', text: '<?php echo $success_msg; ?>', confirmButtonColor: '#2563eb' });
    <?php endif; ?>
    <?php if ($error_msg): ?>
        Swal.fire({ icon: 'error', title: 'Oops...', text: '<?php echo $error_msg; ?>', confirmButtonColor: '#2563eb' });
    <?php endif; ?>
});
</script>
</body>
</html>