<?php
// Mock session
$_SESSION['user_id'] = 1; // Assuming 1
$_SESSION['username'] = 'Guest'; 
$_SESSION['email'] = 'test@example.com';
$_SESSION['full_name'] = 'Test User';

// Include the target file but we need to bypass session_start
ob_start();
include "get_pets.php";
$output = ob_get_clean();

echo "OUTPUT:\n";
echo $output;
?>
