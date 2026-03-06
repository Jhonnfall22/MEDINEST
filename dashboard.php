<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Validate session user from database
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$user = $result->fetch_assoc();
$user['name'] = $user['first_name'] . ' ' . $user['last_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Vet Clinic</title>
  <meta name="description" content="Professional veterinary care for your pets">
  <meta name="keywords" content="vet, clinic, pet care, veterinary">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@300;400;500;600;700;800;900&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* ============================================================
       CUSTOM PROPERTIES & MINIMAL CUSTOM CSS
       (Only styles Bootstrap can't handle)
       ============================================================ */
    :root {
      --blue-darkest: #0c2d57;
      --blue-dark: #1a56db;
      --blue-primary: #3b82f6;
      --blue-light: #60a5fa;
      --blue-lighter: #93c5fd;
      --blue-lightest: #dbeafe;
      --blue-bg: #eff6ff;
    }

    body {
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
    }

    /* ==================== HEADER ==================== */
    #header {
      background: linear-gradient(135deg, var(--blue-darkest) 0%, var(--blue-dark) 100%);
      padding: 15px 0;
      transition: all 0.4s ease;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    #header.scrolled {
      background: rgba(12, 45, 87, 0.98);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }

    #header .logo .sitename {
      font-size: 1.8rem;
      font-weight: 700;
      color: #fff;
    }

    #header .logo .sitename span {
      color: var(--blue-light);
    }

    /* Nav Links */
    .navmenu ul {
      gap: 8px;
    }

    .navmenu ul li a {
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.95rem;
      font-weight: 500;
      padding: 10px 18px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .navmenu ul li a:hover,
    .navmenu ul li a.active {
      color: #fff;
      background: rgba(255, 255, 255, 0.1);
    }

    .navmenu ul li a.active::after {
      content: '';
      position: absolute;
      bottom: 5px;
      left: 50%;
      transform: translateX(-50%);
      width: 20px;
      height: 3px;
      background: var(--blue-light);
      border-radius: 3px;
    }

    /* Profile Dropdown */
    .profile-dropdown .dropdown-toggle {
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-light) 100%);
      color: #fff !important;
      padding: 10px 20px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
      border: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .profile-dropdown .dropdown-toggle:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
      background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-primary) 100%);
    }

    .profile-dropdown .dropdown-toggle::after {
      margin-left: 5px;
      vertical-align: middle;
    }

    .profile-dropdown .dropdown-toggle .profile-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .profile-dropdown .dropdown-toggle .profile-icon {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .profile-dropdown .dropdown-menu {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
      padding: 12px;
      min-width: 220px;
      margin-top: 10px;
      animation: dropdownFadeIn 0.3s ease;
    }

    @keyframes dropdownFadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .profile-dropdown .dropdown-header {
      padding: 12px 15px;
      background: linear-gradient(135deg, var(--blue-lightest) 0%, #fff 100%);
      border-radius: 12px;
      margin-bottom: 8px;
    }

    .profile-dropdown .dropdown-header .user-name {
      font-weight: 600;
      color: var(--blue-darkest);
      font-size: 0.95rem;
    }

    .profile-dropdown .dropdown-header .user-email {
      font-size: 0.8rem;
      color: #64748b;
    }

    .profile-dropdown .dropdown-item {
      padding: 12px 15px;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 500;
      color: #475569;
      transition: all 0.3s ease;
    }

    .profile-dropdown .dropdown-item:hover {
      background: var(--blue-lightest);
      color: var(--blue-dark);
      transform: translateX(5px);
    }

    .profile-dropdown .dropdown-item i {
      width: 20px;
      font-size: 1rem;
    }

    .profile-dropdown .dropdown-item.text-danger:hover {
      background: #fee2e2;
      color: #dc2626;
    }

    .profile-dropdown .dropdown-divider {
      margin: 8px 0;
      border-color: #e2e8f0;
    }

    /* ==================== HERO ==================== */
    .hero {
      min-height: 100vh;
      background: linear-gradient(135deg, var(--blue-darkest) 0%, var(--blue-dark) 50%, var(--blue-primary) 100%);
      overflow: hidden;
      padding-top: 100px;
    }

    /* Animated Bubbles */
    .hero-bubbles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 1;
    }

    .hero-bubble {
      position: absolute;
      bottom: -100px;
      background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.05));
      border-radius: 50%;
      animation: heroRise 15s infinite ease-in;
      box-shadow: inset 0 0 30px rgba(255,255,255,0.2), 0 0 20px rgba(255,255,255,0.1);
      backdrop-filter: blur(2px);
    }

    .hero-bubble::before {
      content: '';
      position: absolute;
      top: 10%;
      left: 20%;
      width: 20%;
      height: 20%;
      background: radial-gradient(circle, rgba(255,255,255,0.6), transparent);
      border-radius: 50%;
    }

    .hero-bubble:nth-child(1)  { width: 80px;  height: 80px;  left: 5%;  animation-duration: 12s; animation-delay: 0s; }
    .hero-bubble:nth-child(2)  { width: 40px;  height: 40px;  left: 15%; animation-duration: 18s; animation-delay: 2s; }
    .hero-bubble:nth-child(3)  { width: 60px;  height: 60px;  left: 25%; animation-duration: 14s; animation-delay: 4s; }
    .hero-bubble:nth-child(4)  { width: 100px; height: 100px; left: 35%; animation-duration: 20s; animation-delay: 1s; }
    .hero-bubble:nth-child(5)  { width: 50px;  height: 50px;  left: 45%; animation-duration: 16s; animation-delay: 3s; }
    .hero-bubble:nth-child(6)  { width: 70px;  height: 70px;  left: 55%; animation-duration: 13s; animation-delay: 5s; }
    .hero-bubble:nth-child(7)  { width: 90px;  height: 90px;  left: 65%; animation-duration: 19s; animation-delay: 2s; }
    .hero-bubble:nth-child(8)  { width: 45px;  height: 45px;  left: 75%; animation-duration: 15s; animation-delay: 4s; }
    .hero-bubble:nth-child(9)  { width: 55px;  height: 55px;  left: 85%; animation-duration: 17s; animation-delay: 1s; }
    .hero-bubble:nth-child(10) { width: 35px;  height: 35px;  left: 92%; animation-duration: 11s; animation-delay: 6s; }
    .hero-bubble:nth-child(11) { width: 65px;  height: 65px;  left: 10%; animation-duration: 22s; animation-delay: 3s; }
    .hero-bubble:nth-child(12) { width: 85px;  height: 85px;  left: 50%; animation-duration: 25s; animation-delay: 0s; }

    @keyframes heroRise {
      0%   { bottom: -100px; transform: translateX(0) scale(1) rotate(0deg); opacity: 0.4; }
      25%  { transform: translateX(50px) scale(1.05) rotate(90deg); opacity: 0.6; }
      50%  { transform: translateX(-30px) scale(0.95) rotate(180deg); opacity: 0.8; }
      75%  { transform: translateX(40px) scale(1.02) rotate(270deg); opacity: 0.5; }
      100% { bottom: 110%; transform: translateX(-20px) scale(0.9) rotate(360deg); opacity: 0; }
    }

    /* Floating Particles */
    .hero-particle {
      position: absolute;
      width: 8px;
      height: 8px;
      background: rgba(255,255,255,0.6);
      border-radius: 50%;
      animation: heroFloat 8s infinite ease-in-out;
    }

    .hero-particle:nth-child(13) { top: 15%; left: 8%;   animation-delay: 0s; }
    .hero-particle:nth-child(14) { top: 25%; left: 20%;  animation-delay: 1s; }
    .hero-particle:nth-child(15) { top: 40%; left: 12%;  animation-delay: 2s; }
    .hero-particle:nth-child(16) { top: 60%; left: 5%;   animation-delay: 1.5s; }
    .hero-particle:nth-child(17) { top: 20%; right: 15%; animation-delay: 0.5s; }
    .hero-particle:nth-child(18) { top: 35%; right: 8%;  animation-delay: 2.5s; }
    .hero-particle:nth-child(19) { top: 55%; right: 12%; animation-delay: 1s; }
    .hero-particle:nth-child(20) { top: 75%; right: 20%; animation-delay: 3s; }

    @keyframes heroFloat {
      0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; box-shadow: 0 0 10px rgba(255,255,255,0.3); }
      50%      { transform: translateY(-25px) scale(1.3); opacity: 1; box-shadow: 0 0 25px rgba(255,255,255,0.6); }
    }

    /* Deco Icons */
    .hero-deco-icon {
      position: absolute;
      font-size: 3rem;
      color: rgba(255,255,255,0.08);
      z-index: 1;
      animation: iconFloat 6s infinite ease-in-out;
    }

    .hero-deco-icon:nth-of-type(1) { top: 12%;  left: 3%;  animation-delay: 0s; }
    .hero-deco-icon:nth-of-type(2) { top: 20%;  right: 5%; animation-delay: 1s; }
    .hero-deco-icon:nth-of-type(3) { bottom: 25%; left: 2%; animation-delay: 2s; }
    .hero-deco-icon:nth-of-type(4) { bottom: 15%; right: 3%; animation-delay: 1.5s; }
    .hero-deco-icon:nth-of-type(5) { top: 50%;  left: 1%;  animation-delay: 0.5s; }
    .hero-deco-icon:nth-of-type(6) { top: 65%;  right: 2%; animation-delay: 2.5s; }

    @keyframes iconFloat {
      0%, 100% { transform: translateY(0); opacity: 0.08; }
      50%      { transform: translateY(-15px); opacity: 0.15; }
    }

    /* Hero Image */
    .hero-image .main-image {
      border-radius: 30px;
      box-shadow: 0 30px 60px rgba(0,0,0,0.4);
      border: 5px solid rgba(255,255,255,0.2);
    }

    .floating-card {
      position: absolute;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 15px 20px;
      box-shadow: 0 15px 40px rgba(0,0,0,0.2);
      animation: cardFloat 3s ease-in-out infinite;
    }

    .emergency-card {
      bottom: 20px;
      left: -30px;
    }

    .floating-card .card-content i {
      font-size: 1.5rem;
      color: var(--blue-primary);
      background: var(--blue-lightest);
      padding: 12px;
      border-radius: 12px;
    }

    .floating-card .label {
      font-weight: 600;
      color: #1e293b;
      font-size: 0.9rem;
    }

    @keyframes cardFloat {
      0%, 100% { transform: translateY(0) rotate(-2deg); }
      50%      { transform: translateY(-10px) rotate(2deg); }
    }

    /* Hero Text */
    .hero-badge {
      background: rgba(255,255,255,0.15);
      color: #fff;
      padding: 10px 24px;
      border-radius: 50px;
      font-size: 0.9rem;
      font-weight: 500;
      border: 1px solid rgba(255,255,255,0.2);
      backdrop-filter: blur(5px);
    }

    .hero-title {
      font-size: 3.2rem;
      font-weight: 700;
      color: #fff;
      line-height: 1.2;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    }

    .hero-description {
      font-size: 1.1rem;
      color: rgba(255,255,255,0.85);
      line-height: 1.8;
      max-width: 550px;
    }

    /* Hero Buttons */
    .btn-hero-primary {
      background: #fff;
      color: var(--blue-dark);
      padding: 15px 35px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .btn-hero-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
      background: var(--blue-lightest);
      color: var(--blue-dark);
    }

    .btn-hero-secondary {
      background: transparent;
      color: #fff;
      padding: 15px 35px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      border: 2px solid rgba(255,255,255,0.4);
    }

    .btn-hero-secondary:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.6);
      color: #fff;
    }

    /* Wave */
    .hero-wave {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      overflow: hidden;
      line-height: 0;
      z-index: 2;
    }

    .hero-wave svg {
      display: block;
      width: calc(100% + 1.3px);
      height: 80px;
    }

    .hero-wave .shape-fill {
      fill: #fff;
    }

    /* ==================== SECTION TITLE ==================== */
    .section-title h2 {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--blue-darkest);
      position: relative;
      display: inline-block;
    }

    .section-title h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, var(--blue-primary), var(--blue-light));
      border-radius: 2px;
    }

    /* ==================== ABOUT SECTION ==================== */
    .section-heading {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--blue-darkest);
    }

    .primary-image {
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }

    .primary-image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }

    .certification-badge {
      position: absolute;
      bottom: -20px;
      right: -20px;
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
      animation: badgePulse 2s infinite;
    }

    .certification-badge i {
      font-size: 2rem;
      color: #fff;
    }

    @keyframes badgePulse {
      0%, 100% { transform: scale(1); }
      50%      { transform: scale(1.05); }
    }

    .small-image {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .small-image img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .small-image:hover img {
      transform: scale(1.05);
    }

    .highlight-box {
      background: linear-gradient(135deg, var(--blue-lightest) 0%, #fff 100%);
      border-radius: 20px;
      border-left: 5px solid var(--blue-primary);
      box-shadow: 0 10px 30px rgba(59, 130, 246, 0.1);
    }

    .highlight-icon {
      width: 60px;
      height: 60px;
      min-width: 60px;
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
      border-radius: 15px;
    }

    .highlight-content h4 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--blue-darkest);
    }

    .feature-item {
      padding: 15px 20px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      border: 1px solid #f1f5f9;
    }

    .feature-item:hover {
      transform: translateX(10px);
      box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
      border-color: var(--blue-lighter);
    }

    .feature-icon {
      width: 40px;
      height: 40px;
      min-width: 40px;
      background: linear-gradient(135deg, var(--blue-lightest) 0%, var(--blue-lighter) 100%);
      border-radius: 10px;
    }

    .feature-icon i {
      color: var(--blue-primary);
    }

    /* ==================== DEPARTMENTS ==================== */
    .featured-departments {
      background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }

    .featured-department {
      background: #fff;
      border-radius: 30px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.08);
      border: 1px solid #f1f5f9;
    }

    .department-category {
      background: linear-gradient(135deg, var(--blue-lightest) 0%, var(--blue-lighter) 100%);
      color: var(--blue-dark);
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .department-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--blue-darkest);
    }

    .department-features .feature-item {
      padding: 0;
      background: transparent;
      box-shadow: none;
      border: none;
    }

    .department-features .feature-item:hover {
      transform: none;
      box-shadow: none;
    }

    .department-features .feature-item i {
      color: var(--blue-primary);
      font-size: 1.2rem;
    }

    .cta-link {
      color: var(--blue-primary);
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .cta-link:hover {
      color: var(--blue-dark);
      gap: 15px !important;
    }

    .department-visual .image-wrapper {
      border-radius: 25px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }

    .department-visual .image-wrapper img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .department-visual .image-wrapper:hover img {
      transform: scale(1.05);
    }

    /* Department Cards */
    .department-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      border: 1px solid #f1f5f9;
      transition: all 0.4s ease;
      overflow: hidden;
    }

    .department-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--blue-primary), var(--blue-light));
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .department-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 25px 50px rgba(59, 130, 246, 0.15);
    }

    .department-card:hover::before {
      transform: scaleX(1);
    }

    .department-card .card-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, var(--blue-lightest) 0%, var(--blue-lighter) 100%);
      border-radius: 18px;
      transition: all 0.3s ease;
    }

    .department-card:hover .card-icon {
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
    }

    .department-card .card-icon i {
      font-size: 1.8rem;
      color: var(--blue-primary);
      transition: color 0.3s ease;
    }

    .department-card:hover .card-icon i {
      color: #fff;
    }

    .department-card .card-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--blue-darkest);
    }

    /* ==================== SERVICES ==================== */
    .featured-services {
      background: linear-gradient(135deg, var(--blue-darkest) 0%, var(--blue-dark) 50%, var(--blue-primary) 100%);
      overflow: hidden;
    }

    .featured-services::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .featured-services .section-title h2 {
      color: #fff;
    }

    .featured-services .section-title h2::after {
      background: linear-gradient(90deg, #fff, rgba(255,255,255,0.5));
    }

    .featured-services .section-title p {
      color: rgba(255,255,255,0.8);
    }

    .service-card {
      background: rgba(255,255,255,0.95);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0,0,0,0.2);
      transition: all 0.4s ease;
    }

    .service-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 30px 60px rgba(0,0,0,0.3);
    }

    .service-card .service-icon {
      position: absolute;
      top: 20px;
      right: 20px;
      width: 55px;
      height: 55px;
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
      border-radius: 15px;
      z-index: 2;
      box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    }

    .service-card .service-image {
      height: 220px;
      overflow: hidden;
    }

    .service-card .service-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .service-card:hover .service-image img {
      transform: scale(1.1);
    }

    .service-card .service-content h3 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--blue-darkest);
    }

    .service-link {
      color: var(--blue-primary);
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .service-link:hover {
      color: var(--blue-dark);
      gap: 12px !important;
    }

    /* ==================== FOOTER ==================== */
    footer {
      background: linear-gradient(180deg, var(--blue-darkest) 0%, #091d3a 100%);
    }

    .footer-about .sitename {
      font-size: 1.8rem;
      font-weight: 700;
      color: #fff;
    }

    .social-links a {
      width: 45px;
      height: 45px;
      background: rgba(255,255,255,0.1);
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .social-links a:hover {
      background: var(--blue-primary);
      transform: translateY(-3px);
    }

    .footer-links h4 {
      font-size: 1.1rem;
      font-weight: 700;
      position: relative;
      padding-bottom: 12px;
    }

    .footer-links h4::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 30px;
      height: 3px;
      background: var(--blue-primary);
      border-radius: 2px;
    }

    .footer-links ul li a {
      color: rgba(255,255,255,0.7);
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .footer-links ul li a:hover {
      color: var(--blue-light);
      padding-left: 5px;
    }

    /* ==================== SCROLL TOP ==================== */
    .scroll-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
      border-radius: 50%;
      color: #fff;
      font-size: 1.5rem;
      box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
      transition: all 0.3s ease;
      z-index: 999;
      opacity: 0;
      visibility: hidden;
    }

    .scroll-top.active {
      opacity: 1;
      visibility: visible;
    }

    .scroll-top:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(59, 130, 246, 0.5);
      color: #fff;
    }

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 992px) {
      .hero-title { font-size: 2.5rem; }
      .hero-content { padding-left: 0; }
      .navmenu ul { display: none; }
      .featured-department { padding: 30px; }
      .department-title { font-size: 1.6rem; }
    }

    @media (max-width: 768px) {
      .hero { min-height: auto; padding: 120px 0 100px; }
      .hero-title { font-size: 2rem; }
      .section-heading { font-size: 1.8rem; }
      .highlight-box { flex-direction: column; }
      
      .profile-dropdown .dropdown-toggle {
        padding: 8px 15px;
      }
      
      .profile-dropdown .dropdown-toggle .user-name-text {
        display: none;
      }
    }
  </style>
</head>

<body class="index-page">

  <!-- ==================== HEADER ==================== -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0 text-decoration-none">
        <h1 class="sitename mb-0">Vet<span>Clinics</span></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul class="d-flex list-unstyled mb-0 align-items-center">
          <li><a href="index.php" class="active position-relative text-decoration-none d-inline-block">Home</a></li>
          <li><a href="about.php" class="text-decoration-none d-inline-block">About</a></li>
          <li><a href="departments.php" class="text-decoration-none d-inline-block">Clinic</a></li>
          <li><a href="services.php" class="text-decoration-none d-inline-block">Services</a></li>
          <li><a href="contact.php" class="text-decoration-none d-inline-block">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <!-- Profile Dropdown -->
      <div class="dropdown profile-dropdown">
        <button class="dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="profile-icon">
            <i class="bi bi-person-fill text-white"></i>
          </div>
          <span class="user-name-text"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li class="dropdown-header">
            <div class="user-name"><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></div>
            <div class="user-email"><?php echo htmlspecialchars($user['email'] ?? 'user@example.com'); ?></div>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="profile.php">
              <i class="bi bi-person"></i> Profile
            </a>  
          </li>

          <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="admin/dashboard.php">
              <i class="bi bi-speedometer2"></i> Admin Dashboard
            </a>
          </li>
          <?php endif; ?>

          <li><hr class="dropdown-divider"></li>

          <li>
            <a href="#" onclick="confirmLogout(event)" class="dropdown-item d-flex align-items-center gap-2 text-danger">
              <i class="bi bi-box-arrow-right"></i> Logout
            </a>
          </li>
        </ul>
      </div>

    </div>
  </header>

  <main class="main">

    <!-- ==================== HERO ==================== -->
    <section id="hero" class="hero section position-relative d-flex align-items-center">

      <!-- Bubbles Background -->
      <div class="hero-bubbles">
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-bubble"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
      </div>

      <!-- Deco Icons -->
      <i class="fas fa-paw hero-deco-icon"></i>
      <i class="fas fa-heartbeat hero-deco-icon"></i>
      <i class="fas fa-stethoscope hero-deco-icon"></i>
      <i class="fas fa-bone hero-deco-icon"></i>
      <i class="fas fa-syringe hero-deco-icon"></i>
      <i class="fas fa-clinic-medical hero-deco-icon"></i>

      <div class="container position-relative z-2">
        <div class="row align-items-center gy-5">

          <!-- Image -->
          <div class="col-lg-5" data-aos="fade-right" data-aos-delay="100">
            <div class="hero-image position-relative">
              <img src="assets/img/health/staff8.jpg" alt="Healthcare Professional"
                   class="img-fluid main-image w-100">

              <div class="floating-card emergency-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-content d-flex align-items-center gap-3">
                  <i class="bi bi-telephone-fill"></i>
                  <div class="text">
                    <span class="label">24/7 Emergency</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
            <div class="hero-content ps-lg-5 mt-4 mt-lg-0 text-center text-lg-start">
              <div class="mb-3">
                <span class="hero-badge d-inline-block">
                  <i class="fas fa-award me-2"></i>Trusted Veterinary Care
                </span>
              </div>

              <h1 class="hero-title mb-4">Compassionate Vet Clinics for Your Pets</h1>

              <p class="hero-description mb-4 mx-auto mx-lg-0">
                Providing professional and compassionate veterinary care for pets of all kinds.
                Our clinics are dedicated to keeping your animals healthy, happy, and safe with
                modern facilities and experienced veterinarians.
              </p>

              <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-lg-start">
                <a href="users/user_home.php" class="btn-hero-primary text-decoration-none d-inline-flex align-items-center gap-2">
                  <i class="fas fa-search"></i> Find a Clinic
                </a>
                <a href="services.php" class="btn-hero-secondary text-decoration-none d-inline-flex align-items-center gap-2">
                  <i class="fas fa-play-circle"></i> Our Services
                </a>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Wave -->
      <div class="hero-wave">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                class="shape-fill"></path>
        </svg>
      </div>
    </section>

    <!-- ==================== ABOUT ==================== -->
    <section id="home-about" class="home-about section py-5 bg-white position-relative overflow-hidden">
      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <!-- Heading -->
        <div class="row" data-aos="fade-up" data-aos-delay="150">
          <div class="col-lg-8 mx-auto text-center mb-5">
            <h2 class="section-heading mb-3">Excellence in Veterinary Care Since 1985</h2>
            <p class="lead-description text-secondary fs-6 mx-auto" style="max-width: 700px;">
              We are dedicated to providing top-quality veterinary services with compassion,
              expertise, and personalized care to ensure the health and happiness of your pets.
            </p>
          </div>
        </div>

        <div class="row align-items-center gy-5">

          <!-- Images -->
          <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
            <div class="image-grid position-relative">
              <div class="primary-image position-relative">
                <img src="assets/img/health/facilities6.webp" alt="Modern hospital facility" class="img-fluid">
                <div class="certification-badge">
                  <i class="bi bi-award"></i>
                </div>
              </div>
              <div class="d-flex gap-3 mt-3">
                <div class="small-image flex-fill">
                  <img src="assets/img/health/consultation3.webp" alt="Doctor consultation" class="img-fluid">
                </div>
                <div class="small-image flex-fill">
                  <img src="assets/img/health/surgery2.webp" alt="Medical procedure" class="img-fluid">
                </div>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="col-lg-5" data-aos="fade-left" data-aos-delay="300">
            <div class="position-relative z-1">

              <!-- Highlight Box -->
              <div class="highlight-box d-flex gap-3 p-4 mb-4">
                <div class="highlight-icon d-flex align-items-center justify-content-center flex-shrink-0">
                  <i class="bi bi-heart-pulse-fill text-white fs-4"></i>
                </div>
                <div class="highlight-content">
                  <h4 class="mb-2">Pet-Centered Care</h4>
                  <p class="text-secondary small mb-0 lh-lg">
                    Each pet receives a personalized treatment plan tailored to their health, breed, age, and medical history.
                  </p>
                </div>
              </div>

              <!-- Feature List -->
              <div class="d-flex flex-column gap-3">
                <div class="feature-item d-flex align-items-center gap-3 bg-white">
                  <div class="feature-icon d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                  </div>
                  <div class="feature-text fw-medium text-secondary small">Advanced diagnostic tools and pet imaging</div>
                </div>
                <div class="feature-item d-flex align-items-center gap-3 bg-white">
                  <div class="feature-icon d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                  </div>
                  <div class="feature-text fw-medium text-secondary small">Experienced and certified veterinarians</div>
                </div>
                <div class="feature-item d-flex align-items-center gap-3 bg-white">
                  <div class="feature-icon d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                  </div>
                  <div class="feature-text fw-medium text-secondary small">Comprehensive rehabilitation and recovery care</div>
                </div>
                <div class="feature-item d-flex align-items-center gap-3 bg-white">
                  <div class="feature-icon d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                  </div>
                  <div class="feature-text fw-medium text-secondary small">24/7 emergency veterinary services</div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== DEPARTMENTS ==================== -->
    <section id="featured-departments" class="featured-departments section py-5">
      <div class="container section-title text-center mb-5" data-aos="fade-up">
        <h2 class="pb-3">Featured Clinics</h2>
        <p class="text-secondary fs-6 mx-auto mt-3" style="max-width: 600px;">
          Discover our network of trusted veterinary clinics providing exceptional care for your beloved pets
        </p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <!-- Featured Department Card -->
        <div class="featured-department p-4 p-lg-5 mb-5" data-aos="fade-up" data-aos-delay="200">
          <div class="row align-items-center gy-4">
            <div class="col-lg-6 order-lg-1">
              <span class="department-category d-inline-block mb-3">Veterinary Care</span>
              <h2 class="department-title mb-3">24/7 Emergency Vet Clinics</h2>
              <p class="text-secondary lh-lg mb-4">
                Our veterinary clinics provide round-the-clock emergency care for pets,
                offering immediate medical attention, advanced treatment, and compassionate
                support when your animals need it most.
              </p>
              <div class="d-flex flex-column gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                  <i class="fas fa-check-circle" style="color: var(--blue-primary); font-size: 1.2rem;"></i>
                  <span class="text-secondary small">24/7 Emergency Pet Care</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                  <i class="fas fa-check-circle" style="color: var(--blue-primary); font-size: 1.2rem;"></i>
                  <span class="text-secondary small">Advanced Diagnostic & Treatment Services</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                  <i class="fas fa-check-circle" style="color: var(--blue-primary); font-size: 1.2rem;"></i>
                  <span class="text-secondary small">Experienced & Licensed Veterinarians</span>
                </div>
              </div>
              <a href="#" class="cta-link text-decoration-none d-inline-flex align-items-center gap-2">
                Find a Vet Clinic <i class="fas fa-arrow-right"></i>
              </a>
            </div>
            <div class="col-lg-6 order-lg-2">
              <div class="department-visual">
                <div class="image-wrapper">
                  <img src="assets/img/health/emergency.webp" alt="Emergency Department" class="img-fluid">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Department Cards Grid -->
        <div class="row g-4 mt-3">
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="department-card position-relative h-100 p-4">
              <div class="card-icon d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-heartbeat"></i>
              </div>
              <h3 class="card-title mb-2">Experienced & Compassionate Veterinarians</h3>
              <p class="card-description text-secondary small mb-0 lh-lg">
                Licensed professionals dedicated to providing gentle, high-quality care for all pets.
              </p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
            <div class="department-card position-relative h-100 p-4">
              <div class="card-icon d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-brain"></i>
              </div>
              <h3 class="card-title mb-2">Modern Diagnostic Equipment</h3>
              <p class="card-description text-secondary small mb-0 lh-lg">
                Advanced tools for accurate and fast diagnosis, including laboratory testing and imaging.
              </p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="department-card position-relative h-100 p-4">
              <div class="card-icon d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-cut"></i>
              </div>
              <h3 class="card-title mb-2">Comprehensive Pet Care Services</h3>
              <p class="card-description text-secondary small mb-0 lh-lg">
                From wellness exams and vaccinations to surgery and grooming — all in one clinic.
              </p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="450">
            <div class="department-card position-relative h-100 p-4">
              <div class="card-icon d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-shield-alt"></i>
              </div>
              <h3 class="card-title mb-2">Clean, Safe & Pet-Friendly Facility</h3>
              <p class="card-description text-secondary small mb-0 lh-lg">
                A hygienic, stress-free environment designed for the comfort of pets and their owners.
              </p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="department-card position-relative h-100 p-4">
              <div class="card-icon d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-clock"></i>
              </div>
              <h3 class="card-title mb-2">Convenient Appointments & Emergency Support</h3>
              <p class="card-description text-secondary small mb-0 lh-lg">
                Flexible scheduling with reliable care when your pet needs it most.
              </p>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="550">
            <div class="department-card position-relative h-100 p-4">
              <div class="card-icon d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-mobile-alt"></i>
              </div>
              <h3 class="card-title mb-2">Online Booking & Records</h3>
              <p class="card-description text-secondary small mb-0 lh-lg">
                Easy appointment scheduling and access to your pet's health records anytime.
              </p>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ==================== SERVICES ==================== -->
    <section id="featured-services" class="featured-services section py-5 position-relative">
      <div class="container section-title text-center mb-5 position-relative z-1" data-aos="fade-up">
        <h2 class="pb-3">Featured Services</h2>
        <p class="mx-auto mt-3" style="max-width: 600px;">
          Comprehensive veterinary services to keep your pets healthy and happy throughout their lives
        </p>
      </div>

      <div class="container position-relative z-1" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-card h-100 position-relative">
              <div class="service-icon d-flex align-items-center justify-content-center">
                <i class="fas fa-heartbeat text-white fs-5"></i>
              </div>
              <div class="service-image">
                <img src="assets/img/health/generalcheckups.jpg" alt="General Check-Ups" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content p-4">
                <h3 class="mb-3">General Check-Ups & Consultations</h3>
                <p class="text-secondary small lh-lg mb-3">
                  Routine health examinations to assess your pet's overall condition, detect early signs of illness, and provide health advice.
                </p>
                <a href="#" class="service-link text-decoration-none d-inline-flex align-items-center gap-2 small">
                  Learn More <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-card h-100 position-relative">
              <div class="service-icon d-flex align-items-center justify-content-center">
                <i class="fas fa-syringe text-white fs-5"></i>
              </div>
              <div class="service-image">
                <img src="assets/img/health/vaccination.jpg" alt="Vaccination" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content p-4">
                <h3 class="mb-3">Vaccination & Preventive Care</h3>
                <p class="text-secondary small lh-lg mb-3">
                  Core and non-core vaccinations, deworming, flea and tick control, and parasite prevention to keep pets healthy.
                </p>
                <a href="#" class="service-link text-decoration-none d-inline-flex align-items-center gap-2 small">
                  Learn More <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-card h-100 position-relative">
              <div class="service-icon d-flex align-items-center justify-content-center">
                <i class="fas fa-microscope text-white fs-5"></i>
              </div>
              <div class="service-image">
                <img src="assets/img/health/diagnostics.jpg" alt="Diagnostics" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content p-4">
                <h3 class="mb-3">Diagnostic Services</h3>
                <p class="text-secondary small lh-lg mb-3">
                  Laboratory tests, blood work, fecal exams, urinalysis, and basic imaging (X-ray, ultrasound) to diagnose illnesses.
                </p>
                <a href="#" class="service-link text-decoration-none d-inline-flex align-items-center gap-2 small">
                  Learn More <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-card h-100 position-relative">
              <div class="service-icon d-flex align-items-center justify-content-center">
                <i class="fas fa-procedures text-white fs-5"></i>
              </div>
              <div class="service-image">
                <img src="assets/img/health/surgical.jpg" alt="Surgical" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content p-4">
                <h3 class="mb-3">Surgical Procedures</h3>
                <p class="text-secondary small lh-lg mb-3">
                  Minor and major surgeries such as spaying/neutering, wound treatment, mass removal, and soft-tissue procedures.
                </p>
                <a href="#" class="service-link text-decoration-none d-inline-flex align-items-center gap-2 small">
                  Learn More <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-card h-100 position-relative">
              <div class="service-icon d-flex align-items-center justify-content-center">
                <i class="fas fa-cut text-white fs-5"></i>
              </div>
              <div class="service-image">
                <img src="assets/img/health/grooming.jpg" alt="Grooming" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content p-4">
                <h3 class="mb-3">Grooming & Basic Pet Care</h3>
                <p class="text-secondary small lh-lg mb-3">
                  Nail trimming, ear cleaning, bathing, and basic grooming to maintain hygiene and comfort.
                </p>
                <a href="#" class="service-link text-decoration-none d-inline-flex align-items-center gap-2 small">
                  Learn More <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="700">
            <div class="service-card h-100 position-relative">
              <div class="service-icon d-flex align-items-center justify-content-center">
                <i class="fas fa-tooth text-white fs-5"></i>
              </div>
              <div class="service-image">
                <img src="assets/img/health/dental.jpg" alt="Dental" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content p-4">
                <h3 class="mb-3">Dental Care</h3>
                <p class="text-secondary small lh-lg mb-3">
                  Professional teeth cleaning, dental examinations, and oral health treatments for optimal pet dental hygiene.
                </p>
                <a href="#" class="service-link text-decoration-none d-inline-flex align-items-center gap-2 small">
                  Learn More <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <!-- ==================== FOOTER ==================== -->
  <footer class="text-white pt-5">
    <div class="container footer-top pb-4 border-bottom border-white border-opacity-10">
      <div class="row gy-4">

        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center text-decoration-none mb-3">
            <span class="sitename">VetClinics</span>
          </a>
          <div class="footer-contact pt-2">
            <p class="text-white-50 small mb-1">123 PawCare Avenue</p>
            <p class="text-white-50 small mb-3">New York, NY 10001</p>
            <p class="text-white-50 small mb-1"><strong class="text-white">Phone:</strong> <span>+1 555 234 5678</span></p>
            <p class="text-white-50 small mb-0"><strong class="text-white">Email:</strong> <span>contact@vetclinics.com</span></p>
          </div>
          <div class="social-links d-flex gap-2 mt-4">
            <a href="#" class="d-flex align-items-center justify-content-center text-white text-decoration-none fs-6">
              <i class="bi bi-twitter-x"></i>
            </a>
            <a href="#" class="d-flex align-items-center justify-content-center text-white text-decoration-none fs-6">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#" class="d-flex align-items-center justify-content-center text-white text-decoration-none fs-6">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#" class="d-flex align-items-center justify-content-center text-white text-decoration-none fs-6">
              <i class="bi bi-linkedin"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4 class="text-white mb-4">Quick Links</h4>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none d-inline-flex align-items-center gap-2">Home</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none d-inline-flex align-items-center gap-2">About Us</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none d-inline-flex align-items-center gap-2">Our Vets</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none d-inline-flex align-items-center gap-2">Appointments</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none d-inline-flex align-items-center gap-2">Contact</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4 class="text-white mb-4">Our Services</h4>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none">General Checkups</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Vaccinations</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Pet Surgery</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Dental Care</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Emergency Care</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4 class="text-white mb-4">Pet Care</h4>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none">Nutrition Advice</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Preventive Care</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Parasite Control</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Senior Pet Care</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Puppy & Kitten Care</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4 class="text-white mb-4">Support</h4>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none">FAQs</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Pet Insurance</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Client Resources</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Terms of Service</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Privacy Policy</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="footer-bottom py-3 text-center text-white-50 small">
      <div class="container">
        <p class="mb-0">&copy; 2024 VetClinics. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center text-decoration-none">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <script>
    // Initialize AOS
    AOS.init({
      duration: 800,
      easing: 'ease-in-out',
      once: true
    });

    // Header scroll effect
    window.addEventListener('scroll', function() {
      const header = document.getElementById('header');
      const scrollTop = document.getElementById('scroll-top');

      if (window.scrollY > 100) {
        header.classList.add('scrolled');
        scrollTop.classList.add('active');
      } else {
        header.classList.remove('scrolled');
        scrollTop.classList.remove('active');
      }
    });

    // Scroll to top
    document.getElementById('scroll-top').addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // SweetAlert Logout Confirmation
    function confirmLogout(event) {
      event.preventDefault();
      Swal.fire({
        title: 'Logout',
        text: 'Are you sure you want to log out?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'logout.php';
        }
      });
    }
  </script>

</body>
</html>