<?php
require_once "config/db.php";

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = $_POST['pet_name'] ?? '';
    $type   = $_POST['pet_type'] ?? '';
    $breed  = $_POST['breed'] ?? '';
    $age    = $_POST['age'] ?? 0;
    $clinic = $_POST['clinic'] ?? '';
    $status = 'pending'; // default status

    // Simple validation
    if($name && $type && $clinic){
        $stmt = $mysqli->prepare("INSERT INTO pets (name, type, breed, age, clinic, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssiss", $name, $type, $breed, $age, $clinic, $status);
        if($stmt->execute()){
            header("Location: mypets.php"); // redirect to mypets.php after registration
            exit;
        } else {
            die("Error saving pet: " . $stmt->error);
        }
    } else {
        die("Please fill in all required fields.");
    }
} else {
    die("Invalid request method.");
}
?>
