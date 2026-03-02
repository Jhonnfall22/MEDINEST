<?php
session_start();
require_once "config/db.php"; // Database connection

$user = $_SESSION['username'] ?? 'Guest';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic  = $_POST['clinic'] ?? '';
    $name    = $_POST['name'] ?? '';
    $type    = $_POST['type'] ?? '';
    $breed   = $_POST['breed'] ?? '';
    $age     = $_POST['age'] ?? 0;
    $concern = $_POST['concern'] ?? '';
    $status  = 'pending'; // default status

    // Insert into pets table with user
    $sql = "INSERT INTO pets (user, name, type, breed, age, concern, clinic, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssisss", $user, $name, $type, $breed, $age, $concern, $clinic, $status);
        $stmt->execute();
        $stmt->close();

        // Redirect to mypets.php after registration
        header("Location: mypets.php");
        exit();
    } else {
        die("Database error: " . $mysqli->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Pet | VetClinic</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root { --admin-primary: #220bef; --border-color: #e0e0e0; }
body { font-family: 'Poppins', sans-serif; background: #f4f7fa; color: #333; }
.navbar-brand span { color: var(--admin-primary); font-weight: 700; }
.clinic-card { cursor: pointer; transition: all 0.3s ease; border-radius: 12px; padding: 30px 20px; background: #fff; text-align: center; border: 1px solid var(--border-color); }
.clinic-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-color: var(--admin-primary); }
.clinic-card.selected { border: 2px solid var(--admin-primary); background-color: rgba(34, 11, 239, 0.02); }
#petFormSection { display: none; background: #fff; border: 1px solid var(--border-color); border-radius: 16px; padding: 40px; margin-top: 20px; }
.form-label { font-size: 0.85rem; text-transform: uppercase; font-weight: 600; margin-bottom: 8px; color: #555; }
.form-control, .form-select { border: 1px solid var(--border-color); border-radius: 10px; padding: 12px 15px; }
.form-control:focus, .form-select:focus { border-color: var(--admin-primary); box-shadow: 0 0 0 4px rgba(34, 11, 239, 0.1); outline: none; }
.btn-submit { background-color: var(--admin-primary); border: none; padding: 12px 40px; border-radius: 10px; font-weight: 600; transition: opacity 0.2s; color: white; }
.btn-submit:hover { opacity: 0.9; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm bg-white py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Vet<span>clinic</span></a>
        <div class="ms-auto">
            <a href="user_home.php" class="btn btn-sm btn-light border fw-medium me-2">Home</a>
            <a href="mypets.php" class="btn btn-sm btn-primary fw-medium" style="background-color: var(--admin-primary); border:none;">My Pets</a>
        </div>
    </div>
</nav>

<section class="pt-5 mt-5 pb-3 text-center">
    <h2>Choose Your Clinic</h2>
    <p class="text-muted">Select a partner clinic to start your pet's consultation.</p>
</section>

<div class="container pb-5">
    <div id="clinicSelection" class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="clinic-card" onclick="selectClinic('PawCare Vet Clinic', this)">
                <i class="bi bi-hospital text-success" style="font-size: 45px;"></i>
                <h5>PawCare Vet Clinic</h5>
                <p><i class="bi bi-geo-alt me-1"></i> Dasmariñas City</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="clinic-card" onclick="selectClinic('Happy Tails Veterinary', this)">
                <i class="bi bi-hospital text-primary" style="font-size: 45px;"></i>
                <h5>Happy Tails Veterinary</h5>
                <p><i class="bi bi-geo-alt me-1"></i> Bacoor City</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="clinic-card" onclick="selectClinic('AnimalCare Center', this)">
                <i class="bi bi-hospital text-danger" style="font-size: 45px;"></i>
                <h5>AnimalCare Center</h5>
                <p><i class="bi bi-geo-alt me-1"></i> Imus City</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <section id="petFormSection">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h4 class="fw-bold mb-1">Pet Information Form</h4>
                    <button class="btn btn-sm btn-light border" onclick="location.reload()"><i class="bi bi-arrow-left me-1"></i> Change</button>
                </div>

                <form method="POST" class="row g-3">
                    <input type="hidden" name="clinic" id="clinicNameInput">
                    <div class="col-md-6">
                        <label class="form-label">Pet Name</label>
                        <input type="text" class="form-control" name="name" placeholder="What's your pet's name?" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pet Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Species</option>
                            <option>Dog</option>
                            <option>Cat</option>
                            <option>Bird</option>
                            <option>Exotic</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Breed</label>
                        <input type="text" class="form-control" name="breed" placeholder="e.g. Shih Tzu, Persian">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Age (Approximate)</label>
                        <input type="number" class="form-control" name="age" placeholder="How old is your pet?">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Concern / Reason for Visit</label>
                        <textarea class="form-control" name="concern" rows="4" placeholder="Describe any symptoms or the purpose of your visit..."></textarea>
                    </div>
                    <div class="col-12 mt-4 text-center">
                        <button type="submit" class="btn btn-submit">Submit Registration</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<script>
function selectClinic(clinicName, element) {
    document.querySelectorAll('.clinic-card').forEach(card => card.classList.remove('selected'));
    element.classList.add('selected');
    document.getElementById('clinicNameInput').value = clinicName;
    document.getElementById('clinicSelection').classList.add('d-none');
    document.getElementById('petFormSection').style.display = 'block';
    window.scrollTo({ top: 100, behavior: 'smooth' });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>