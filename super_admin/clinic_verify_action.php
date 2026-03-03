<?php
require_once '../config/db.php';

// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['clinic_id'], $_POST['action'])) {
        die("Invalid request.");
    }

    $clinic_id = intval($_POST['clinic_id']);
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['approve', 'reject'])) {
        die("Invalid action.");
    }

    if ($action === 'approve') {

        $stmt = $conn->prepare("
            UPDATE clinics 
            SET status = 'approved',
                approved_date = NOW(),
                rejected_date = NULL
            WHERE id = ?
        ");

    } else { // reject

        $stmt = $conn->prepare("
            UPDATE clinics 
            SET status = 'rejected',
                rejected_date = NOW(),
                approved_date = NULL
            WHERE id = ?
        ");
    }

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $clinic_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();

        // Redirect back to clinic page
        header("Location: clinic.php?success=1");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

} else {
    die("Invalid access.");
}
?>