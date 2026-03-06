<?php
require_once 'config/db.php';
$email = 'logintest@example.com';
$stmt = $conn->prepare("SELECT verification_code FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($code);
$stmt->fetch();
echo trim($code);
?>
