<?php
session_start();
require_once "config/db.php";

$user = $_SESSION['username'] ?? 'Guest';

$pets = [];
$completed = 0;
$pending = 0;

$sql = "SELECT * FROM pets WHERE user = ? ORDER BY created_at DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
    if ($row['status'] === 'completed') $completed++;
    if ($row['status'] === 'pending') $pending++;
}

header('Content-Type: application/json');
echo json_encode([
    'pets' => $pets,
    'total' => count($pets),
    'completed' => $completed,
    'pending' => $pending
]);
exit();