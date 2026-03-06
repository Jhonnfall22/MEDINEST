<?php 
include 'auth_check.php'; 
include '../users/config/db.php';

$clinic_id = $_SESSION['clinic_admin_id'];

// Get Search and Filter Parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Fetch Distinct Categories for Filter
$cat_stmt = $conn->prepare("SELECT DISTINCT category FROM clinic_inventory WHERE clinic_id = ?");
$cat_stmt->bind_param("i", $clinic_id);
$cat_stmt->execute();
$cat_res = $cat_stmt->get_result();
$categories = [];
while ($cat_row = $cat_res->fetch_assoc()) {
    $categories[] = $cat_row['category'];
}
$cat_stmt->close();

// Build Inventory Query
$sql = "SELECT * FROM clinic_inventory WHERE clinic_id = ?";
$params = [$clinic_id];
$types = "i";

if (!empty($search)) {
    $sql .= " AND item_name LIKE ?";
    $search_param = "%$search%";
    $params[] = $search_param;
    $types .= "s";
}

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$sql .= " ORDER BY quantity ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$inventory_res = $stmt->get_result();
$inventory_items = [];
while ($row = $inventory_res->fetch_assoc()) {
    $inventory_items[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <title>Vet Clinics | Reports</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css" />

    <style>
        .report-card {
            border-radius: 12px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .report-header {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .report-table th, .report-table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <!-- Navigation Menu -->
    <?php include 'partials/sidebar.php'; ?>

    <!-- Header -->
    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse"></a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Reports</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item">Reports</li>
                    </ul>
                </div>
            </div>

            <div class="main-content">
                <div class="row">
                    <!-- Daily Appointments -->
                    <div class="col-md-6">
                        <div class="report-card">
                            <div class="report-header">Daily Appointments</div>
                            <table class="table report-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Client</th>
                                        <th>Pet</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>1</td><td>Charlie</td><td>Dog</td><td>9:00 AM</td></tr>
                                    <tr><td>2</td><td>Mia</td><td>Cat</td><td>10:00 AM</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pet Vaccinations -->
                    <div class="col-md-6">
                        <div class="report-card">
                            <div class="report-header">Pet Vaccinations</div>
                            <table class="table report-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pet</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>1</td><td>Charlie</td><td>Owner</td><td>Completed</td></tr>
                                    <tr><td>2</td><td>Mia</td><td>Family</td><td>Pending</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Inventory Report -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="report-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="report-header mb-0">Inventory Report</div>
                                
                                <!-- Search and Filter Form -->
                                <form method="GET" class="d-flex gap-2 align-items-center w-50">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search item..." value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                    <select name="category" class="form-select form-select-sm w-50" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category_filter === $cat) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (!empty($search) || !empty($category_filter)): ?>
                                        <a href="report.php" class="btn btn-sm btn-light border" title="Clear Filters"><i class="bi bi-x-circle"></i></a>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <table class="table report-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($inventory_items)): ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No inventory records found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($inventory_items as $index => $item): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($item['item_name']); ?></td>
                                                <td><span class="badge bg-soft-info text-info"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                                <td><?php echo htmlspecialchars($item['quantity'] . ' ' . $item['unit']); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $item['status'];
                                                    $badge_class = 'bg-success';
                                                    if ($status === 'Out of Stock') $badge_class = 'bg-danger';
                                                    if ($status === 'Low Stock') $badge_class = 'bg-warning text-dark';
                                                    ?>
                                                    <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Vendors JS -->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/vendors/js/daterangepicker.min.js"></script>
    <script src="assets/vendors/js/apexcharts.min.js"></script>
    <script src="assets/vendors/js/circle-progress.min.js"></script>

    <!-- Apps Init -->
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/dashboard-init.min.js"></script>
    <script src="assets/js/theme-customizer-init.min.js"></script>

</body>

</html>
