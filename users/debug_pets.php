<?php
require_once "../config/db.php";
session_start();

echo "Session Info:\n";
print_r($_SESSION);

echo "\nPets Table Sample (Last 10):\n";
$res = $conn->query("SELECT * FROM pets ORDER BY id DESC LIMIT 10");
if ($res) {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error: " . $conn->error;
}
?>
