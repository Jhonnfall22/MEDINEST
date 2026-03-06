<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php";

$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';

$pets = [];
$completed = 0;
$pending = 0;

if ($user_id) {
    // Check for pets by user_id link (if available), email/username, or full name
    $full_name = $_SESSION['full_name'] ?? '';
    $email = $_SESSION['email'] ?? '';
    
    $sql = "SELECT p.* FROM pets p 
            WHERE p.user = ? 
            OR p.user = ? 
            OR p.user = ? 
            OR p.user = (SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = ?)
            OR p.user = (SELECT email FROM users WHERE id = ?)
            OR p.user = 'Guest'
            ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $username, $email, $full_name, $user_id, $user_id);
} else {
    $sql = "SELECT * FROM pets WHERE user = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
    $status = strtolower($row['status']);
    if ($status === 'completed' || $status === 'approved') $completed++;
    if ($status === 'pending') $pending++;
}

header('Content-Type: application/json');
echo json_encode([
    'pets' => $pets,
    'total' => count($pets),
    'completed' => $completed,
    'pending' => $pending
]);
exit();