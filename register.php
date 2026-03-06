<?php
session_start();
require_once "config/db.php"; // make sure path is correct

// MANUAL PHPMailer INCLUDES
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle AJAX form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajax'])) {

    header('Content-Type: application/json');

    $errors = [];

    // Sanitize inputs
    $firstName   = trim($_POST["firstName"] ?? "");
    $middleName  = trim($_POST["middleName"] ?? "");
    $lastName    = trim($_POST["lastName"] ?? "");
    $email       = trim($_POST["emailReg"] ?? "");
    $contact     = trim($_POST["contact"] ?? "");
    $address     = trim($_POST["address"] ?? "");
    $barangay    = trim($_POST["barangay"] ?? "");
    $password    = $_POST["passwordReg"] ?? "";
    $confirmPass = $_POST["confirmPassword"] ?? "";

    // Defaults
    $role = "user";
    $verificationCode = bin2hex(random_bytes(16));
    $isVerified = 0;

    // ---------- VALIDATION ----------
    if ($firstName === "" || $lastName === "") {
        $errors[] = "First and last name are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (!preg_match("/^09\d{9}$/", $contact)) {
        $errors[] = "Invalid Philippine mobile number.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $confirmPass) {
        $errors[] = "Passwords do not match.";
    }

    // Check existing email
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Email is already registered.";
        }
        $check->close();
    }

    // ---------- INSERT USER ----------
    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users 
            (first_name, middle_name, last_name, contact, address, barangay, email, password, role, verification_code, is_verified, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        // FIXED PARAMETER ORDER
        $stmt->bind_param(
            "ssssssssssi",
            $firstName,
            $middleName,
            $lastName,
            $contact,
            $address,
            $barangay,
            $email,
            $hashedPassword,
            $role,
            $verificationCode,
            $isVerified
        );

        if ($stmt->execute()) {

            // ---------- SEND VERIFICATION EMAIL ----------
            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'johnchristianloyola203@gmail.com';
                $mail->Password   = 'jtgfpcbxjjgjnebs';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('johnchristianloyola203@gmail.com', 'VetClinic');
                $mail->addAddress($email, $firstName . ' ' . $lastName);
                $mail->addReplyTo('johnchristianloyola203@gmail.com', 'VetClinic Support');

                $mail->isHTML(true);
                $mail->Subject = 'VetClinic Registration Confirmation';

                $verificationLink = "http://localhost/MediNest/verify.php?code=$verificationCode";

                $mail->Body = "
                    <h3>Thank you for registering, $firstName!</h3>
                    <p>Please click the link below to verify your email and activate your account:</p>
                    <a href='$verificationLink'>Verify Email</a>
                ";

                $mail->send();

                echo json_encode([
                    "success" => true,
                    "message" => "A verification email has been sent to $email."
                ]);

            } catch (Exception $e) {

                echo json_encode([
                    "success" => false,
                    "errors" => ["Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]
                ]);

            }

        } else {

            echo json_encode([
                "success" => false,
                "errors" => ["Registration failed. Please try again."]
            ]);

        }

        $stmt->close();

    } else {

        echo json_encode([
            "success" => false,
            "errors" => $errors
        ]);

    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | VetClinic</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Bubble Animation Background - Blue Theme */
    .bubbles-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: linear-gradient(135deg, #0c2d57 0%, #1a56db 50%, #3b82f6 100%);
      overflow: hidden;
    }

    .bubble {
      position: absolute;
      bottom: -100px;
      background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
      border-radius: 50%;
      animation: rise 10s infinite ease-in;
      box-shadow: 
        inset 0 0 30px rgba(255,255,255,0.3),
        0 0 20px rgba(255,255,255,0.1);
      backdrop-filter: blur(2px);
    }

    .bubble::before {
      content: '';
      position: absolute;
      top: 10%;
      left: 20%;
      width: 20%;
      height: 20%;
      background: radial-gradient(circle, rgba(255,255,255,0.8), transparent);
      border-radius: 50%;
    }

    .bubble:nth-child(1) { width: 40px; height: 40px; left: 10%; animation-duration: 8s; animation-delay: 0s; }
    .bubble:nth-child(2) { width: 20px; height: 20px; left: 20%; animation-duration: 12s; animation-delay: 1s; }
    .bubble:nth-child(3) { width: 50px; height: 50px; left: 35%; animation-duration: 10s; animation-delay: 2s; }
    .bubble:nth-child(4) { width: 80px; height: 80px; left: 50%; animation-duration: 15s; animation-delay: 0s; }
    .bubble:nth-child(5) { width: 35px; height: 35px; left: 55%; animation-duration: 9s; animation-delay: 3s; }
    .bubble:nth-child(6) { width: 45px; height: 45px; left: 65%; animation-duration: 11s; animation-delay: 2s; }
    .bubble:nth-child(7) { width: 90px; height: 90px; left: 70%; animation-duration: 14s; animation-delay: 4s; }
    .bubble:nth-child(8) { width: 25px; height: 25px; left: 80%; animation-duration: 10s; animation-delay: 1s; }
    .bubble:nth-child(9) { width: 15px; height: 15px; left: 90%; animation-duration: 8s; animation-delay: 5s; }
    .bubble:nth-child(10) { width: 60px; height: 60px; left: 5%; animation-duration: 13s; animation-delay: 3s; }
    .bubble:nth-child(11) { width: 30px; height: 30px; left: 25%; animation-duration: 9s; animation-delay: 4s; }
    .bubble:nth-child(12) { width: 70px; height: 70px; left: 45%; animation-duration: 16s; animation-delay: 1s; }
    .bubble:nth-child(13) { width: 55px; height: 55px; left: 75%; animation-duration: 11s; animation-delay: 2s; }
    .bubble:nth-child(14) { width: 22px; height: 22px; left: 85%; animation-duration: 7s; animation-delay: 6s; }
    .bubble:nth-child(15) { width: 48px; height: 48px; left: 15%; animation-duration: 12s; animation-delay: 5s; }

    @keyframes rise {
      0% {
        bottom: -100px;
        transform: translateX(0) scale(1);
        opacity: 0.5;
      }
      50% {
        transform: translateX(100px) scale(1.1);
        opacity: 0.8;
      }
      100% {
        bottom: 110%;
        transform: translateX(-50px) scale(0.8);
        opacity: 0;
      }
    }

    /* Floating particles */
    .particle {
      position: absolute;
      width: 6px;
      height: 6px;
      background: rgba(255,255,255,0.5);
      border-radius: 50%;
      animation: float 6s infinite ease-in-out;
    }

    .particle:nth-child(16) { top: 20%; left: 10%; animation-delay: 0s; }
    .particle:nth-child(17) { top: 40%; left: 30%; animation-delay: 1s; }
    .particle:nth-child(18) { top: 60%; left: 50%; animation-delay: 2s; }
    .particle:nth-child(19) { top: 30%; left: 70%; animation-delay: 3s; }
    .particle:nth-child(20) { top: 80%; left: 90%; animation-delay: 4s; }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.5; }
      50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
    }

    /* Main Container */
    .main-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .register-wrapper {
      display: flex;
      max-width: 1100px;
      width: 100%;
      background: rgba(255, 255, 255, 0.98);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Left Side - Image Section - Blue Theme */
    .image-section {
      flex: 1;
      background: linear-gradient(135deg, #0c2d57 0%, #1a56db 50%, #3b82f6 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
      overflow: hidden;
      min-height: 600px;
    }

    .image-section::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      animation: pattern-move 20s linear infinite;
    }

    @keyframes pattern-move {
      0% { transform: translate(0, 0); }
      100% { transform: translate(30px, 30px); }
    }

    .vet-illustration {
      position: relative;
      z-index: 2;
      margin-bottom: 30px;
    }

    .vet-illustration img {
      width: 280px;
      height: 280px;
      object-fit: cover;
      border-radius: 50%;
      border: 5px solid rgba(255,255,255,0.3);
      box-shadow: 
        0 20px 40px rgba(0,0,0,0.3),
        0 0 0 15px rgba(255,255,255,0.1);
      animation: pulse-glow 3s ease-in-out infinite;
    }

    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 0 15px rgba(255,255,255,0.1); }
      50% { box-shadow: 0 25px 50px rgba(0,0,0,0.4), 0 0 0 20px rgba(255,255,255,0.2), 0 0 60px rgba(59, 130, 246, 0.3); }
    }

    .image-section h2 {
      color: white;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 15px;
      text-align: center;
      position: relative;
      z-index: 2;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .image-section p {
      color: rgba(255,255,255,0.9);
      font-size: 1rem;
      text-align: center;
      position: relative;
      z-index: 2;
      max-width: 300px;
      line-height: 1.6;
    }

    .features-list {
      list-style: none;
      padding: 0;
      margin-top: 30px;
      position: relative;
      z-index: 2;
    }

    .features-list li {
      color: rgba(255,255,255,0.95);
      padding: 10px 0;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.95rem;
    }

    .features-list li i {
      width: 30px;
      height: 30px;
      background: rgba(255,255,255,0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
    }

    /* Paw prints decoration */
    .paw-print {
      position: absolute;
      font-size: 2rem;
      color: rgba(255,255,255,0.1);
      z-index: 1;
    }

    .paw-print:nth-of-type(1) { top: 10%; left: 10%; transform: rotate(-30deg); }
    .paw-print:nth-of-type(2) { top: 20%; right: 15%; transform: rotate(20deg); }
    .paw-print:nth-of-type(3) { bottom: 30%; left: 5%; transform: rotate(-15deg); }
    .paw-print:nth-of-type(4) { bottom: 15%; right: 10%; transform: rotate(45deg); }
    .paw-print:nth-of-type(5) { top: 50%; left: 15%; transform: rotate(10deg); }

    /* Right Side - Form Section */
    .form-section {
      flex: 1.2;
      padding: 40px 50px;
      max-height: 90vh;
      overflow-y: auto;
    }

    .form-section::-webkit-scrollbar {
      width: 6px;
    }

    .form-section::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .form-section::-webkit-scrollbar-thumb {
      background: #3b82f6;
      border-radius: 10px;
    }

    .form-section::-webkit-scrollbar-thumb:hover {
      background: #1a56db;
    }

    .form-header {
      margin-bottom: 25px;
    }

    .form-header .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .form-header .logo i {
      font-size: 2rem;
      color: #1a56db;
    }

    .form-header .logo span {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0c2d57;
    }

    .form-header h3 {
      font-size: 1.8rem;
      font-weight: 700;
      color: #0c2d57;
      margin-bottom: 5px;
    }

    .form-header p {
      color: #6c757d;
      font-size: 0.95rem;
    }

    /* Form Styling - Blue Theme */
    .form-label {
      font-weight: 500;
      color: #1a56db;
      font-size: 0.85rem;
      margin-bottom: 6px;
    }

    .form-control, .form-select {
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
      background-color: white;
    }

    .form-control::placeholder {
      color: #94a3b8;
    }

    .input-group {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      z-index: 10;
    }

    .input-group .form-control {
      padding-left: 45px;
    }

    /* Password Strength Indicator */
    .password-strength {
      height: 4px;
      border-radius: 2px;
      margin-top: 8px;
      background: #e2e8f0;
      overflow: hidden;
    }

    .password-strength-bar {
      height: 100%;
      width: 0;
      transition: all 0.3s ease;
      border-radius: 2px;
    }

    /* Checkbox Styling - Blue Theme */
    .form-check {
      padding-left: 0;
    }

    .form-check-input {
      width: 20px;
      height: 20px;
      border: 2px solid #cbd5e1;
      border-radius: 6px;
      margin-right: 10px;
      cursor: pointer;
    }

    .form-check-input:checked {
      background-color: #1a56db;
      border-color: #1a56db;
    }

    .form-check-input:focus {
      box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.15);
    }

    .form-check-label {
      color: #64748b;
      font-size: 0.9rem;
    }

    .form-check-label a {
      color: #1a56db;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .form-check-label a:hover {
      color: #3b82f6;
      text-decoration: underline;
    }

    /* Submit Button - Blue Theme */
    .btn-register {
      background: linear-gradient(135deg, #1a56db 0%, #3b82f6 100%);
      border: none;
      border-radius: 12px;
      padding: 14px;
      font-size: 1rem;
      font-weight: 600;
      color: white;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-register::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: all 0.5s ease;
    }

    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(26, 86, 219, 0.4);
      background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    }

    .btn-register:hover::before {
      left: 100%;
    }

    .btn-register:active {
      transform: translateY(0);
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
    }

    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e2e8f0;
    }

    .divider span {
      padding: 0 15px;
      color: #94a3b8;
      font-size: 0.85rem;
    }

    /* Social Login - Blue Theme */
    .social-login {
      display: flex;
      gap: 12px;
    }

    .btn-social {
      flex: 1;
      padding: 12px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 0.9rem;
      color: #475569;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .btn-social:hover {
      border-color: #3b82f6;
      background: #f8fafc;
      transform: translateY(-2px);
    }

    .btn-social i {
      font-size: 1.2rem;
    }

    .btn-google i { color: #ea4335; }
    .btn-facebook i { color: #1877f2; }

    /* Login Link - Blue Theme */
    .login-link {
      text-align: center;
      margin-top: 20px;
      color: #64748b;
      font-size: 0.95rem;
    }

    .login-link a {
      color: #1a56db;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .login-link a:hover {
      color: #3b82f6;
      text-decoration: underline;
    }

    /* Row Animation */
    .form-row {
      animation: fadeInUp 0.5s ease forwards;
      opacity: 0;
    }

    .form-row:nth-child(1) { animation-delay: 0.1s; }
    .form-row:nth-child(2) { animation-delay: 0.15s; }
    .form-row:nth-child(3) { animation-delay: 0.2s; }
    .form-row:nth-child(4) { animation-delay: 0.25s; }
    .form-row:nth-child(5) { animation-delay: 0.3s; }
    .form-row:nth-child(6) { animation-delay: 0.35s; }
    .form-row:nth-child(7) { animation-delay: 0.4s; }
    .form-row:nth-child(8) { animation-delay: 0.45s; }
    .form-row:nth-child(9) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Input Hover Effects */
    .form-control:hover, .form-select:hover {
      border-color: #93c5fd;
    }

    /* Required field indicator */
    .form-label::after {
      content: '';
    }

    /* Floating Label Animation (optional enhancement) */
    .input-group.focused .input-icon {
      color: #3b82f6;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .register-wrapper {
        flex-direction: column;
        max-width: 500px;
      }

      .image-section {
        min-height: 300px;
        padding: 30px;
      }

      .vet-illustration img {
        width: 150px;
        height: 150px;
      }

      .image-section h2 {
        font-size: 1.5rem;
      }

      .features-list {
        display: none;
      }

      .form-section {
        padding: 30px;
      }
    }

    @media (max-width: 576px) {
      .main-container {
        padding: 10px;
      }

      .form-section {
        padding: 25px 20px;
      }

      .social-login {
        flex-direction: column;
      }

      .image-section {
        min-height: 250px;
      }

      .form-header h3 {
        font-size: 1.5rem;
      }

      .row.g-3 > div {
        margin-bottom: 5px;
      }
    }

    /* Extra animations for visual appeal */
    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    .btn-register {
      background-size: 200% 100%;
    }

    .btn-register:hover {
      animation: shimmer 1.5s infinite;
    }
  </style>
</head>
<body>

<!-- Animated Bubbles Background -->
<div class="bubbles-container">
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
</div>

<div class="main-container">
  <div class="register-wrapper">
    
    <!-- Left Side - Image Section -->
    <div class="image-section">
      <i class="fas fa-paw paw-print"></i>
      <i class="fas fa-paw paw-print"></i>
      <i class="fas fa-paw paw-print"></i>
      <i class="fas fa-paw paw-print"></i>
      <i class="fas fa-paw paw-print"></i>
      
      <div class="vet-illustration">
        <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&h=400&fit=crop&crop=face" alt="Happy Pet">
      </div>
      
      <h2>Welcome to VetClinic</h2>
      <p>Join our caring community and give your pets the best healthcare they deserve.</p>
      
      <ul class="features-list">
        <li>
          <i class="fas fa-calendar-check"></i>
          Easy Online Appointments
        </li>
        <li>
          <i class="fas fa-user-md"></i>
          Expert Veterinarians
        </li>
        <li>
          <i class="fas fa-heart"></i>
          24/7 Pet Care Support
        </li>
        <li>
          <i class="fas fa-shield-alt"></i>
          Secure Health Records
        </li>
      </ul>
    </div>

    <!-- Right Side - Form Section -->
    <div class="form-section">
      <div class="form-header">
        <div class="logo">
          <i class="fas fa-clinic-medical"></i>
          <span>VetClinic</span>
        </div>
        <h3>Create Your Account</h3>
        <p>Fill in your details to get started</p>
      </div>

      <form id="registerForm" novalidate>
        <div class="row g-3 form-row">
          <div class="col-md-4">
            <label class="form-label">First Name *</label>
            <input type="text" name="firstName" class="form-control" placeholder="John" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middleName" class="form-control" placeholder="Robert">
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name *</label>
            <input type="text" name="lastName" class="form-control" placeholder="Doe" required>
          </div>
        </div>

        <div class="mt-3 form-row">
          <label class="form-label">Email Address *</label>
          <div class="input-group">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="emailReg" class="form-control" placeholder="your.email@example.com" required>
          </div>
        </div>

        <div class="mt-3 form-row">
          <label class="form-label">Contact Number *</label>
          <div class="input-group">
            <i class="fas fa-phone input-icon"></i>
            <input type="tel" name="contact" class="form-control" placeholder="09xxxxxxxxx" required pattern="09\d{9}" maxlength="11">
          </div>
        </div>

        <div class="mt-3 form-row">
          <label class="form-label">Complete Address</label>
          <div class="input-group">
            <i class="fas fa-map-marker-alt input-icon"></i>
            <input type="text" name="address" class="form-control" placeholder="House No., Street, Subdivision">
          </div>
        </div>

        <div class="mt-3 form-row">
          <label class="form-label">Barangay (Dasmariñas) *</label>
          <select class="form-select" id="barangay" name="barangay" required>
            <option value="" selected disabled>Select your Barangay</option>
            <option>Burol</option>
            <option>Burol I</option>
            <option>Burol II</option>
            <option>Burol III</option>
            <option>Datu Esmael</option>
            <option>Emmanuel Bergado I</option>
            <option>Emmanuel Bergado II</option>
            <option>Fatima I</option>
            <option>Fatima II</option>
            <option>Fatima III</option>
            <option>H-2</option>
            <option>Langkaan I</option>
            <option>Langkaan II</option>
            <option>Luzviminda I</option>
            <option>Luzviminda II</option>
            <option>Luzviminda III</option>
            <option>Paliparan I</option>
            <option>Paliparan II</option>
            <option>Paliparan III</option>
            <option>Sabang</option>
            <option>Salawag</option>
            <option>Salitran I</option>
            <option>Salitran II</option>
            <option>Salitran III</option>
            <option>Salitran IV</option>
            <option>Sampaloc I</option>
            <option>Sampaloc II</option>
            <option>Sampaloc III</option>
            <option>Sampaloc IV</option>
            <option>Sampaloc V</option>
            <option>San Agustin I</option>
            <option>San Agustin II</option>
            <option>San Agustin III</option>
            <option>San Andres I</option>
            <option>San Andres II</option>
            <option>San Antonio De Padua I</option>
            <option>San Antonio De Padua II</option>
            <option>San Dionisio</option>
            <option>San Esteban</option>
            <option>San Francisco I</option>
            <option>San Francisco II</option>
            <option>San Isidro Labrador I</option>
            <option>San Isidro Labrador II</option>
            <option>San Jose</option>
            <option>San Juan I</option>
            <option>San Juan II</option>
            <option>San Lorenzo Ruiz I</option>
            <option>San Lorenzo Ruiz II</option>
            <option>San Luis I</option>
            <option>San Luis II</option>
            <option>San Manuel I</option>
            <option>San Manuel II</option>
            <option>San Mateo</option>
            <option>San Miguel</option>
            <option>San Nicolas I</option>
            <option>San Nicolas II</option>
            <option>San Roque</option>
            <option>San Simon</option>
            <option>Santa Cristina I</option>
            <option>Santa Cristina II</option>
            <option>Santa Fe</option>
            <option>Santa Lucia</option>
            <option>Santa Maria</option>
            <option>Santo Cristo</option>
            <option>Santo Niño I</option>
            <option>Santo Niño II</option>
            <option>Victoria Reyes</option>
            <option>Zone I</option>
            <option>Zone I-A</option>
            <option>Zone II</option>
            <option>Zone III</option>
            <option>Zone IV</option>
          </select>
        </div>

        <div class="row g-3 mt-2 form-row">
          <div class="col-md-6">
            <label class="form-label">Password *</label>
            <div class="input-group">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" name="passwordReg" id="passwordReg" class="form-control" placeholder="Min. 8 characters" required minlength="8">
            </div>
            <div class="password-strength">
              <div class="password-strength-bar" id="strengthBar"></div>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Confirm Password *</label>
            <div class="input-group">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm password" required minlength="8">
            </div>
          </div>
        </div>

        <div class="form-check mt-4 form-row d-flex align-items-center">
          <input class="form-check-input" type="checkbox" id="termsCheck" required>
          <label class="form-check-label" for="termsCheck">
            I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a>
          </label>
        </div>

        <div class="d-grid mt-4 form-row">
          <button type="submit" class="btn btn-register">
            <i class="fas fa-user-plus me-2"></i>Create Account
          </button>
        </div>

        <div class="login-link form-row">
          Already have an account? <a href="index.php">Sign In</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Password Strength Checker
document.getElementById('passwordReg').addEventListener('input', function() {
  const password = this.value;
  const strengthBar = document.getElementById('strengthBar');
  let strength = 0;
  
  if (password.length >= 8) strength += 25;
  if (password.match(/[a-z]+/)) strength += 25;
  if (password.match(/[A-Z]+/)) strength += 25;
  if (password.match(/[0-9]+/)) strength += 15;
  if (password.match(/[^a-zA-Z0-9]+/)) strength += 10;
  
  strengthBar.style.width = strength + '%';
  
  if (strength <= 25) {
    strengthBar.style.background = '#ef4444';
  } else if (strength <= 50) {
    strengthBar.style.background = '#f59e0b';
  } else if (strength <= 75) {
    strengthBar.style.background = '#3b82f6';
  } else {
    strengthBar.style.background = '#22c55e';
  }
});

// Form Submission
document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Add loading state
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
  submitBtn.disabled = true;

  let formData = new FormData(this);
  formData.append('ajax', 1);

  fetch('register.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
      
      if (data.success) {
        Swal.fire({
          title: 'Almost there!',
          html: data.message + '<br><br><i class="fas fa-envelope fa-3x mb-3" style="color: #3b82f6;"></i><br>Please check your email and click the verification link to continue.',
          icon: 'success',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          background: '#fff',
          customClass: {
            popup: 'animated fadeInDown'
          },
          didOpen: () => { 
            Swal.showLoading(); 
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Registration Failed',
          html: data.errors.join('<br>'),
          confirmButtonColor: '#1a56db'
        });
      }
    })
    .catch(err => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
      
      Swal.fire({
        icon: 'error',
        title: 'Connection Error',
        text: 'Something went wrong. Please try again.',
        confirmButtonColor: '#1a56db'
      });
    });
});

// Input focus animation
document.querySelectorAll('.form-control, .form-select').forEach(input => {
  input.addEventListener('focus', function() {
    this.parentElement.classList.add('focused');
  });
  input.addEventListener('blur', function() {
    this.parentElement.classList.remove('focused');
  });
});
</script>

</body>
</html>