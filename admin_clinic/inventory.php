<?php 
include 'auth_check.php'; 
include '../users/config/db.php';

$clinic_id = $_SESSION['clinic_admin_id'];

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
        $item_name = $_POST['item_name'];
        $category = $_POST['category'];
        $quantity = (int)$_POST['quantity'];
        $unit = $_POST['unit'];
        $expiration_date = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
        
        // Calculate Status
        $status = 'Available';
        if ($quantity <= 0) {
            $status = 'Out of Stock';
        } elseif ($quantity < 10) {
            $status = 'Low Stock';
        }

        if ($_POST['action'] === 'add') {
            $stmt = $conn->prepare("INSERT INTO clinic_inventory (clinic_id, item_name, category, quantity, unit, expiration_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississs", $clinic_id, $item_name, $category, $quantity, $unit, $expiration_date, $status);
        } else {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE clinic_inventory SET item_name=?, category=?, quantity=?, unit=?, expiration_date=?, status=? WHERE id=? AND clinic_id=?");
            $stmt->bind_param("ssisssii", $item_name, $category, $quantity, $unit, $expiration_date, $status, $id, $clinic_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Item saved successfully.";
        } else {
            $_SESSION['error'] = "Error saving item.";
        }
        header("Location: inventory.php");
        exit();
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM clinic_inventory WHERE id=? AND clinic_id=?");
        $stmt->bind_param("ii", $id, $clinic_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Item deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting item.";
        }
        header("Location: inventory.php");
        exit();
    } elseif ($_POST['action'] === 'initialize') {
        // One-Click Fix: Create table and seed data
        $sql = "CREATE TABLE IF NOT EXISTS `clinic_inventory` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `clinic_id` INT(10) UNSIGNED NOT NULL,
            `item_name` VARCHAR(255) NOT NULL,
            `category` VARCHAR(100) NOT NULL,
            `quantity` INT(11) NOT NULL DEFAULT 0,
            `unit` VARCHAR(50) NOT NULL,
            `expiration_date` DATE DEFAULT NULL,
            `status` VARCHAR(50) NOT NULL DEFAULT 'Available',
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
            CONSTRAINT `fk_clinic_inventory_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        
        if ($conn->query($sql)) {
            // Seed sample data
            $seed_stmt = $conn->prepare("INSERT INTO clinic_inventory (clinic_id, item_name, category, quantity, unit, expiration_date, status) VALUES 
                (?, 'Rabies Vaccine (Canine)', 'Vaccine', 50, 'Vials', '2026-12-31', 'Available'),
                (?, 'DHLPP Combination Vaccine', 'Vaccine', 30, 'Vials', '2026-08-15', 'Available'),
                (?, 'Amoxicillin 250mg', 'Medicine', 100, 'Tablets', '2027-01-10', 'Available'),
                (?, 'Disposable Syringes (3ml)', 'Supply', 1000, 'Pieces', NULL, 'Available'),
                (?, 'Medical Gauze (Sterile)', 'Supply', 0, 'Packs', NULL, 'Out of Stock')");
            $seed_stmt->bind_param("iiiii", $clinic_id, $clinic_id, $clinic_id, $clinic_id, $clinic_id);
            $seed_stmt->execute();
            
            $_SESSION['success'] = "Inventory initialized with sample data!";
        } else {
            $_SESSION['error'] = "Setup failed: " . $conn->error;
        }
        header("Location: inventory.php");
        exit();
    }
}

// Search and Filter Logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

$items = [];
$total_items = 0;
$low_stock = 0;
$out_of_stock = 0;

$sql = "SELECT * FROM clinic_inventory WHERE clinic_id = ?";
$params = [$clinic_id];
$types = "i";

if (!empty($search)) {
    $sql .= " AND item_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Update statuses automatically to ensure they match quantities
$conn->query("UPDATE clinic_inventory SET status = 'Out of Stock' WHERE quantity <= 0 AND clinic_id = $clinic_id");
$conn->query("UPDATE clinic_inventory SET status = 'Low Stock' WHERE quantity > 0 AND quantity < 10 AND clinic_id = $clinic_id");
$conn->query("UPDATE clinic_inventory SET status = 'Available' WHERE quantity >= 10 AND clinic_id = $clinic_id");

// Re-fetch items after potential update
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total_items = 0;
$low_stock = 0;
$out_of_stock = 0;

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total_items++;
    if ($row['status'] === 'Low Stock') $low_stock++;
    if ($row['status'] === 'Out of Stock') $out_of_stock++;
}

// Standard Categories for Vet Clinic
$all_categories = ['Vaccine', 'Medicine', 'Medical Supply', 'Supply', 'Supplement', 'Equipment', 'Diagnostic', 'Food', 'Grooming', 'Service', 'Other'];

// Get unique categories currently in use (optional, but we'll use $all_categories as requested)
$categories_result = $conn->query("SELECT DISTINCT category FROM clinic_inventory WHERE clinic_id = $clinic_id");
$used_categories = [];
if ($categories_result) {
    while ($cat_row = $categories_result->fetch_assoc()) {
        $used_categories[] = $cat_row['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Inventory | MEDINEST</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendors/css/vendors.min.css" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="assets/css/theme.min.css" />
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php include 'partials/sidebar.php'; ?>


<!-- ===================== HEADER ===================== -->
<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left">
            <h5 class="mb-0">Inventory Management</h5>
        </div>
    </div>
</header>
<!-- ===================== END HEADER ===================== -->


<!-- ===================== MAIN CONTENT ===================== -->
<main class="nxl-container">
    <div class="nxl-content">

        <!-- PAGE HEADER -->
        <div class="page-header mb-4">
            <div class="page-header-left">
                <h5>Clinic Inventory</h5>
                <p class="text-muted mb-0">Medicines, vaccines, and medical supplies</p>
            </div>
        </div>

        <!-- STATS -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0">
                    <h6 class="text-muted">Total Items</h6>
                    <h3 class="fw-bold"><?php echo $total_items; ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0">
                    <h6 class="text-muted">Low Stock</h6>
                    <h3 class="fw-bold text-warning"><?php echo $low_stock; ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0">
                    <h6 class="text-muted">Out of Stock</h6>
                    <h3 class="fw-bold text-danger"><?php echo $out_of_stock; ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center p-3 shadow-sm border-0 bg-primary-soft">
                    <h6 class="text-primary">Clinic ID: <?php echo $clinic_id; ?></h6>
                    <h3 class="fw-bold text-primary">Active</h3>
                </div>
            </div>
        </div>

        <!-- INVENTORY TABLE -->
        <div class="card p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Inventory Records</h5>
                <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> Add New Item
                </button>
            </div>

            <!-- SEARCH AND FILTER -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i data-feather="search" style="width: 16px;"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by item name..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($all_categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category_filter === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary px-4">Filter</button>
                    <?php if (!empty($search) || !empty($category_filter)): ?>
                        <a href="inventory.php" class="btn btn-link text-muted btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Expiration Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i data-feather="box" class="text-muted mb-2" style="width: 48px; height: 48px;"></i>
                                    <p class="text-muted">No inventory items found. Add your first item above.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><span class="fw-bold text-dark"><?php echo htmlspecialchars($item['item_name']); ?></span></td>
                                    <td><span class="badge bg-soft-info text-info"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                    <td><?php echo $item['expiration_date'] ?: '-'; ?></td>
                                    <td>
                                        <?php if ($item['status'] === 'Available'): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php elseif ($item['status'] === 'Low Stock'): ?>
                                            <span class="badge bg-warning">Low Stock</span>
                                        <?php elseif ($item['status'] === 'Out of Stock'): ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($item['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-link text-primary p-0 me-3" 
                                                onclick='editItem(<?php echo json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                title="Edit Item">
                                            <i data-feather="edit-2" style="width: 16px; height: 16px;"></i>
                                        </button>
                                        <button class="btn btn-sm btn-link text-danger p-0" 
                                                onclick="deleteItem(<?php echo $item['id']; ?>)"
                                                title="Delete Item">
                                            <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                        </button>
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

<!-- ADD/EDIT MODAL -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog border-0">
        <div class="modal-content shadow-lg border-0">
            <form method="POST">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Inventory Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="itemId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Name</label>
                        <input type="text" name="item_name" id="item_name" class="form-control" required placeholder="e.g. Rabies Vaccine">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category" id="category" class="form-control" required>
                            <?php foreach ($all_categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Unit</label>
                            <input type="text" name="unit" id="unit" class="form-control" required placeholder="e.g. Vials, Boxes">
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Expiration Date (Optional)</label>
                        <input type="date" name="expiration_date" id="expiration_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE FORM -->
<form id="deleteForm" method="POST" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script src="assets/vendors/js/vendors.min.js"></script>
<script src="assets/js/common-init.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    function editItem(item) {
        document.getElementById('modalTitle').innerText = 'Edit Inventory Item';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('itemId').value = item.id;
        document.getElementById('item_name').value = item.item_name;
        document.getElementById('category').value = item.category;
        document.getElementById('quantity').value = item.quantity;
        document.getElementById('unit').value = item.unit;
        document.getElementById('expiration_date').value = item.expiration_date;
        
        var modal = new bootstrap.Modal(document.getElementById('addItemModal'));
        modal.show();
    }

    function deleteItem(id) {
        Swal.fire({
            title: 'Delete Item?',
            text: "This action cannot be undone. The item will be removed from your stock records.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff4d49',
            cancelButtonColor: '#a8afb9',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        })
    }

    // Show alerts if any
    <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $_SESSION['success']; ?>',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo $_SESSION['error']; ?>',
            icon: 'error'
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>

</body>
</html>
