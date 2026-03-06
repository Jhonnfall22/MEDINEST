<?php
session_start();

// Database connection
require_once 'config/db.php';

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $isAjax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

    // Check user in database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Use password_verify if password is hashed, otherwise plain check
        if (password_verify($password, $user['password'])) {
            // Check if user is verified
            if (isset($user['is_verified']) && $user['is_verified'] == 0) {
                if ($isAjax) {
                    echo json_encode(["success" => false, "message" => "Please verify your email address first."]);
                } else {
                    $_SESSION['login_error'] = "Please verify your email address first.";
                    header("Location: verify.php?email=" . urlencode($email));
                }
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['email']; // Standardizing username as email
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            if ($isAjax) {
                echo json_encode(["success" => true, "redirect" => "dashboard.php"]);
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }

    // Handle failure
    if ($isAjax) {
        echo json_encode(["success" => false, "message" => $error_message]);
    } else {
        $_SESSION['login_error'] = $error_message;
        header("Location: index.php");
    }
    exit();
}
?>