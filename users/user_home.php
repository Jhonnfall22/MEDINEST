<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$username = 'Guest';
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $username = $user['first_name'] . ' ' . $user['last_name'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home | VetClinic</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-200: #bfdbfe;
            --primary-300: #93c5fd;
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --primary-800: #1e40af;
            --primary-900: #1e3a8a;
            --gradient-primary: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            --gradient-light: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            --gradient-hero: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-50);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--primary-100); }
        ::-webkit-scrollbar-thumb { background: var(--primary-400); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-500); }

        /* ===== NAVBAR ===== */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 15px 0;
            transition: all 0.3s;
            border-bottom: 1px solid var(--primary-100);
            box-shadow: 0 4px 30px rgba(30, 64, 175, 0.08);
        }

        .navbar.scrolled {
            padding: 10px 0;
            box-shadow: 0 4px 30px rgba(30, 64, 175, 0.15);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-brand span {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-brand .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        /* ===== BACK BUTTON ===== */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border: 2px solid var(--primary-200);
            border-radius: 50px;
            background: white;
            color: var(--primary-700);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(30, 64, 175, 0.08);
        }

        .btn-back:hover {
            border-color: var(--primary-500);
            color: var(--primary-800);
            background: var(--primary-50);
            transform: translateX(-5px);
            box-shadow: 0 4px 20px rgba(30, 64, 175, 0.15);
        }

        .btn-back i {
            transition: transform 0.3s ease;
            font-size: 1.1rem;
        }

        .btn-back:hover i {
            transform: translateX(-3px);
        }

        /* ===== NAV BUTTONS ===== */
        .nav-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 50px;
            color: var(--primary-700);
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid var(--primary-200);
        }

        .welcome-badge .avatar {
            width: 32px;
            height: 32px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border: 2px solid #fecaca;
            border-radius: 50px;
            background: white;
            color: #dc2626;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            border-color: #dc2626;
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(220, 38, 38, 0.3);
        }

        /* ===== HERO SECTION ===== */
        .hero {
            position: relative;
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            overflow: hidden;
            margin-top: 76px;
        }

        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-hero);
            z-index: 1;
        }

        .hero-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('pic.jpg') center/cover no-repeat;
            opacity: 0.15;
            mix-blend-mode: overlay;
        }

        /* Animated Background Shapes */
        .hero-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            left: -50px;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 10%;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 150px;
            height: 150px;
            top: 30%;
            right: 15%;
            animation-delay: -15s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(5deg); }
            50% { transform: translate(-20px, 20px) rotate(-5deg); }
            75% { transform: translate(20px, 30px) rotate(3deg); }
        }

        /* Floating Paw Prints */
        .floating-paws {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            pointer-events: none;
        }

        .paw {
            position: absolute;
            font-size: 2rem;
            opacity: 0.1;
            animation: floatPaw 15s infinite ease-in-out;
        }

        .paw:nth-child(1) { top: 15%; left: 8%; animation-delay: 0s; }
        .paw:nth-child(2) { top: 25%; right: 12%; animation-delay: -3s; }
        .paw:nth-child(3) { top: 60%; left: 5%; animation-delay: -6s; }
        .paw:nth-child(4) { top: 70%; right: 8%; animation-delay: -9s; }
        .paw:nth-child(5) { top: 40%; left: 15%; animation-delay: -12s; }
        .paw:nth-child(6) { top: 80%; left: 20%; animation-delay: -4s; }

        @keyframes floatPaw {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.1; }
            50% { transform: translateY(-30px) rotate(15deg); opacity: 0.2; }
        }

        .hero-content {
            max-width: 900px;
            padding: 0 30px;
            z-index: 10;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease;
        }

        .hero-badge i {
            color: #fbbf24;
            font-size: 1.1rem;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            margin-bottom: 25px;
            line-height: 1.1;
            animation: fadeInUp 0.8s ease;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .hero-content h1 .highlight {
            position: relative;
            display: inline-block;
        }

        .hero-content h1 .highlight::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            height: 15px;
            background: rgba(251, 191, 36, 0.4);
            border-radius: 5px;
            z-index: -1;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            font-weight: 400;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        /* ===== START BUTTON ===== */
        .btn-start {
            background: white;
            color: var(--primary-700);
            font-weight: 700;
            padding: 18px 45px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: none;
            font-size: 1.05rem;
        }

        .btn-start:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            color: var(--primary-800);
        }

        .btn-start i {
            transition: transform 0.3s ease;
            font-size: 1.2rem;
        }

        .btn-start:hover i {
            transform: translateX(5px);
        }

        .btn-secondary-hero {
            background: transparent;
            color: white;
            font-weight: 600;
            padding: 18px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-size: 1rem;
        }

        .btn-secondary-hero:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-3px);
        }

        /* Hero Bottom Wave */
        .hero-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 5;
        }

        .hero-wave svg {
            display: block;
            width: 100%;
            height: auto;
        }

        /* ===== INFO BAR ===== */
        .info-bar {
            margin-top: -80px;
            position: relative;
            z-index: 20;
            padding: 0 20px;
        }

        .info-card {
            background: white;
            border-radius: 24px;
            padding: 35px 30px;
            box-shadow: 0 15px 50px rgba(30, 64, 175, 0.1);
            border: 1px solid var(--primary-100);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(30, 64, 175, 0.15);
        }

        .info-card .icon-wrapper {
            width: 70px;
            height: 70px;
            background: var(--gradient-light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            transition: transform 0.3s ease;
        }

        .info-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .info-card .icon-wrapper i {
            font-size: 1.8rem;
            color: white;
        }

        .info-card h6 {
            font-weight: 700;
            color: var(--primary-800);
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .info-card p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.6;
        }

        /* ===== FEATURES SECTION ===== */
        .features-section {
            padding: 100px 0;
            background: white;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-100);
            color: var(--primary-700);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-900);
            margin-bottom: 15px;
        }

        .section-header p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            padding: 40px 30px;
            border-radius: 24px;
            background: var(--primary-50);
            border: 1px solid var(--primary-100);
            transition: all 0.4s ease;
            height: 100%;
        }

        .feature-card:hover {
            background: white;
            box-shadow: 0 20px 50px rgba(30, 64, 175, 0.1);
            transform: translateY(-5px);
        }

        .feature-card .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.25);
        }

        .feature-card .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .feature-card h5 {
            font-weight: 700;
            color: var(--primary-800);
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.7;
            margin: 0;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            padding: 80px 0;
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .cta-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .cta-content h3 {
            font-size: 2.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 15px;
        }

        .cta-content p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .btn-cta {
            background: white;
            color: var(--primary-700);
            font-weight: 700;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            color: var(--primary-800);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--primary-900);
            padding: 60px 0 30px;
            color: white;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-brand .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 300px;
        }

        .footer-links h6 {
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px;
            margin-top: 50px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary-500);
            transform: translateY(-3px);
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                padding: 20px;
                border-radius: 20px;
                margin-top: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            }

            .nav-buttons {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }

            .welcome-badge, .btn-logout {
                width: 100%;
                justify-content: center;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-start, .btn-secondary-hero {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .hero {
                min-height: 90vh;
            }

            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-content p {
                font-size: 1rem;
            }

            .info-bar {
                margin-top: -40px;
            }

            .info-card {
                padding: 25px 20px;
            }

            .section-header h2 {
                font-size: 1.8rem;
            }

            .cta-content h3 {
                font-size: 1.6rem;
            }

            .btn-back span {
                display: none;
            }

            .btn-back {
                padding: 10px 15px;
            }
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
    <div class="container">
        <!-- Back Button -->
        <a href="../dashboard.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span>Back</span>
        </a>

        <a class="navbar-brand ms-3" href="user_home.php">
            <div class="logo-icon">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>
            Vet<span>clinic</span>
        </a>
        
        <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <div class="nav-buttons ms-auto">
                <div class="welcome-badge">
                    <div class="avatar">
                        <?php echo strtoupper(substr($username, 0, 1)); ?>
                    </div>
                    <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-background"></div>
    
    <!-- Animated Shapes -->
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <!-- Floating Paws -->
    <div class="floating-paws">
        <span class="paw">🐾</span>
        <span class="paw">🐾</span>
        <span class="paw">🐾</span>
        <span class="paw">🐾</span>
        <span class="paw">🐾</span>
        <span class="paw">🐾</span>
    </div>

    <div class="hero-content">
        <div class="hero-badge">
            <i class="bi bi-star-fill"></i>
            Expert Pet Care 24/7
        </div>
        <h1>Compassionate Care<br>for Your <span class="highlight">Best Friend</span></h1>
        <p>Easily manage your pet's health records, schedule clinic visits, and track appointments all in one place.</p>
        <div class="hero-buttons">
            <a href="search_clinic.php" class="btn-start">
                <i class="bi bi-hospital"></i>
                Choose Your Clinic
                <i class="bi bi-arrow-right"></i>
            </a>
            <a href="mypets.php" class="btn-secondary-hero">    
                <i class="bi bi-heart"></i>
                View My Pets
            </a>
        </div>
    </div>

    <!-- Wave Bottom -->
    <div class="hero-wave">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="var(--primary-50)"/>
        </svg>
    </div>
</section>

<!-- Info Bar -->
<div class="container info-bar">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-4 col-md-6">
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h6>Fast Booking</h6>
                <p>Book appointments in under 2 minutes with our streamlined process.</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h6>Trusted Clinics</h6>
                <p>Verified network of professional veterinarians you can trust.</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h6>Nearby Locations</h6>
                <p>Find the nearest clinic with our interactive map feature.</p>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="bi bi-stars"></i>
                Why Choose Us
            </div>
            <h2>Everything Your Pet Needs</h2>
            <p>We provide comprehensive care solutions to keep your furry friends happy and healthy.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p>Round-the-clock emergency support for your pet's urgent needs. We're always here when you need us most.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-file-medical"></i>
                    </div>
                    <h5>Health Records</h5>
                    <p>Keep track of vaccinations, treatments, and medical history all in one secure digital location.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-map"></i>
                    </div>
                    <h5>Find Nearby Clinics</h5>
                    <p>Use our interactive map to discover partner clinics near you with real-time directions.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-bell"></i>
                    </div>
                    <h5>Smart Reminders</h5>
                    <p>Never miss an appointment with automated reminders for checkups and vaccinations.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5>Expert Vets</h5>
                    <p>Access to a network of certified veterinarians with years of experience in pet care.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <h5>Wellness Programs</h5>
                    <p>Comprehensive wellness plans tailored to your pet's specific needs and lifestyle.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h3>Ready to Give Your Pet the Best Care?</h3>
            <p>Join thousands of pet owners who trust VetClinic for their furry friends.</p>
            <a href="pets.php" class="btn-cta">
                <i class="bi bi-rocket-takeoff"></i>
                Get Started Now
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <div class="logo-icon">
                        <i class="bi bi-heart-pulse-fill text-white"></i>
                    </div>
                    Vetclinic
                </div>
                <p class="footer-text">Providing compassionate and professional veterinary care for your beloved pets since 2020.</p>
                <div class="social-links">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-links">
                    <h6>Quick Links</h6>
                    <ul>
                        <li><a href="user_home.php">Home</a></li>
                        <li><a href="pets.php">Find Clinic</a></li>
                        <li><a href="mypets.php">My Pets</a></li>
                        <li><a href="#">Appointments</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-links">
                    <h6>Services</h6>
                    <ul>
                        <li><a href="#">Vaccination</a></li>
                        <li><a href="#">Surgery</a></li>
                        <li><a href="#">Grooming</a></li>
                        <li><a href="#">Emergency</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="footer-links">
                    <h6>Contact Us</h6>
                    <ul>
                        <li><i class="bi bi-geo-alt me-2"></i>Dasmariñas, Cavite, Philippines</li>
                        <li><i class="bi bi-telephone me-2"></i>(046) 123-4567</li>
                        <li><i class="bi bi-envelope me-2"></i>support@vetclinic.com</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="mb-0">&copy; 2026 VetClinic Network. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.info-card, .feature-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
</script>

</body>
</html>