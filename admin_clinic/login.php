<?php
session_start();
require_once '../users/config/db.php';

// Redirect if already logged in
if (isset($_SESSION['clinic_admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, clinic_name, password, status FROM clinics WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Check if account is approved
        if (strcasecmp($row['status'], 'Approved') !== 0) {
            echo json_encode(['status' => 'error', 'message' => 'Your clinic account is currently ' . $row['status'] . '. Please wait for approval.']);
            exit();
        }

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['clinic_admin_id'] = $row['id'];
            $_SESSION['clinic_name'] = $row['clinic_name'];
            
            echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Admin email not found.']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Veterinary Clinic Admin Login" />
    <meta name="author" content="flexilecode" />

    <title>Clinic Admin Login | Vet Clinics</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        .login-wrapper {
            display: flex;
            max-width: 950px;
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
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
            min-height: 550px;
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

        .clinic-illustration {
            position: relative;
            z-index: 2;
            margin-bottom: 30px;
        }

        .clinic-illustration img {
            width: 200px;
            height: 200px;
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
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .image-section p {
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
            text-align: center;
            position: relative;
            z-index: 2;
            max-width: 280px;
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
            font-size: 0.9rem;
        }

        .features-list li i {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        /* Medical icons decoration */
        .med-icon {
            position: absolute;
            font-size: 1.8rem;
            color: rgba(255,255,255,0.1);
            z-index: 1;
        }

        .med-icon:nth-of-type(1) { top: 8%; left: 10%; transform: rotate(-30deg); }
        .med-icon:nth-of-type(2) { top: 15%; right: 12%; transform: rotate(20deg); }
        .med-icon:nth-of-type(3) { bottom: 20%; left: 8%; transform: rotate(-15deg); }
        .med-icon:nth-of-type(4) { bottom: 10%; right: 15%; transform: rotate(45deg); }
        .med-icon:nth-of-type(5) { top: 50%; left: 5%; transform: rotate(10deg); }

        /* Right Side - Form Section */
        .form-section {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .form-header .logo i {
            font-size: 2.2rem;
            color: #1a56db;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            padding: 14px;
            border-radius: 14px;
        }

        .form-header .logo span {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0c2d57;
        }

        .form-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0c2d57;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Form Styling - Blue Theme */
        .form-label {
            font-weight: 600;
            color: #1e40af;
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-label i {
            font-size: 0.8rem;
            color: #3b82f6;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            background-color: white;
        }

        .form-control:hover {
            border-color: #93c5fd;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        /* Input with icon */
        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .input-group .form-control {
            padding-left: 50px;
        }

        .input-group.focused .input-icon {
            color: #3b82f6;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        /* Remember Me & Forgot Password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-check {
            padding-left: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 5px;
            cursor: pointer;
            margin: 0;
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
            cursor: pointer;
        }

        .forgot-link {
            color: #1a56db;
            font-size: 0.9rem;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        /* Submit Button - Blue Theme */
        .btn-login {
            background: linear-gradient(135deg, #1a56db 0%, #3b82f6 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(26, 86, 219, 0.4);
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-social i {
            font-size: 1.2rem;
        }

        .btn-google i { color: #ea4335; }
        .btn-facebook i { color: #1877f2; }

        /* Register Link - Blue Theme */
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.95rem;
        }

        .register-link a {
            color: #1a56db;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        /* Form Animation */
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

        /* Welcome Badge */
        .welcome-badge {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #3b82f6;
        }

        .welcome-badge i {
            font-size: 1.2rem;
            color: #1a56db;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 480px;
            }

            .image-section {
                min-height: 280px;
                padding: 35px;
            }

            .clinic-illustration img {
                width: 120px;
                height: 120px;
            }

            .image-section h2 {
                font-size: 1.4rem;
            }

            .features-list {
                display: none;
            }

            .form-section {
                padding: 35px;
            }
        }

        @media (max-width: 576px) {
            .main-container {
                padding: 15px;
            }

            .form-section {
                padding: 30px 25px;
            }

            .image-section {
                min-height: 220px;
                padding: 30px;
            }

            .form-header h3 {
                font-size: 1.5rem;
            }

            .social-login {
                flex-direction: column;
            }

            .form-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        /* Shake Animation for Error */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease;
        }

        /* Success Checkmark Animation */
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .success-icon {
            animation: checkmark 0.5s ease;
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
    <div class="login-wrapper">
        
        <!-- Left Side - Image Section -->
        <div class="image-section">
            <i class="fas fa-stethoscope med-icon"></i>
            <i class="fas fa-heartbeat med-icon"></i>
            <i class="fas fa-clinic-medical med-icon"></i>
            <i class="fas fa-paw med-icon"></i>
            <i class="fas fa-syringe med-icon"></i>
            
            <div class="clinic-illustration">
                <img src="https://images.unsplash.com/photo-1628009368231-7bb7cfcb0def?w=400&h=400&fit=crop" alt="Veterinary Clinic">
            </div>
            
            <h2>Welcome Back!</h2>
            <p>Manage your veterinary clinic efficiently with our comprehensive dashboard.</p>
            
            <ul class="features-list">
                <li>
                    <i class="fas fa-calendar-check"></i>
                    Manage Appointments
                </li>
                <li>
                    <i class="fas fa-paw"></i>
                    Patient Records
                </li>
                <li>
                    <i class="fas fa-chart-bar"></i>
                    Analytics & Reports
                </li>
                <li>
                    <i class="fas fa-bell"></i>
                    Real-time Notifications
                </li>
            </ul>
        </div>

        <!-- Right Side - Form Section -->
        <div class="form-section">
            <div class="form-header">
                <div class="logo">
                    <i class="fas fa-hospital-user"></i>
                    <span>VetClinic Admin</span>
                </div>
                <h3>Sign In</h3>
                <p>Access your clinic management dashboard</p>
            </div>

            <div class="welcome-badge form-row">
                <i class="fas fa-shield-alt"></i>
                <span>Secure login with 256-bit SSL encryption</span>
            </div>

            <form id="loginForm" action="index.html" method="POST">
                
                <div class="mb-4 form-row">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <div class="input-group">
                        <i class="fas fa-at input-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="admin@yourclinic.com" required>
                    </div>
                </div>

                <div class="mb-3 form-row">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-options form-row">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    <a href="forgot_password.html" class="forgot-link">
                        <i class="fas fa-question-circle"></i> Forgot password?
                    </a>
                </div>

                <div class="d-grid form-row">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In to Dashboard
                    </button>
                </div>
                
                <div class="login-link form-row text-center mt-4">
                Already have a clinic account? <a href="register.php">Sign up</a>
                </div>
            </form>

  
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Password Toggle
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});

// Input focus animation
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});

// Form Submission with validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = this.querySelector('input[name="email"]').value;
    const password = this.querySelector('input[name="password"]').value;
    
    // Basic validation
    if (!email || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please fill in all required fields.',
            confirmButtonColor: '#1a56db'
        });
        return;
    }

    // Add loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
    submitBtn.disabled = true;

    // Actual AJAX call
    const formData = new FormData(this);
    formData.append('action', 'login');

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                html: '<i class="fas fa-check-circle fa-3x mb-3 success-icon" style="color: #22c55e;"></i><br>' + data.message + ' Redirecting...',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'index.php';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: data.message,
                confirmButtonColor: '#1a56db'
            });
            document.querySelector('.login-wrapper').classList.add('shake');
            setTimeout(() => {
                document.querySelector('.login-wrapper').classList.remove('shake');
            }, 500);
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'System Error',
            text: 'An error occurred. Please try again later.',
            confirmButtonColor: '#1a56db'
        });
    });
});

// Remember Me - Store email in localStorage
const rememberCheckbox = document.getElementById('rememberMe');
const emailInput = document.querySelector('input[name="email"]');

// Load saved email
if (localStorage.getItem('rememberedEmail')) {
    emailInput.value = localStorage.getItem('rememberedEmail');
    rememberCheckbox.checked = true;
}

// Save email on form submit
document.getElementById('loginForm').addEventListener('submit', function() {
    if (rememberCheckbox.checked) {
        localStorage.setItem('rememberedEmail', emailInput.value);
    } else {
        localStorage.removeItem('rememberedEmail');
    }
});

// Social login buttons (placeholder functionality)
document.querySelector('.btn-google').addEventListener('click', function() {
    Swal.fire({
        title: 'Google Sign In',
        html: '<i class="fab fa-google fa-3x mb-3" style="color: #ea4335;"></i><br>Redirecting to Google...',
        showConfirmButton: false,
        timer: 2000
    });
});

document.querySelector('.btn-facebook').addEventListener('click', function() {
    Swal.fire({
        title: 'Facebook Sign In',
        html: '<i class="fab fa-facebook fa-3x mb-3" style="color: #1877f2;"></i><br>Redirecting to Facebook...',
        showConfirmButton: false,
        timer: 2000
    });
});
</script>

</body>
</html>