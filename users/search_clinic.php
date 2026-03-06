<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config/db.php"; 


$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic  = $_POST['clinic'] ?? '';
    $name    = $_POST['name'] ?? '';
    $type    = $_POST['type'] ?? '';
    $breed   = $_POST['breed'] ?? '';
    $age     = $_POST['age'] ?? 0;
    $concern = $_POST['concern'] ?? '';
    $status  = 'pending';

 
    // Use email as the consistent identifier for account association
    $username_reg = $_SESSION['email'] ?? ($user['email'] ?? 'Guest');

    $sql = "INSERT INTO pets (user, name, type, breed, age, concern, clinic, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssisss", $username_reg, $name, $type, $breed, $age, $concern, $clinic, $status);
        $stmt->execute();
        $stmt->close();

       
        header("Location: mypets.php");
        exit();
    } else {
        die("Database error: " . $conn->error);
    }
}


$clinics_data = [];
$clinic_query = "SELECT * FROM clinics WHERE status = 'Approved'";
$result = $conn->query($clinic_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $clinics_data[] = [
            'id' => (int)$row['id'],
            'name' => $row['clinic_name'],
            'location' => $row['address'], 
            'address' => $row['address'], 
            'lat' => (double)$row['lat'],
            'lng' => (double)$row['lng'],
            'rating' => 4.5, 
            'reviews' => 10, 
            'hours' => '8:00 AM - 5:00 PM', 
            'phone' => $row['contact'],
            'status' => 'open', 
            'icon' => 'bi-hospital' 
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pet | VetClinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

    <style>
        :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-200: #bfdbfe;
            --primary-300: #93c5fd;
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --primary-800: #1e40af;
            --primary-900: #1e3a8a;
            --gradient-primary: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            --gradient-light: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        }

        .nav-btn {
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .nav-btn-outline {
            background: white;
            border: 2px solid var(--primary-200);
            color: var(--primary-700);
        }
        .nav-btn-outline:hover {
            border-color: var(--primary-500);
            background: var(--primary-50);
        }
        .nav-btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
        }
        .nav-btn-primary:hover {
            box-shadow: 0 5px 20px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
            color: white;
        }

/* Main Layout */
.main-container {
    padding-top: 70px;
    min-height: 100vh;
}

/* Map Section */
.map-section {
    position: relative;
    height: 60vh;
    background: var(--primary-900);
}
#map {
    height: 100%;
    width: 100%;
    z-index: 1;
}

/* Map Overlay Info - Left Side */
.map-overlay {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
    background: rgba(255,255,255,0.98);
    backdrop-filter: blur(20px);
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(30, 64, 175, 0.2);
    width: 300px;
    border: 1px solid var(--primary-100);
    animation: slideInLeft 0.5s ease;
}
.map-overlay h5 {
    font-weight: 700;
    color: var(--primary-800);
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
}
.map-overlay h5 i {
    color: var(--primary-500);
}
.map-overlay p {
    color: #64748b;
    font-size: 0.8rem;
    margin-bottom: 0;
}
.location-status {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding: 10px 14px;
    background: var(--primary-50);
    border-radius: 10px;
    font-size: 0.8rem;
    color: var(--primary-700);
    border: 1px solid var(--primary-200);
}
.location-status.success {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
.location-status.loading {
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
}
.location-status.error {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.location-status i {
    font-size: 1rem;
}
.location-status.loading i {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.location-coords {
    margin-top: 10px;
    padding: 10px;
    background: var(--primary-900);
    border-radius: 8px;
    font-family: monospace;
    font-size: 0.75rem;
    color: var(--primary-200);
}

/* Nearest Clinic Badge - Top Center */
.nearest-badge {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    background: var(--gradient-primary);
    color: white;
    padding: 12px 25px;
    border-radius: 50px;
    box-shadow: 0 10px 40px rgba(30, 64, 175, 0.4);
    animation: slideInDown 0.5s ease;
    display: none;
    text-align: center;
}
.nearest-badge.show {
    display: flex;
    align-items: center;
    gap: 15px;
}
.nearest-badge .icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.nearest-badge .icon i {
    font-size: 1.2rem;
}
.nearest-badge .info h6 {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.9;
    margin-bottom: 2px;
}
.nearest-badge .info .clinic-name {
    font-weight: 600;
    font-size: 0.95rem;
}

/* Route Panel - Right Side, Compact */
.route-panel {
    position: absolute;
    top: 20px;
    right: 20px;
    bottom: 20px;
    z-index: 1000;
    background: rgba(255,255,255,0.98);
    backdrop-filter: blur(20px);
    width: 320px;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(30, 64, 175, 0.2);
    display: none;
    flex-direction: column;
    border: 1px solid var(--primary-100);
    animation: slideInRight 0.4s ease;
    overflow: hidden;
}
.route-panel.show {
    display: flex;
}
.route-panel-header {
    padding: 18px 20px;
    background: var(--gradient-primary);
    color: white;
}
.route-panel-header .header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.route-panel-header h6 {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.9;
    margin: 0;
}
.route-panel-header .close-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: all 0.3s;
}
.route-panel-header .close-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}
.route-panel-header .destination {
    font-weight: 600;
    font-size: 1.1rem;
}

.route-summary {
    display: flex;
    gap: 10px;
    padding: 15px 20px;
    background: var(--primary-50);
    border-bottom: 1px solid var(--primary-100);
}
.route-summary-item {
    flex: 1;
    text-align: center;
    padding: 12px 8px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(30, 64, 175, 0.08);
}
.route-summary-item i {
    font-size: 1.3rem;
    color: var(--primary-500);
    margin-bottom: 5px;
    display: block;
}
.route-summary-item .value {
    font-weight: 700;
    color: var(--primary-800);
    font-size: 1rem;
}
.route-summary-item .label {
    font-size: 0.65rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.route-steps-container {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
}
.route-steps-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--primary-600);
    font-weight: 600;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.route-step {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: white;
    border-radius: 10px;
    margin-bottom: 8px;
    border: 1px solid var(--primary-100);
    transition: all 0.3s;
}
.route-step:hover {
    border-color: var(--primary-300);
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.1);
}
.route-step .step-icon {
    width: 32px;
    height: 32px;
    background: var(--gradient-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.route-step .step-text {
    font-size: 0.85rem;
    color: #334155;
    line-height: 1.4;
}
.route-step .step-distance {
    font-size: 0.7rem;
    color: #94a3b8;
    margin-top: 4px;
}

/* Cards Section */
.cards-section {
    background: linear-gradient(180deg, var(--primary-100) 0%, var(--primary-50) 50%, white 100%);
    padding: 50px 0 70px;
    position: relative;
}
.cards-section::before {
    content: '';
    position: absolute;
    top: -40px;
    left: 0;
    right: 0;
    height: 40px;
    background: linear-gradient(180deg, transparent, var(--primary-100));
}

.section-header {
    text-align: center;
    margin-bottom: 40px;
    animation: fadeInUp 0.6s ease;
}
.section-header h3 {
    font-weight: 700;
    color: var(--primary-800);
    margin-bottom: 10px;
    font-size: 1.8rem;
}
.section-header h3 i {
    color: var(--primary-500);
}
.section-header p {
    color: #64748b;
    font-size: 1rem;
}

/* Clinic Cards */
.clinic-card { 
    cursor: pointer; 
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
    border-radius: 20px; 
    padding: 25px; 
    background: white;
    border: 2px solid var(--primary-100);
    box-shadow: 0 4px 20px rgba(30, 64, 175, 0.06);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}
.clinic-card:nth-child(1) { animation-delay: 0.1s; }
.clinic-card:nth-child(2) { animation-delay: 0.2s; }
.clinic-card:nth-child(3) { animation-delay: 0.3s; }
.clinic-card:nth-child(4) { animation-delay: 0.4s; }

.clinic-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
}

.clinic-card:hover { 
    transform: translateY(-10px); 
    box-shadow: 0 20px 50px rgba(30, 64, 175, 0.15);
    border-color: var(--primary-300);
}
.clinic-card.active {
    border-color: var(--primary-500);
    box-shadow: 0 15px 40px rgba(30, 64, 175, 0.2);
}
.clinic-card.nearest {
    border-color: #10b981;
    background: linear-gradient(135deg, white 0%, #ecfdf5 100%);
}
.clinic-card.nearest::after {
    content: '⭐ NEAREST';
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-size: 0.65rem;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 20px;
}

.clinic-card .card-header-section {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 18px;
}
.clinic-card .icon-wrapper {
    width: 65px;
    height: 85px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--gradient-light);
    color: white;
    font-size: 1.4rem;
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.clinic-card .card-info h5 {
    font-weight: 600;
    color: var(--primary-800);
    margin-bottom: 5px;
    font-size: 1rem;
}
.clinic-card .card-info .location {
    color: #64748b;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 5px;
}
.clinic-card .card-info .location i {
    color: #ef4444;
    font-size: 0.85rem;
}

.clinic-card .card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 12px 15px;
    background: var(--primary-50);
    border-radius: 12px;
    border: 1px solid var(--primary-100);
}
.clinic-card .rating {
    color: #fbbf24;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 5px;
}
.clinic-card .rating span {
    color: var(--primary-700);
    font-weight: 600;
}
.clinic-card .distance-badge {
    font-size: 0.8rem;
    color: var(--primary-600);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
    background: white;
    padding: 5px 10px;
    border-radius: 20px;
    border: 1px solid var(--primary-200);
}
.clinic-card .distance-badge i {
    color: var(--primary-500);
}

.clinic-card .status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status.open { 
    background: #ecfdf5; 
    color: #059669;
    border: 1px solid #a7f3d0;
}
.status.closed { 
    background: #fef2f2; 
    color: #dc2626;
    border: 1px solid #fecaca;
}

.clinic-card .card-actions {
    display: flex;
    gap: 10px;
    margin-top: 18px;
}
.btn-view-map {
    flex: 1;
    padding: 11px 15px;
    border: 2px solid var(--primary-200);
    background: white;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--primary-600);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    cursor: pointer;
}
.btn-view-map:hover {
    border-color: var(--primary-500);
    background: var(--primary-50);
    color: var(--primary-700);
}
.btn-directions {
    flex: 1;
    padding: 11px 15px;
    border: none;
    background: var(--gradient-primary);
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    cursor: pointer;
}
.btn-directions:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
}
.btn-get-started {
    width: 100%;
    padding: 13px 20px;
    border: none;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    cursor: pointer;
    margin-top: 12px;
}
.btn-get-started:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slideInDown {
    from { opacity: 0; transform: translate(-50%, -30px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}
@keyframes pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
    50% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

/* Custom Leaflet Styles */
.leaflet-container {
    background: var(--primary-900);
}
.custom-marker {
    background: none;
    border: none;
}
.marker-pin {
    width: 44px;
    height: 44px;
    border-radius: 50% 50% 50% 0;
    background: var(--gradient-primary);
    position: relative;
    transform: rotate(-45deg);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 20px rgba(30, 64, 175, 0.4);
    border: 3px solid white;
    animation: bounce 2s ease infinite;
}
.marker-pin i {
    transform: rotate(45deg);
    color: white;
    font-size: 16px;
}
.marker-pin.user-marker {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    transform: rotate(0);
    animation: pulse 2s ease infinite;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
}
.marker-pin.user-marker i {
    transform: rotate(0);
    font-size: 14px;
}
.marker-pin.active {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    animation: pulse 1s ease infinite;
    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
}

/* Pet Form Section */
#petFormSection { 
    display: none; 
    background: white;
    border-radius: 24px; 
    padding: 50px; 
    margin: 40px auto;
    max-width: 800px;
    box-shadow: 0 15px 50px rgba(30, 64, 175, 0.1);
    border: 1px solid var(--primary-100);
    animation: fadeInUp 0.5s ease;
}
.form-header {
    text-align: center;
    margin-bottom: 40px;
}
.form-header .icon-circle {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
}
.form-header .icon-circle i {
    font-size: 35px;
    color: white;
}
.form-header h4 {
    font-weight: 700;
    color: var(--primary-800);
}
.form-header p {
    color: #64748b;
}

.form-label { 
    font-size: 0.8rem; 
    text-transform: uppercase; 
    font-weight: 600; 
    margin-bottom: 10px; 
    color: var(--primary-700);
    letter-spacing: 0.5px;
}
.form-control, .form-select { 
    border: 2px solid var(--primary-200); 
    border-radius: 12px; 
    padding: 14px 18px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: white;
}
.form-control:focus, .form-select:focus { 
    border-color: var(--primary-500); 
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); 
    outline: none; 
}
.form-control::placeholder {
    color: #94a3b8;
}
.btn-submit { 
    background: var(--gradient-primary);
    border: none; 
    padding: 16px 50px; 
    border-radius: 12px; 
    font-weight: 600; 
    font-size: 1rem;
    transition: all 0.3s; 
    color: white; 
}
.btn-submit:hover { 
    transform: translateY(-3px);
    box-shadow: 0 10px 35px rgba(37, 99, 235, 0.4);
    color: white;
}
.btn-back {
    padding: 10px 20px;
    border: 2px solid var(--primary-200);
    border-radius: 10px;
    background: white;
    color: var(--primary-600);
    font-weight: 600;
    transition: all 0.3s;
}
.btn-back:hover {
    border-color: var(--primary-500);
    background: var(--primary-50);
}

/* Hide default routing container */
.leaflet-routing-container {
    display: none !important;
}

/* Responsive */
@media (max-width: 992px) {
    .route-panel {
        top: auto;
        right: 10px;
        bottom: 10px;
        left: 10px;
        width: auto;
        max-height: 50%;
    }
    .map-overlay {
        width: 280px;
        padding: 15px;
    }
    .nearest-badge {
        top: auto;
        bottom: 10px;
        padding: 10px 20px;
    }
    .nearest-badge.show {
        flex-direction: column;
        gap: 8px;
    }
}
@media (max-width: 768px) {
    .map-section { height: 50vh; }
    .map-overlay { 
        top: 10px; 
        left: 10px; 
        right: 10px;
        width: auto;
    }
    .location-coords { display: none; }
    #petFormSection { 
        padding: 30px 20px; 
    }
}
</style>
</head>
<body>
<?php include 'userSidebar.php'; ?>
<div class="main-content">


<div class="main-container">
    <!-- Map Section -->
    <section class="map-section">
        <div id="map"></div>
        
        <!-- Map Overlay Info - Left Side -->
        <div class="map-overlay">
            <h5><i class="bi bi-geo-alt-fill"></i> Find Nearby Clinics</h5>
            <p>Veterinary clinics in Dasmariñas, Cavite</p>
            
            <!-- Real-time Search Input -->
            <div class="search-wrapper mt-3 mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-primary"></i></span>
                    <input type="text" id="clinicSearch" class="form-control border-start-0 ps-0" placeholder="Search clinic name or address..." autocomplete="off">
                </div>
            </div>

            <div id="locationStatus" class="location-status loading">
                <i class="bi bi-arrow-repeat"></i>
                <span>Detecting your location...</span>
            </div>
            <div id="locationCoords" class="location-coords" style="display: none;">
                Lat: <span id="userLat">-</span><br>
                Lng: <span id="userLng">-</span>
            </div>
        </div>

        <!-- Nearest Clinic Badge - Top Center -->
        <div id="nearestBadge" class="nearest-badge">
            <div class="icon">
                <i class="bi bi-lightning-fill"></i>
            </div>
            <div class="info">
                <h6>Nearest Clinic</h6>
                <div class="clinic-name" id="nearestClinicName">-</div>
            </div>
        </div>

        <!-- Route Panel - Right Side -->
        <div id="routePanel" class="route-panel">
            <div class="route-panel-header">
                <div class="header-top">
                    <h6><i class="bi bi-signpost-2 me-1"></i> Directions</h6>
                    <button class="close-btn" onclick="clearRoute()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="destination" id="routeDestination">-</div>
            </div>
            
            <div class="route-summary">
                <div class="route-summary-item">
                    <i class="bi bi-signpost-split"></i>
                    <div class="value" id="routeDistance">-</div>
                    <div class="label">Distance</div>
                </div>
                <div class="route-summary-item">
                    <i class="bi bi-clock"></i>
                    <div class="value" id="routeTime">-</div>
                    <div class="label">Est. Time</div>
                </div>
            </div>
            
            <div class="route-steps-container">
                <div class="route-steps-title">
                    <i class="bi bi-list-ol"></i> Turn-by-Turn
                </div>
                <div id="routeSteps">
                    <!-- Steps will be populated dynamically -->
                </div>
            </div>
        </div>
    </section>

    <!-- Clinic Cards Section -->
    <section class="cards-section" id="cardsSection">
        <div class="container">
            <div class="section-header">
                <h3><i class="bi bi-hospital me-2"></i>Partner Veterinary Clinics</h3>
                <p>Select a clinic to view directions or register your pet</p>
            </div>

            <div class="row g-4" id="clinicCards">
                <!-- Cards will be populated dynamically -->
            </div>
        </div>
    </section>

    <!-- Pet Form Section -->
    <div class="container">
        <section id="petFormSection">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-back" onclick="goBack()">
                    <i class="bi bi-arrow-left me-2"></i>Back to Clinics
                </button>
                <span class="badge text-dark px-3 py-2" style="background: var(--primary-100); border: 1px solid var(--primary-200);">
                    <i class="bi bi-building me-1"></i>
                    <span id="selectedClinicBadge"></span>
                </span>
            </div>
            
            <div class="form-header">
                <div class="icon-circle">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <h4>Pet Information Form</h4>
                <p>Fill in your pet's details to complete the registration</p>
            </div>

            <form method="POST" class="row g-4">
                <input type="hidden" name="clinic" id="clinicNameInput">
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-tag me-1"></i>Pet Name</label>
                    <input type="text" class="form-control" name="name" placeholder="What's your pet's name?" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-list me-1"></i>Pet Type</label>
                    <select class="form-select" name="type" required>
                        <option value="">Select Species</option>
                        <option>🐕 Dog</option>
                        <option>🐈 Cat</option>
                        <option>🐦 Bird</option>
                        <option>🐰 Rabbit</option>
                        <option>🦎 Exotic</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-bookmark me-1"></i>Breed</label>
                    <input type="text" class="form-control" name="breed" placeholder="e.g. Shih Tzu, Persian, Labrador">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="bi bi-calendar3 me-1"></i>Age</label>
                    <input type="text" class="form-control" name="age" placeholder="e.g. 2 years, 6 months">
                </div>
                <div class="col-12">
                    <label class="form-label"><i class="bi bi-chat-text me-1"></i>Concern / Reason for Visit</label>
                    <textarea class="form-control" name="concern" rows="4" placeholder="Describe any symptoms, behavior changes, or the purpose of your visit..."></textarea>
                </div>
                <div class="col-12 mt-4 text-center">
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle me-2"></i>Submit Registration
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Clinic Data - All in Dasmariñas, Cavite
// Clinic Data fetched from database
const clinics = <?php echo json_encode($clinics_data); ?>;

let map;
let markers = {};
let userMarker = null;
let userLocation = null;
let routingControl = null;
let activeClinicId = null;
let watchId = null;

// Initialize the app
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    renderClinicCards();
    getUserLocation();
    
    // Add Real-time Search Listener
    const searchInput = document.getElementById('clinicSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            filterClinics(e.target.value);
        });
    }
});

// Real-time Filtering Logic
function filterClinics(query) {
    const searchTerm = query.toLowerCase().trim();
    
    // 1. Filter Data
    const filteredClinics = clinics.filter(clinic => {
        return clinic.name.toLowerCase().includes(searchTerm) || 
               clinic.address.toLowerCase().includes(searchTerm);
    });
    
    // 2. Update Map Markers
    clinics.forEach(clinic => {
        const marker = markers[clinic.id];
        if (marker) {
            if (filteredClinics.some(c => c.id === clinic.id)) {
                if (!map.hasLayer(marker)) marker.addTo(map);
            } else {
                if (map.hasLayer(marker)) map.removeLayer(marker);
            }
        }
    });
    
    // 3. Update Cards
    renderClinicCards(filteredClinics);
    
    // 4. Update Nearest Badge (only if we have location and results)
    updateNearestBadge(filteredClinics);
}

function updateNearestBadge(filteredList = clinics) {
    if (!userLocation || filteredList.length === 0) {
        document.getElementById('nearestBadge').classList.remove('show');
        return;
    }
    
    // Find nearest from the (possibly filtered) list
    let nearest = null;
    let minDistance = Infinity;
    
    filteredList.forEach(clinic => {
        if (clinic.distance < minDistance) {
            minDistance = clinic.distance;
            nearest = clinic;
        }
    });
    
    if (nearest) {
        document.getElementById('nearestClinicName').textContent = 
            `${nearest.name} • ${formatDistance(minDistance)}`;
        document.getElementById('nearestBadge').classList.add('show');
    } else {
        document.getElementById('nearestBadge').classList.remove('show');
    }
}

// Initialize Leaflet Map
function initMap() {
    map = L.map('map', {
        center: [14.3272, 120.9367],
        zoom: 14,
        zoomControl: false
    });

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Blue-tinted map tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '©OpenStreetMap, ©CartoDB',
        maxZoom: 19
    }).addTo(map);

    // Add clinic markers
    markers = {}; // Initialize as object for easy lookup by clinic ID
    clinics.forEach((clinic) => {
        const marker = createClinicMarker(clinic);
        markers[clinic.id] = marker; // Key markers by clinic ID
    });

    // Fit bounds
    const markerArray = Object.values(markers);
    const group = L.featureGroup(markerArray);
    map.fitBounds(group.getBounds().pad(0.1));
}

// Create custom clinic marker
function createClinicMarker(clinic) {
    const markerHtml = `
        <div class="marker-pin" id="marker-${clinic.id}">
            <i class="bi ${clinic.icon}"></i>
        </div>
    `;

    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: markerHtml,
        iconSize: [44, 44],
        iconAnchor: [22, 44],
        popupAnchor: [0, -44]
    });

    const marker = L.marker([clinic.lat, clinic.lng], { icon: customIcon }).addTo(map);
    
    const popupContent = `
        <div style="padding: 15px; min-width: 200px;">
            <h6 style="font-weight: 600; margin-bottom: 8px; color: #1e40af;">${clinic.name}</h6>
            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 6px;">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>${clinic.location}
            </p>
            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 6px;">
                <i class="bi bi-clock me-1"></i>${clinic.hours}
            </p>
            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">
                <i class="bi bi-star-fill text-warning me-1"></i>${clinic.rating} (${clinic.reviews} reviews)
            </p>
            <button onclick="getDirections(${clinic.id})" style="width: 100%; padding: 10px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                <i class="bi bi-signpost-split me-1"></i> Get Directions
            </button>
        </div>
    `;
    
    marker.bindPopup(popupContent);
    
    marker.on('click', () => {
        highlightClinicCard(clinic.id);
    });

    return marker;
}

// Get user location with high accuracy
function getUserLocation() {
    const statusEl = document.getElementById('locationStatus');
    
    if (!navigator.geolocation) {
        setDefaultLocation();
        statusEl.className = 'location-status error';
        statusEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i><span>Geolocation not supported</span>';
        return;
    }

    // Options for high accuracy
    const options = {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0
    };

    // Watch position for real-time updates
    watchId = navigator.geolocation.watchPosition(
        (position) => {
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy
            };
            
            // Update coordinates display
            document.getElementById('userLat').textContent = userLocation.lat.toFixed(6);
            document.getElementById('userLng').textContent = userLocation.lng.toFixed(6);
            document.getElementById('locationCoords').style.display = 'block';
            
            // Update or add user marker
            if (userMarker) {
                userMarker.setLatLng([userLocation.lat, userLocation.lng]);
            } else {
                addUserMarker();
            }
            
            // Calculate distances
            calculateDistances();
            
            // Update status
            const accuracyText = userLocation.accuracy < 50 ? 'High accuracy' : 
                                 userLocation.accuracy < 100 ? 'Medium accuracy' : 'Low accuracy';
            statusEl.className = 'location-status success';
            statusEl.innerHTML = `<i class="bi bi-check-circle-fill"></i><span>Location found! (${accuracyText})</span>`;
        },
        (error) => {
            console.log('Geolocation error:', error.message);
            
            let errorMessage = 'Location unavailable';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Location permission denied';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Position unavailable';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Location request timed out';
                    break;
            }
            
            statusEl.className = 'location-status error';
            statusEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i><span>${errorMessage}</span>`;
            
            // Try to get cached position or use default
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userLocation = {
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude,
                        accuracy: pos.coords.accuracy
                    };
                    addUserMarker();
                    calculateDistances();
                    statusEl.className = 'location-status';
                    statusEl.innerHTML = '<i class="bi bi-info-circle"></i><span>Using cached location</span>';
                },
                () => {
                    setDefaultLocation();
                },
                { enableHighAccuracy: false, timeout: 5000, maximumAge: 300000 }
            );
        },
        options
    );
}

// Set default location in Dasmariñas
function setDefaultLocation() {
    userLocation = { lat: 14.3250, lng: 120.9400, accuracy: 1000 };
    addUserMarker();
    calculateDistances();
    
    document.getElementById('userLat').textContent = userLocation.lat.toFixed(6);
    document.getElementById('userLng').textContent = userLocation.lng.toFixed(6);
    document.getElementById('locationCoords').style.display = 'block';
    
    const statusEl = document.getElementById('locationStatus');
    statusEl.className = 'location-status';
    statusEl.innerHTML = '<i class="bi bi-info-circle"></i><span>Using default location</span>';
}

// Add user marker to map
function addUserMarker() {
    if (userMarker) {
        map.removeLayer(userMarker);
    }

    const userIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div class="marker-pin user-marker"><i class="bi bi-person-fill"></i></div>',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    userMarker = L.marker([userLocation.lat, userLocation.lng], { icon: userIcon }).addTo(map);
    
    const accuracyCircle = L.circle([userLocation.lat, userLocation.lng], {
        radius: userLocation.accuracy || 50,
        color: '#10b981',
        fillColor: '#10b981',
        fillOpacity: 0.1,
        weight: 2
    }).addTo(map);

    userMarker.bindPopup(`
        <div style="padding: 12px; text-align: center;">
            <strong style="color: #059669;">📍 Your Location</strong>
            <p style="margin: 8px 0 0; font-size: 0.8rem; color: #64748b;">
                Accuracy: ±${Math.round(userLocation.accuracy || 50)}m
            </p>
        </div>
    `);
    
    // Fit bounds to include user
    const allMarkers = [...markers, userMarker];
    const group = L.featureGroup(allMarkers);
    map.fitBounds(group.getBounds().pad(0.1));
}

// Calculate distance (Haversine formula)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function deg2rad(deg) {
    return deg * (Math.PI/180);
}

// Calculate distances and update UI
function calculateDistances() {
    let nearestClinic = null;
    let minDistance = Infinity;

    clinics.forEach(clinic => {
        const distance = calculateDistance(
            userLocation.lat, userLocation.lng,
            clinic.lat, clinic.lng
        );
        clinic.distance = distance;

        if (distance < minDistance) {
            minDistance = distance;
            nearestClinic = clinic;
        }
    });

    clinics.sort((a, b) => a.distance - b.distance);

    const currentSearch = document.getElementById('clinicSearch')?.value || '';
    filterClinics(currentSearch);
}

// Format distance
function formatDistance(distance) {
    if (distance < 1) {
        return Math.round(distance * 1000) + ' m';
    }
    return distance.toFixed(1) + ' km';
}

// Render clinic cards
function renderClinicCards(listToRender = clinics) {
    const container = document.getElementById('clinicCards');
    container.innerHTML = '';

    if (listToRender.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 border shadow-sm">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">No clinics found</h4>
                    <p class="text-muted">Try adjusting your search terms</p>
                </div>
            </div>
        `;
        return;
    }

    listToRender.forEach((clinic, index) => {
        const isNearest = index === 0 && clinic.distance && clinics[0].id === clinic.id;
        const card = document.createElement('div');
        card.className = 'col-lg-3 col-md-6';
        card.innerHTML = `
            <div class="clinic-card ${isNearest ? 'nearest' : ''}" id="card-${clinic.id}">
                <div class="card-header-section">
                    <div class="icon-wrapper">
                        <i class="bi ${clinic.icon}"></i>
                    </div>
                    <div class="card-info">
                        <h5>${clinic.name}</h5>
                        <div class="location">
                            <i class="bi bi-geo-alt-fill"></i>
                            ${clinic.location}
                        </div>
                    </div>
                </div>
                
                <div class="card-meta">
                    <div class="rating">
                        <i class="bi bi-star-fill"></i>
                        <span>${clinic.rating}</span>
                        <small class="text-muted">(${clinic.reviews})</small>
                    </div>
                    ${clinic.distance ? `
                        <div class="distance-badge">
                            <i class="bi bi-signpost-split"></i>
                            ${formatDistance(clinic.distance)}
                        </div>
                    ` : ''}
                </div>
                
                <span class="status ${clinic.status}">
                    <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                    ${clinic.status === 'open' ? 'Open Now' : 'Closed'}
                </span>
                
                <div class="card-actions">
                    <button class="btn-view-map" onclick="viewOnMap(${clinic.id})">
                        <i class="bi bi-map"></i>
                        View
                    </button>
                    <button class="btn-directions" onclick="getDirections(${clinic.id})">
                        <i class="bi bi-signpost-split"></i>
                        Directions
                    </button>
                </div>
                
                <button class="btn-get-started" onclick="selectClinic(${clinic.id})">
                    <i class="bi bi-rocket-takeoff"></i>
                    Get Started
                </button>
            </div>
        `;
        container.appendChild(card);
    });
}

// View clinic on map
function viewOnMap(clinicId) {
    const clinic = clinics.find(c => c.id === clinicId);
    if (clinic) {
        map.setView([clinic.lat, clinic.lng], 17, { animate: true });
        markers[clinicId].openPopup();
        highlightClinicCard(clinicId);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Highlight clinic card
function highlightClinicCard(clinicId) {
    document.querySelectorAll('.clinic-card').forEach(card => {
        card.classList.remove('active');
    });
    
    const card = document.getElementById(`card-${clinicId}`);
    if (card) card.classList.add('active');
    
    document.querySelectorAll('.marker-pin').forEach(pin => {
        pin.classList.remove('active');
    });
    const markerPin = document.getElementById(`marker-${clinicId}`);
    if (markerPin) markerPin.classList.add('active');
    
    activeClinicId = clinicId;
}

// Get directions
function getDirections(clinicId) {
    if (!userLocation) {
        alert('Please wait for your location to be detected.');
        return;
    }

    const clinic = clinics.find(c => c.id === clinicId);
    if (!clinic) return;

    if (routingControl) {
        map.removeControl(routingControl);
    }

    highlightClinicCard(clinicId);

    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(userLocation.lat, userLocation.lng),
            L.latLng(clinic.lat, clinic.lng)
        ],
        routeWhileDragging: false,
        showAlternatives: false,
        addWaypoints: false,
        draggableWaypoints: false,
        fitSelectedRoutes: true,
        lineOptions: {
            styles: [
                { color: '#1e40af', opacity: 0.9, weight: 6 },
                { color: '#3b82f6', opacity: 0.5, weight: 12 }
            ]
        },
        createMarker: function() { return null; }
    }).addTo(map);

    routingControl.on('routesfound', function(e) {
        const route = e.routes[0];
        const summary = route.summary;
        
        document.getElementById('routeDestination').textContent = clinic.name;
        document.getElementById('routeDistance').textContent = formatDistance(summary.totalDistance / 1000);
        document.getElementById('routeTime').textContent = formatTime(summary.totalTime);
        
        const stepsContainer = document.getElementById('routeSteps');
        stepsContainer.innerHTML = '';
        
        route.instructions.forEach((instruction) => {
            if (instruction.text) {
                const stepEl = document.createElement('div');
                stepEl.className = 'route-step';
                stepEl.innerHTML = `
                    <div class="step-icon">
                        <i class="bi ${getDirectionIcon(instruction.type)}"></i>
                    </div>
                    <div>
                        <div class="step-text">${instruction.text}</div>
                        ${instruction.distance > 0 ? `<div class="step-distance">${formatDistance(instruction.distance / 1000)}</div>` : ''}
                    </div>
                `;
                stepsContainer.appendChild(stepEl);
            }
        });
        
        document.getElementById('routePanel').classList.add('show');
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Format time
function formatTime(seconds) {
    if (seconds < 60) return seconds + ' sec';
    if (seconds < 3600) return Math.round(seconds / 60) + ' min';
    const hours = Math.floor(seconds / 3600);
    const mins = Math.round((seconds % 3600) / 60);
    return hours + 'h ' + mins + 'm';
}

// Get direction icon
function getDirectionIcon(type) {
    const icons = {
        'Head': 'bi-arrow-up',
        'Continue': 'bi-arrow-up',
        'SlightRight': 'bi-arrow-up-right',
        'Right': 'bi-arrow-right',
        'SharpRight': 'bi-arrow-right',
        'TurnAround': 'bi-arrow-return-left',
        'SharpLeft': 'bi-arrow-left',
        'Left': 'bi-arrow-left',
        'SlightLeft': 'bi-arrow-up-left',
        'WaypointReached': 'bi-geo-alt',
        'Roundabout': 'bi-arrow-clockwise',
        'DestinationReached': 'bi-flag-fill',
        'Fork': 'bi-signpost-2'
    };
    return icons[type] || 'bi-arrow-right';
}

// Clear route
function clearRoute() {
    if (routingControl) {
        map.removeControl(routingControl);
        routingControl = null;
    }
    document.getElementById('routePanel').classList.remove('show');
    
    const markerArray = Object.values(markers);
    const allMarkers = userMarker ? [...markerArray, userMarker] : markerArray;
    const group = L.featureGroup(allMarkers);
    map.fitBounds(group.getBounds().pad(0.1));
    
    document.querySelectorAll('.marker-pin').forEach(pin => {
        pin.classList.remove('active');
    });
}

// Select clinic
function selectClinic(clinicId) {
    const clinic = clinics.find(c => c.id === clinicId);
    if (!clinic) return;

    document.getElementById('clinicNameInput').value = clinic.name;
    document.getElementById('selectedClinicBadge').textContent = clinic.name;
    
    document.querySelector('.map-section').style.display = 'none';
    document.getElementById('cardsSection').style.display = 'none';
    document.getElementById('petFormSection').style.display = 'block';
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Go back
function goBack() {
    document.querySelector('.map-section').style.display = 'block';
    document.getElementById('cardsSection').style.display = 'block';
    document.getElementById('petFormSection').style.display = 'none';
    
    setTimeout(() => { map.invalidateSize(); }, 100);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (watchId) navigator.geolocation.clearWatch(watchId);
});
</script>

</div> <!-- Closing main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
