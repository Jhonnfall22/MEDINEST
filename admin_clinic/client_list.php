<?php 
include 'auth_check.php'; 
include '../users/config/db.php';

$clinic_id = $_SESSION['clinic_admin_id'];

// Get Clinic Name for filtering pets
$stmt = $conn->prepare("SELECT clinic_name FROM clinics WHERE id = ?");
$stmt->bind_param("i", $clinic_id);
$stmt->execute();
$clinic_res = $stmt->get_result()->fetch_assoc();
$clinic_name = $clinic_res['clinic_name'] ?? '';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $pet_id = (int)$_POST['id'];
        $new_status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE pets SET status = ? WHERE id = ? AND clinic = ?");
        $stmt->bind_param("sis", $new_status, $pet_id, $clinic_name);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Pet status updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating status.";
        }
        header("Location: client_list.php");
        exit();
    }
}

// Search and Filter Logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT p.*, u.first_name as owner_fname, u.last_name as owner_lname, u.email as owner_email, u.contact as owner_contact 
        FROM pets p 
        LEFT JOIN users u ON (p.user = u.email OR p.user = CONCAT(u.first_name, ' ', u.last_name))
        WHERE p.clinic = ?";
$params = [$clinic_name];
$types = "s";

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.user LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sssss";
}

if (!empty($status_filter)) {
    $sql .= " AND p.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
$total_pets = 0;
$pending_pets = 0;

while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
    $total_pets++;
    if (strtolower($row['status']) === 'pending') $pending_pets++;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Patients | MEDINEST</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" href="assets/css/theme.min.css" />
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php include 'partials/sidebar.php'; ?>

<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left">
            <h5 class="mb-0">Patient Management</h5>
        </div>
    </div>
</header>

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header mb-4">
            <div class="page-header-left">
                <h5>Patient List</h5>
                <p class="text-muted mb-0">Registered pets and owner details for <?php echo htmlspecialchars($clinic_name); ?></p>
            </div>
        </div>

        <!-- STATS -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card p-3 shadow-sm border-0">
                    <h6 class="text-muted mb-1 small text-uppercase">Total Patients</h6>
                    <h3 class="fw-bold mb-0"><?php echo $total_pets; ?></h3>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card p-3 shadow-sm border-0">
                    <h6 class="text-muted mb-1 small text-uppercase">Pending Requests</h6>
                    <h3 class="fw-bold mb-0 text-warning"><?php echo $pending_pets; ?></h3>
                </div>
            </div>
        </div>

        <div class="card p-4 shadow-sm border-0">
            <!-- SEARCH AND FILTER -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group text-white">
                        <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 16px;"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search pet or owner..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo ($status_filter === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo ($status_filter === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary px-4 text-white">Filter</button>
                    <?php if (!empty($search) || !empty($status_filter)): ?>
                        <a href="client_list.php" class="btn btn-link text-muted btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Pet Name</th>
                            <th>Species/Breed</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Concern</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pets)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted mb-0">No pets found for this clinic.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pets as $index => $pet): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><span class="fw-bold text-dark"><?php echo htmlspecialchars($pet['name']); ?></span></td>
                                    <td>
                                        <div class="small text-muted"><?php echo htmlspecialchars($pet['type']); ?></div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($pet['breed']); ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($pet['owner_fname'])): ?>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($pet['owner_fname'] . ' ' . $pet['owner_lname']); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($pet['owner_email']); ?></div>
                                            <div class="small text-muted"><i data-feather="phone" style="width: 12px;"></i> <?php echo htmlspecialchars($pet['owner_contact'] ?? 'N/A'); ?></div>
                                        <?php else: ?>
                                            <span class="text-muted fw-medium"><?php echo htmlspecialchars($pet['user']); ?></span>
                                            <div class="small text-warning">Account not found</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $status_class = 'bg-soft-warning text-warning';
                                        if (strtolower($pet['status']) === 'approved') $status_class = 'bg-soft-success text-success';
                                        if (strtolower($pet['status']) === 'rejected') $status_class = 'bg-soft-danger text-danger';
                                        ?>
                                        <span class="badge <?php echo $status_class; ?> px-3"><?php echo ucfirst($pet['status']); ?></span>
                                    </td>
                                    <td class="small text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($pet['concern'] ?? '-'); ?></td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Manage
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="updateStatus(<?php echo $pet['id']; ?>, 'approved')">Approve</a></li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="updateStatus(<?php echo $pet['id']; ?>, 'rejected')">Reject</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick='viewDetails(<?php echo json_encode($pet, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>View Details</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- DETAILS MODAL -->
<div class="modal fade" id="petDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pet Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Pet Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td width="100">Name:</td><td class="fw-bold" id="detName"></td></tr>
                            <tr><td>Species:</td><td id="detType"></td></tr>
                            <tr><td>Breed:</td><td id="detBreed"></td></tr>
                            <tr><td>Age:</td><td id="detAge"></td></tr>
                            <tr><td>Registered:</td><td id="detCreated"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Owner & Visit</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td width="100">Owner:</td><td class="fw-bold" id="detOwner"></td></tr>
                            <tr><td>Email:</td><td id="detEmail" class="small"></td></tr>
                            <tr><td>Contact:</td><td id="detContact"></td></tr>
                            <tr><td>Status:</td><td id="detStatus"></td></tr>
                        </table>
                        <label class="small text-muted mb-1">Concern:</label>
                        <div class="p-2 bg-light rounded small" id="detConcern"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- STATUS FORM -->
<form id="statusForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="update_status">
    <input type="hidden" name="id" id="statusId">
    <input type="hidden" name="status" id="statusValue">
</form>

<script src="assets/vendors/js/vendors.min.js"></script>
<script src="assets/js/common-init.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    function updateStatus(id, status) {
        Swal.fire({
            title: 'Update Status?',
            text: `Set this registration as ${status.toUpperCase()}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'approved' ? '#28c76f' : '#ff4d49',
            confirmButtonText: 'Yes, update it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('statusId').value = id;
                document.getElementById('statusValue').value = status;
                document.getElementById('statusForm').submit();
            }
        })
    }

    function viewDetails(pet) {
        document.getElementById('detName').innerText = pet.name;
        document.getElementById('detType').innerText = pet.type;
        document.getElementById('detBreed').innerText = pet.breed;
        document.getElementById('detAge').innerText = pet.age;
        document.getElementById('detCreated').innerText = pet.created_at;
        document.getElementById('detOwner').innerText = pet.owner_fname ? (pet.owner_fname + ' ' + pet.owner_lname) : pet.user;
        document.getElementById('detEmail').innerText = pet.owner_email || 'N/A';
        document.getElementById('detContact').innerText = pet.owner_contact || 'N/A';
        document.getElementById('detStatus').innerHTML = `<span class="badge bg-soft-info text-info">${pet.status}</span>`;
        document.getElementById('detConcern').innerText = pet.concern || 'No special concern noted.';
        
        var modal = new bootstrap.Modal(document.getElementById('petDetailsModal'));
        modal.show();
    }

    <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({ title: 'Success!', text: '<?php echo $_SESSION['success']; ?>', icon: 'success', timer: 3000, showConfirmButton: false });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({ title: 'Error!', text: '<?php echo $_SESSION['error']; ?>', icon: 'error' });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>

</body>
</html>
