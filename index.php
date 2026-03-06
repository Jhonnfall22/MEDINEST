<?php
session_start();
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Vet Clinic</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* ==================== GLOBAL STYLES ==================== */
    :root {
      --blue-darkest: #0c2d57;
      --blue-dark: #1a56db;
      --blue-primary: #3b82f6;
      --blue-light: #60a5fa;
      --blue-lighter: #93c5fd;
      --blue-lightest: #dbeafe;
      --blue-bg: #eff6ff;
      --white: #ffffff;
      --gray-50: #f8fafc;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-400: #94a3b8;
      --gray-500: #64748b;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-800: #1e293b;
      --gray-900: #0f172a;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      color: var(--gray-700);
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
      color: var(--white);
      text-decoration: none;
    }

    #header .logo .sitename span {
      color: var(--blue-light);
    }

    /* Navigation */
    .navmenu ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 8px;
    }

    .navmenu ul li a {
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.95rem;
      font-weight: 500;
      padding: 10px 18px;
      border-radius: 8px;
      transition: all 0.3s ease;
      text-decoration: none;
      position: relative;
    }

    .navmenu ul li a:hover,
    .navmenu ul li a.active {
      color: var(--white);
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

    /* Login Button */
    .btn-getstarted {
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-light) 100%);
      color: var(--white) !important;
      padding: 12px 28px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.95rem;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-getstarted:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
      background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-primary) 100%);
    }

    .btn-getstarted i {
      font-size: 0.9rem;
    }

    /* ==================== HERO SECTION ==================== */
    .hero {
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      background: linear-gradient(135deg, var(--blue-darkest) 0%, var(--blue-dark) 50%, var(--blue-primary) 100%);
      overflow: hidden;
      padding-top: 100px;
    }

    /* Animated Bubbles Background */
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
      box-shadow: 
        inset 0 0 30px rgba(255,255,255,0.2),
        0 0 20px rgba(255,255,255,0.1);
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

    .hero-bubble:nth-child(1) { width: 80px; height: 80px; left: 5%; animation-duration: 12s; animation-delay: 0s; }
    .hero-bubble:nth-child(2) { width: 40px; height: 40px; left: 15%; animation-duration: 18s; animation-delay: 2s; }
    .hero-bubble:nth-child(3) { width: 60px; height: 60px; left: 25%; animation-duration: 14s; animation-delay: 4s; }
    .hero-bubble:nth-child(4) { width: 100px; height: 100px; left: 35%; animation-duration: 20s; animation-delay: 1s; }
    .hero-bubble:nth-child(5) { width: 50px; height: 50px; left: 45%; animation-duration: 16s; animation-delay: 3s; }
    .hero-bubble:nth-child(6) { width: 70px; height: 70px; left: 55%; animation-duration: 13s; animation-delay: 5s; }
    .hero-bubble:nth-child(7) { width: 90px; height: 90px; left: 65%; animation-duration: 19s; animation-delay: 2s; }
    .hero-bubble:nth-child(8) { width: 45px; height: 45px; left: 75%; animation-duration: 15s; animation-delay: 4s; }
    .hero-bubble:nth-child(9) { width: 55px; height: 55px; left: 85%; animation-duration: 17s; animation-delay: 1s; }
    .hero-bubble:nth-child(10) { width: 35px; height: 35px; left: 92%; animation-duration: 11s; animation-delay: 6s; }
    .hero-bubble:nth-child(11) { width: 65px; height: 65px; left: 10%; animation-duration: 22s; animation-delay: 3s; }
    .hero-bubble:nth-child(12) { width: 85px; height: 85px; left: 50%; animation-duration: 25s; animation-delay: 0s; }

    @keyframes heroRise {
      0% {
        bottom: -100px;
        transform: translateX(0) scale(1) rotate(0deg);
        opacity: 0.4;
      }
      25% {
        transform: translateX(50px) scale(1.05) rotate(90deg);
        opacity: 0.6;
      }
      50% {
        transform: translateX(-30px) scale(0.95) rotate(180deg);
        opacity: 0.8;
      }
      75% {
        transform: translateX(40px) scale(1.02) rotate(270deg);
        opacity: 0.5;
      }
      100% {
        bottom: 110%;
        transform: translateX(-20px) scale(0.9) rotate(360deg);
        opacity: 0;
      }
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

    .hero-particle:nth-child(13) { top: 15%; left: 8%; animation-delay: 0s; }
    .hero-particle:nth-child(14) { top: 25%; left: 20%; animation-delay: 1s; }
    .hero-particle:nth-child(15) { top: 40%; left: 12%; animation-delay: 2s; }
    .hero-particle:nth-child(16) { top: 60%; left: 5%; animation-delay: 1.5s; }
    .hero-particle:nth-child(17) { top: 20%; right: 15%; animation-delay: 0.5s; }
    .hero-particle:nth-child(18) { top: 35%; right: 8%; animation-delay: 2.5s; }
    .hero-particle:nth-child(19) { top: 55%; right: 12%; animation-delay: 1s; }
    .hero-particle:nth-child(20) { top: 75%; right: 20%; animation-delay: 3s; }

    @keyframes heroFloat {
      0%, 100% { 
        transform: translateY(0) scale(1); 
        opacity: 0.4;
        box-shadow: 0 0 10px rgba(255,255,255,0.3);
      }
      50% { 
        transform: translateY(-25px) scale(1.3); 
        opacity: 1;
        box-shadow: 0 0 25px rgba(255,255,255,0.6);
      }
    }

    /* Decorative Icons */
    .hero-deco-icon {
      position: absolute;
      font-size: 3rem;
      color: rgba(255,255,255,0.08);
      z-index: 1;
      animation: iconFloat 6s infinite ease-in-out;
    }

    .hero-deco-icon:nth-of-type(1) { top: 12%; left: 3%; transform: rotate(-15deg); animation-delay: 0s; }
    .hero-deco-icon:nth-of-type(2) { top: 20%; right: 5%; transform: rotate(20deg); animation-delay: 1s; }
    .hero-deco-icon:nth-of-type(3) { bottom: 25%; left: 2%; transform: rotate(-10deg); animation-delay: 2s; }
    .hero-deco-icon:nth-of-type(4) { bottom: 15%; right: 3%; transform: rotate(15deg); animation-delay: 1.5s; }
    .hero-deco-icon:nth-of-type(5) { top: 50%; left: 1%; transform: rotate(25deg); animation-delay: 0.5s; }
    .hero-deco-icon:nth-of-type(6) { top: 65%; right: 2%; transform: rotate(-20deg); animation-delay: 2.5s; }

    @keyframes iconFloat {
      0%, 100% { transform: translateY(0) rotate(var(--rotate, 0deg)); opacity: 0.08; }
      50% { transform: translateY(-15px) rotate(var(--rotate, 0deg)); opacity: 0.15; }
    }

    /* Hero Content */
    .hero .container {
      position: relative;
      z-index: 2;
    }

    .hero-image {
      position: relative;
    }

    .hero-image .main-image {
      border-radius: 30px;
      box-shadow: 0 30px 60px rgba(0,0,0,0.4);
      border: 5px solid rgba(255,255,255,0.2);
    }

  

    .hero-image .floating-card {
      position: absolute;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 15px 20px;
      box-shadow: 0 15px 40px rgba(0,0,0,0.2);
      animation: cardFloat 3s ease-in-out infinite;
    }

    .hero-image .emergency-card {
      bottom: 20px;
      left: -30px;
    }

    .hero-image .floating-card .card-content {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .hero-image .floating-card .card-content i {
      font-size: 1.5rem;
      color: var(--blue-primary);
      background: var(--blue-lightest);
      padding: 12px;
      border-radius: 12px;
    }

    .hero-image .floating-card .label {
      font-weight: 600;
      color: var(--gray-800);
      font-size: 0.9rem;
    }

    @keyframes cardFloat {
      0%, 100% { transform: translateY(0) rotate(-2deg); }
      50% { transform: translateY(-10px) rotate(2deg); }
    }

    .hero-content {
      padding-left: 40px;
    }

    .hero-badge {
      display: inline-block;
      background: rgba(255,255,255,0.15);
      color: var(--white);
      padding: 10px 24px;
      border-radius: 50px;
      font-size: 0.9rem;
      font-weight: 500;
      margin-bottom: 25px;
      border: 1px solid rgba(255,255,255,0.2);
      backdrop-filter: blur(5px);
    }

    .hero-title {
      font-size: 3.2rem;
      font-weight: 700;
      color: var(--white);
      line-height: 1.2;
      margin-bottom: 25px;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    }

    .hero-description {
      font-size: 1.1rem;
      color: rgba(255,255,255,0.85);
      line-height: 1.8;
      margin-bottom: 35px;
      max-width: 550px;
    }

    .hero-buttons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn-hero-primary {
      background: var(--white);
      color: var(--blue-dark);
      padding: 15px 35px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
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
      color: var(--white);
      padding: 15px 35px;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      border: 2px solid rgba(255,255,255,0.4);
    }

    .btn-hero-secondary:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.6);
      color: var(--white);
    }

    /* Wave Divider */
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
      position: relative;
      display: block;
      width: calc(100% + 1.3px);
      height: 80px;
    }

    .hero-wave .shape-fill {
      fill: var(--white);
    }

    /* ==================== SECTION STYLES ==================== */
    .section {
      padding: 100px 0;
      position: relative;
    }

    .section-title {
      text-align: center;
      margin-bottom: 60px;
    }

    .section-title h2 {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--blue-darkest);
      margin-bottom: 15px;
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

    .section-title p {
      color: var(--gray-500);
      font-size: 1.1rem;
      max-width: 600px;
      margin: 20px auto 0;
    }

    /* ==================== HOME ABOUT SECTION ==================== */
    .home-about {
      background: var(--white);
      position: relative;
      overflow: hidden;
    }

    .home-about::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 40%;
      height: 100%;
      z-index: 0;
    }

    .section-heading {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--blue-darkest);
      margin-bottom: 20px;
    }

    .lead-description {
      color: var(--gray-500);
      font-size: 1.1rem;
      max-width: 700px;
      margin: 0 auto;
    }

    .image-grid {
      position: relative;
    }

    .image-grid .primary-image {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }

    .image-grid .primary-image img {
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
      color: var(--white);
    }

    @keyframes badgePulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    .secondary-images {
      display: flex;
      gap: 20px;
      margin-top: 20px;
    }

    .secondary-images .small-image {
      flex: 1;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .secondary-images .small-image img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .secondary-images .small-image:hover img {
      transform: scale(1.05);
    }

    .content-wrapper {
      position: relative;
      z-index: 1;
    }

    .highlight-box {
      background: linear-gradient(135deg, var(--blue-lightest) 0%, var(--white) 100%);
      border-radius: 20px;
      padding: 30px;
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
      border-left: 5px solid var(--blue-primary);
      box-shadow: 0 10px 30px rgba(59, 130, 246, 0.1);
    }

    .highlight-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .highlight-icon i {
      font-size: 1.8rem;
      color: var(--white);
    }

    .highlight-content h4 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--blue-darkest);
      margin-bottom: 10px;
    }

    .highlight-content p {
      color: var(--gray-500);
      font-size: 0.95rem;
      margin: 0;
      line-height: 1.6;
    }

    .feature-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px 20px;
      background: var(--white);
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      border: 1px solid var(--gray-100);
    }

    .feature-item:hover {
      transform: translateX(10px);
      box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
      border-color: var(--blue-lighter);
    }

    .feature-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, var(--blue-lightest) 0%, var(--blue-lighter) 100%);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .feature-icon i {
      font-size: 1rem;
      color: var(--blue-primary);
    }

    .feature-text {
      font-size: 0.95rem;
      font-weight: 500;
      color: var(--gray-700);
    }

    /* ==================== FEATURED DEPARTMENTS SECTION ==================== */
    .featured-departments {
      background: linear-gradient(180deg, var(--gray-50) 0%, var(--white) 100%);
      position: relative;
    }

    .featured-department {
      background: var(--white);
      border-radius: 30px;
      padding: 50px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.08);
      margin-bottom: 50px;
      border: 1px solid var(--gray-100);
    }

    .department-category {
      display: inline-block;
      background: linear-gradient(135deg, var(--blue-lightest) 0%, var(--blue-lighter) 100%);
      color: var(--blue-dark);
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .department-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--blue-darkest);
      margin-bottom: 20px;
    }

    .department-description {
      color: var(--gray-500);
      font-size: 1rem;
      line-height: 1.8;
      margin-bottom: 25px;
    }

    .department-features {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 30px;
    }

    .department-features .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
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

    .department-features .feature-item span {
      color: var(--gray-600);
      font-size: 0.95rem;
    }

    .cta-link {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      color: var(--blue-primary);
      font-weight: 600;
      text-decoration: none;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .cta-link:hover {
      color: var(--blue-dark);
      gap: 15px;
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
    .departments-grid {
      margin-top: 30px;
    }

    .department-card {
      background: var(--white);
      border-radius: 20px;
      padding: 35px 30px;
      height: 100%;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      border: 1px solid var(--gray-100);
      transition: all 0.4s ease;
      position: relative;
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
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 25px;
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
      color: var(--white);
    }

    .department-card .card-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--blue-darkest);
      margin-bottom: 15px;
    }

    .department-card .card-description {
      color: var(--gray-500);
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 0;
    }

    /* ==================== FEATURED SERVICES SECTION ==================== */
    .featured-services {
      background: linear-gradient(135deg, var(--blue-darkest) 0%, var(--blue-dark) 50%, var(--blue-primary) 100%);
      position: relative;
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
      color: var(--white);
    }

    .featured-services .section-title h2::after {
      background: linear-gradient(90deg, var(--white), rgba(255,255,255,0.5));
    }

    .featured-services .section-title p {
      color: rgba(255,255,255,0.8);
    }

    .service-card {
      background: rgba(255,255,255,0.95);
      border-radius: 24px;
      overflow: hidden;
      height: 100%;
      box-shadow: 0 20px 50px rgba(0,0,0,0.2);
      transition: all 0.4s ease;
      position: relative;
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
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    }

    .service-card .service-icon i {
      font-size: 1.4rem;
      color: var(--white);
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

    .service-card .service-content {
      padding: 30px;
    }

    .service-card .service-content h3 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--blue-darkest);
      margin-bottom: 15px;
    }

    .service-card .service-content p {
      color: var(--gray-500);
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .service-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--blue-primary);
      font-weight: 600;
      text-decoration: none;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .service-link:hover {
      color: var(--blue-dark);
      gap: 12px;
    }

    /* ==================== FOOTER ==================== */
    footer {
      background: linear-gradient(180deg, var(--blue-darkest) 0%, #091d3a 100%);
      color: var(--white);
      padding-top: 80px;
    }

    .footer-top {
      padding-bottom: 50px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .footer-about .logo {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
    }

    .footer-about .sitename {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--white);
    }

    .footer-contact p {
      color: rgba(255,255,255,0.7);
      margin-bottom: 8px;
      font-size: 0.95rem;
    }

    .footer-contact strong {
      color: var(--white);
    }

    .social-links {
      display: flex;
      gap: 12px;
    }

    .social-links a {
      width: 45px;
      height: 45px;
      background: rgba(255,255,255,0.1);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }

    .social-links a:hover {
      background: var(--blue-primary);
      transform: translateY(-3px);
    }

    .footer-links h4 {
      color: var(--white);
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 25px;
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

    .footer-links ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links ul li {
      margin-bottom: 12px;
    }

    .footer-links ul li a {
      color: rgba(255,255,255,0.7);
      text-decoration: none;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .footer-links ul li a:hover {
      color: var(--blue-light);
      padding-left: 5px;
    }

    .footer-bottom {
      padding: 25px 0;
      text-align: center;
      color: rgba(255,255,255,0.6);
      font-size: 0.9rem;
    }

    /* ==================== SCROLL TO TOP ==================== */
    .scroll-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, var(--blue-primary) 0%, var(--blue-dark) 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
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
      color: var(--white);
    }

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 992px) {
      .hero-title {
        font-size: 2.5rem;
      }

      .hero-content {
        padding-left: 0;
        margin-top: 40px;
        text-align: center;
      }

      .hero-description {
        margin: 0 auto 35px;
      }

      .hero-buttons {
        justify-content: center;
      }

      .navmenu ul {
        display: none;
      }

      .featured-department {
        padding: 30px;
      }

      .department-title {
        font-size: 1.6rem;
      }
    }

    @media (max-width: 768px) {
      .hero {
        min-height: auto;
        padding: 120px 0 100px;
      }

      .hero-title {
        font-size: 2rem;
      }

      .hero-buttons {
        flex-direction: column;
        align-items: center;
      }

      .section {
        padding: 60px 0;
      }

      .section-heading {
        font-size: 1.8rem;
      }

      .highlight-box {
        flex-direction: column;
        text-align: center;
      }

      .secondary-images {
        flex-direction: column;
      }
    }

    /* ==================== MODAL STYLES (PRESERVED) ==================== */
    .login-modal .modal-dialog {
      max-width: 880px;
      margin: 20px auto;
    }

    .login-modal .modal-content {
      border: none;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
      background: transparent;
    }

    .login-modal .modal-header,
    .login-modal .modal-footer {
      display: none;
    }

    .login-modal .modal-body {
      padding: 0;
    }

    .modal-login-wrapper {
      display: flex;
      min-height: 520px;
      font-family: 'Poppins', sans-serif;
    }

    .modal-left-section {
      flex: 0.85;
      background: linear-gradient(135deg, #0c2d57 0%, #1a56db 50%, #3b82f6 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 30px;
      position: relative;
      overflow: hidden;
    }

    .modal-left-section::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      animation: patternFloat 25s linear infinite;
    }

    .modal-bubble {
      position: absolute;
      background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.08));
      border-radius: 50%;
      box-shadow: inset 0 0 20px rgba(255,255,255,0.2), 0 0 15px rgba(255,255,255,0.1);
      backdrop-filter: blur(2px);
    }

    .modal-bubble::before {
      content: '';
      position: absolute;
      top: 15%;
      left: 20%;
      width: 25%;
      height: 25%;
      background: radial-gradient(circle, rgba(255,255,255,0.7), transparent);
      border-radius: 50%;
    }

    .modal-bubble:nth-child(1) { width: 35px; height: 35px; top: 8%; left: 12%; animation: bubbleFloat1 7s infinite ease-in-out; }
    .modal-bubble:nth-child(2) { width: 25px; height: 25px; top: 18%; right: 10%; animation: bubbleFloat2 9s infinite ease-in-out 1s; }
    .modal-bubble:nth-child(3) { width: 45px; height: 45px; bottom: 25%; left: 8%; animation: bubbleFloat3 8s infinite ease-in-out 0.5s; }
    .modal-bubble:nth-child(4) { width: 20px; height: 20px; bottom: 12%; right: 15%; animation: bubbleFloat1 6s infinite ease-in-out 2s; }
    .modal-bubble:nth-child(5) { width: 30px; height: 30px; top: 45%; right: 5%; animation: bubbleFloat2 10s infinite ease-in-out 1.5s; }
    .modal-bubble:nth-child(6) { width: 18px; height: 18px; top: 65%; left: 15%; animation: bubbleFloat3 7s infinite ease-in-out 3s; }
    .modal-bubble:nth-child(7) { width: 40px; height: 40px; bottom: 40%; right: 8%; animation: bubbleFloat1 11s infinite ease-in-out 0.8s; }

    .modal-particle {
      position: absolute;
      width: 5px;
      height: 5px;
      background: rgba(255,255,255,0.5);
      border-radius: 50%;
      animation: particleGlow 4s infinite ease-in-out;
    }

    .modal-particle:nth-child(8) { top: 15%; left: 25%; animation-delay: 0s; }
    .modal-particle:nth-child(9) { top: 35%; right: 20%; animation-delay: 1s; }
    .modal-particle:nth-child(10) { bottom: 35%; left: 20%; animation-delay: 2s; }
    .modal-particle:nth-child(11) { bottom: 20%; right: 25%; animation-delay: 1.5s; }

    .modal-deco-icon {
      position: absolute;
      font-size: 1.6rem;
      color: rgba(255,255,255,0.12);
      z-index: 1;
      animation: iconPulse 4s infinite ease-in-out;
    }

    .modal-deco-icon:nth-of-type(1) { top: 6%; left: 8%; transform: rotate(-20deg); animation-delay: 0s; }
    .modal-deco-icon:nth-of-type(2) { top: 12%; right: 8%; transform: rotate(15deg); animation-delay: 1s; }
    .modal-deco-icon:nth-of-type(3) { bottom: 18%; left: 6%; transform: rotate(-10deg); animation-delay: 2s; }
    .modal-deco-icon:nth-of-type(4) { bottom: 8%; right: 10%; transform: rotate(25deg); animation-delay: 1.5s; }

    @keyframes iconPulse {
      0%, 100% { opacity: 0.12; transform: scale(1); }
      50% { opacity: 0.2; transform: scale(1.1); }
    }

    .modal-pet-image {
      position: relative;
      z-index: 2;
      margin-bottom: 25px;
    }

    .modal-pet-image img {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid rgba(255,255,255,0.3);
      box-shadow: 0 15px 35px rgba(0,0,0,0.3), 0 0 0 12px rgba(255,255,255,0.08);
      animation: imageGlow 3.5s ease-in-out infinite;
    }

    @keyframes imageGlow {
      0%, 100% { box-shadow: 0 15px 35px rgba(0,0,0,0.3), 0 0 0 12px rgba(255,255,255,0.08); }
      50% { box-shadow: 0 20px 45px rgba(0,0,0,0.4), 0 0 0 18px rgba(255,255,255,0.15), 0 0 50px rgba(59, 130, 246, 0.25); }
    }

    .modal-left-section h4 {
      color: white;
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 12px;
      text-align: center;
      position: relative;
      z-index: 2;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.2);
    }

    .modal-left-section > p {
      color: rgba(255,255,255,0.9);
      font-size: 0.9rem;
      text-align: center;
      position: relative;
      z-index: 2;
      max-width: 240px;
      line-height: 1.55;
      margin: 0;
    }

    .modal-feature-list {
      list-style: none;
      padding: 0;
      margin: 25px 0 0 0;
      position: relative;
      z-index: 2;
    }

    .modal-feature-list li {
      color: rgba(255,255,255,0.95);
      padding: 9px 0;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.85rem;
    }

    .modal-feature-list li i {
      width: 30px;
      height: 30px;
      background: rgba(255,255,255,0.18);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      transition: all 0.3s ease;
    }

    .modal-feature-list li:hover i {
      background: rgba(255,255,255,0.3);
      transform: scale(1.1);
    }

    .modal-right-section {
      flex: 1;
      padding: 40px 45px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: white;
      position: relative;
    }

    .modal-close-custom {
      position: absolute;
      top: 18px;
      right: 18px;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      border: 2px solid #e2e8f0;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 10;
      color: #64748b;
    }

    .modal-close-custom:hover {
      background: #fee2e2;
      border-color: #fecaca;
      color: #ef4444;
      transform: rotate(90deg);
    }

    .modal-form-header {
      margin-bottom: 28px;
    }

    .modal-form-header .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 22px;
    }

    .modal-form-header .brand-icon {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #dbeafe, #bfdbfe);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-form-header .brand-icon i {
      font-size: 1.6rem;
      color: #1a56db;
    }

    .modal-form-header .brand-text {
      font-size: 1.25rem;
      font-weight: 700;
      color: #0c2d57;
    }

    .modal-form-header h5 {
      font-size: 1.6rem;
      font-weight: 700;
      color: #0c2d57;
      margin: 0 0 6px 0;
    }

    .modal-form-header p {
      color: #64748b;
      font-size: 0.92rem;
      margin: 0;
    }

    .modal-security-badge {
      background: linear-gradient(135deg, #dbeafe, #eff6ff);
      color: #1e40af;
      padding: 12px 16px;
      border-radius: 12px;
      font-size: 0.82rem;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 22px;
      border-left: 4px solid #3b82f6;
    }

    .modal-security-badge i {
      font-size: 1.1rem;
      color: #1a56db;
    }

    .modal-error-alert {
      background: linear-gradient(135deg, #fee2e2, #fef2f2);
      border: none;
      border-left: 4px solid #ef4444;
      border-radius: 12px;
      padding: 14px 16px;
      color: #991b1b;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
      animation: alertShake 0.5s ease;
    }

    .modal-error-alert i {
      font-size: 1.2rem;
      color: #ef4444;
    }

    @keyframes alertShake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
      20%, 40%, 60%, 80% { transform: translateX(4px); }
    }

    .modal-login-form .form-group {
      margin-bottom: 18px;
    }

    .modal-login-form .form-label {
      font-weight: 600;
      color: #1e40af;
      font-size: 0.88rem;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .modal-login-form .form-label i {
      font-size: 0.75rem;
      color: #3b82f6;
    }

    .modal-login-form .form-control {
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 18px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background-color: #f8fafc;
      font-family: 'Poppins', sans-serif;
    }

    .modal-login-form .form-control:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
      background-color: white;
      outline: none;
    }

    .modal-login-form .form-control:hover:not(:focus) {
      border-color: #93c5fd;
    }

    .modal-login-form .form-control::placeholder {
      color: #94a3b8;
    }

    .modal-input-wrapper {
      position: relative;
    }

    .modal-input-wrapper .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      font-size: 0.95rem;
      transition: color 0.3s ease;
      z-index: 5;
    }

    .modal-input-wrapper .form-control {
      padding-left: 48px;
    }

    .modal-input-wrapper.focused .input-icon {
      color: #3b82f6;
    }

    .modal-input-wrapper .password-toggle {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      cursor: pointer;
      font-size: 0.95rem;
      transition: color 0.3s ease;
      z-index: 5;
    }

    .modal-input-wrapper .password-toggle:hover {
      color: #3b82f6;
    }

    .modal-form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
    }

    .modal-remember-check {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .modal-remember-check input[type="checkbox"] {
      width: 18px;
      height: 18px;
      border: 2px solid #cbd5e1;
      border-radius: 5px;
      cursor: pointer;
      accent-color: #1a56db;
      margin: 0;
    }

    .modal-remember-check label {
      color: #64748b;
      font-size: 0.88rem;
      cursor: pointer;
      margin: 0;
      user-select: none;
    }

    .modal-forgot-link {
      color: #1a56db;
      font-size: 0.88rem;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .modal-forgot-link:hover {
      color: #3b82f6;
      text-decoration: underline;
    }

    .modal-btn-login {
      background: linear-gradient(135deg, #1a56db 0%, #3b82f6 100%);
      border: none;
      border-radius: 12px;
      padding: 15px;
      font-size: 1rem;
      font-weight: 600;
      color: white;
      width: 100%;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-family: 'Poppins', sans-serif;
    }

    .modal-btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
      transition: left 0.5s ease;
    }

    .modal-btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(26, 86, 219, 0.4);
      background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    }

    .modal-btn-login:hover::before {
      left: 100%;
    }

    .modal-btn-login:active {
      transform: translateY(0);
    }

    .modal-btn-login:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }

    .modal-register-link {
      text-align: center;
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid #e2e8f0;
      color: #64748b;
      font-size: 0.92rem;
    }

    .modal-register-link a {
      color: #1a56db;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .modal-register-link a:hover {
      color: #3b82f6;
      text-decoration: underline;
    }

    .modal-animate-row {
      animation: modalFadeInUp 0.45s ease forwards;
      opacity: 0;
    }

    .modal-animate-row:nth-child(1) { animation-delay: 0.05s; }
    .modal-animate-row:nth-child(2) { animation-delay: 0.1s; }
    .modal-animate-row:nth-child(3) { animation-delay: 0.15s; }
    .modal-animate-row:nth-child(4) { animation-delay: 0.2s; }
    .modal-animate-row:nth-child(5) { animation-delay: 0.25s; }
    .modal-animate-row:nth-child(6) { animation-delay: 0.3s; }
    .modal-animate-row:nth-child(7) { animation-delay: 0.35s; }
    .modal-animate-row:nth-child(8) { animation-delay: 0.4s; }

    @keyframes modalFadeInUp {
      from { opacity: 0; transform: translateY(18px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-modal.show .modal-content {
      animation: modalZoomIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes modalZoomIn {
      from { opacity: 0; transform: scale(0.85) translateY(-40px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes wrapperShake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .modal-login-wrapper.shake {
      animation: wrapperShake 0.5s ease;
    }

    @media (max-width: 768px) {
      .login-modal .modal-dialog {
        max-width: 95%;
        margin: 15px auto;
      }

      .modal-login-wrapper {
        flex-direction: column;
        min-height: auto;
      }

      .modal-left-section {
        min-height: 220px;
        padding: 30px 25px;
      }

      .modal-pet-image img {
        width: 90px;
        height: 90px;
      }

      .modal-left-section h4 {
        font-size: 1.3rem;
      }

      .modal-left-section > p {
        font-size: 0.85rem;
      }

      .modal-feature-list {
        display: none;
      }

      .modal-right-section {
        padding: 30px 28px;
      }

      .modal-form-header h5 {
        font-size: 1.4rem;
      }

      .modal-form-options {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
      }
    }

    @media (max-width: 480px) {
      .modal-right-section {
        padding: 25px 22px;
      }

      .modal-left-section {
        min-height: 180px;
        padding: 25px 20px;
      }

      .modal-pet-image img {
        width: 75px;
        height: 75px;
      }

      .modal-form-header .brand {
        margin-bottom: 18px;
      }

      .modal-form-header .brand-icon {
        width: 42px;
        height: 42px;
      }

      .modal-form-header .brand-icon i {
        font-size: 1.3rem;
      }

      .modal-security-badge {
        font-size: 0.78rem;
        padding: 10px 14px;
      }
    }

    .login-modal.show {
      background: rgba(12, 45, 87, 0.6);
      backdrop-filter: blur(4px);
    }
  </style>
</head>

<body class="index-page">

  <!-- ==================== HEADER ==================== -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">Vet<span>Clinics</span></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="departments.php">Clinic</a></li>
          <li><a href="services.php">Services</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
        <i class="fas fa-sign-in-alt"></i> Log in
      </a>

    </div>
  </header>

  <!-- ==================== LOGIN MODAL ==================== -->
  <div class="modal fade login-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-login-wrapper" id="modalLoginWrapper">
            
            <!-- Left Section -->
            <div class="modal-left-section">
              <div class="modal-bubble"></div>
              <div class="modal-bubble"></div>
              <div class="modal-bubble"></div>
              <div class="modal-bubble"></div>
              <div class="modal-bubble"></div>
              <div class="modal-bubble"></div>
              <div class="modal-bubble"></div>
              
              <div class="modal-particle"></div>
              <div class="modal-particle"></div>
              <div class="modal-particle"></div>
              <div class="modal-particle"></div>
              
              <i class="fas fa-paw modal-deco-icon"></i>
              <i class="fas fa-heartbeat modal-deco-icon"></i>
              <i class="fas fa-stethoscope modal-deco-icon"></i>
              <i class="fas fa-bone modal-deco-icon"></i>
              
              <div class="modal-pet-image">
                <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=300&h=300&fit=crop&crop=face" alt="Happy Pet">
              </div>
              
              <h4>Welcome Back!</h4>
              <p>Sign in to manage your pet's health records and appointments.</p>
              
              <ul class="modal-feature-list">
                <li><i class="fas fa-calendar-check"></i> Quick Appointments</li>
                <li><i class="fas fa-file-medical"></i> Health Records</li>
                <li><i class="fas fa-bell"></i> Smart Reminders</li>
              </ul>
            </div>

            <!-- Right Section - Form -->
            <div class="modal-right-section">
              <button type="button" class="modal-close-custom" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
              </button>

              <div class="modal-form-header modal-animate-row">
                <div class="brand">
                  <div class="brand-icon"><i class="fas fa-paw"></i></div>
                  <span class="brand-text">VetClinic</span>
                </div>
                <h5>Sign In</h5>
                <p>Enter your credentials to continue</p>
              </div>

              <div class="modal-security-badge modal-animate-row">
                <i class="fas fa-shield-alt"></i>
                <span>Secure login with 256-bit SSL encryption</span>
              </div>

              <?php if(isset($login_error) && $login_error): ?>
                <div class="modal-error-alert modal-animate-row">
                  <i class="fas fa-exclamation-circle"></i>
                  <span><?php echo $login_error; ?></span>
                </div>
              <?php endif; ?>

              <form id="loginForm" class="modal-login-form" method="POST" action="login_process.php" novalidate>
                <div class="form-group modal-animate-row">
                  <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
                  <div class="modal-input-wrapper">
                    <i class="fas fa-at input-icon"></i>
                    <input type="email" class="form-control" name="email" id="loginEmail" placeholder="your.email@example.com" required autocomplete="email">
                  </div>
                </div>

                <div class="form-group modal-animate-row">
                  <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                  <div class="modal-input-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password" class="form-control" name="password" id="loginPassword" placeholder="Enter your password" required autocomplete="current-password">
                    <i class="fas fa-eye password-toggle" id="toggleLoginPassword"></i>
                  </div>
                </div>

                <div class="modal-form-options modal-animate-row">
                  <div class="modal-remember-check">
                    <input type="checkbox" id="rememberMe" name="remember">
                    <label for="rememberMe">Remember me</label>
                  </div>
                  <a href="forgot_password.php" class="modal-forgot-link">
                    <i class="fas fa-question-circle"></i> Forgot password?
                  </a>
                </div>

                <button type="submit" class="modal-btn-login modal-animate-row" id="loginSubmitBtn">
                  <i class="fas fa-sign-in-alt"></i>
                  <span>Sign In</span>
                </button>
              </form>

              <div class="modal-register-link modal-animate-row">
                Don't have an account? <a href="register.php">Register here</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <main class="main">

    <!-- ==================== HERO SECTION ==================== -->
    <section id="hero" class="hero section">

      <!-- Decorative Icons -->
      <i class="fas fa-paw hero-deco-icon"></i>
      <i class="fas fa-heartbeat hero-deco-icon"></i>
      <i class="fas fa-stethoscope hero-deco-icon"></i>
      <i class="fas fa-bone hero-deco-icon"></i>
      <i class="fas fa-syringe hero-deco-icon"></i>
      <i class="fas fa-clinic-medical hero-deco-icon"></i>

      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-5">
            <div class="hero-image" data-aos="fade-right" data-aos-delay="100">
              <img src="assets/img/health/staff8.jpg" alt="Healthcare Professional" class="img-fluid main-image">
              <div class="floating-card emergency-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-content">
                  <i class="bi bi-telephone-fill"></i>
                  <div class="text">
                    <span class="label">24/7 Emergency</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="hero-content" data-aos="fade-left" data-aos-delay="200">
              <div class="badge-container">
                <span class="hero-badge"><i class="fas fa-award me-2"></i>Trusted Veterinary Care</span>
              </div>

              <h1 class="hero-title">Compassionate Vet Clinics for Your Pets</h1>
              <p class="hero-description">
                Providing professional and compassionate veterinary care for pets of all kinds.
                Our clinics are dedicated to keeping your animals healthy, happy, and safe with
                modern facilities and experienced veterinarians.
              </p>

              <div class="hero-buttons">
                <a href="departments.php" class="btn-hero-primary">
                  <i class="fas fa-search"></i> Find a Clinic
                </a>
                <a href="services.php" class="btn-hero-secondary">
                  <i class="fas fa-play-circle"></i> Our Services
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Wave Divider -->
      <div class="hero-wave">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
        </svg>
      </div>
    </section>

    <!-- ==================== HOME ABOUT SECTION ==================== -->
    <section id="home-about" class="home-about section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-lg-8 mx-auto text-center mb-5" data-aos="fade-up" data-aos-delay="150">
            <h2 class="section-heading">Excellence in Veterinary Care Since 1985</h2>
            <p class="lead-description">
              We are dedicated to providing top-quality veterinary services with compassion,
              expertise, and personalized care to ensure the health and happiness of your pets.
            </p>
          </div>
        </div>

        <div class="row align-items-center gy-5">
          <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
            <div class="image-grid">
              <div class="primary-image">
                <img src="assets/img/health/facilities6.webp" alt="Modern hospital facility" class="img-fluid">
                <div class="certification-badge">
                  <i class="bi bi-award"></i>
                </div>
              </div>
              <div class="secondary-images">
                <div class="small-image">
                  <img src="assets/img/health/consultation3.webp" alt="Doctor consultation" class="img-fluid">
                </div>
                <div class="small-image">
                  <img src="assets/img/health/surgery2.webp" alt="Medical procedure" class="img-fluid">
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5" data-aos="fade-left" data-aos-delay="300">
            <div class="content-wrapper">
              <div class="highlight-box">
                <div class="highlight-icon">
                  <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="highlight-content">
                  <h4>Pet-Centered Care</h4>
                  <p>Each pet receives a personalized treatment plan tailored to their health, breed, age, and medical history.</p>
                </div>
              </div>

              <div class="feature-list">
                <div class="feature-item">
                  <div class="feature-icon"><i class="bi bi-check-circle-fill"></i></div>
                  <div class="feature-text">Advanced diagnostic tools and pet imaging</div>
                </div>
                <div class="feature-item">
                  <div class="feature-icon"><i class="bi bi-check-circle-fill"></i></div>
                  <div class="feature-text">Experienced and certified veterinarians</div>
                </div>
                <div class="feature-item">
                  <div class="feature-icon"><i class="bi bi-check-circle-fill"></i></div>
                  <div class="feature-text">Comprehensive rehabilitation and recovery care</div>
                </div>
                <div class="feature-item">
                  <div class="feature-icon"><i class="bi bi-check-circle-fill"></i></div>
                  <div class="feature-text">24/7 emergency veterinary services</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== FEATURED DEPARTMENTS SECTION ==================== -->
    <section id="featured-departments" class="featured-departments section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Featured Clinics</h2>
        <p>Discover our network of trusted veterinary clinics providing exceptional care for your beloved pets</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="departments-showcase">
          <div class="featured-department" data-aos="fade-up" data-aos-delay="200">
            <div class="row align-items-center">
              <div class="col-lg-6 order-lg-1">
                <div class="department-content">
                  <div class="department-category">Veterinary Care</div>
                  <h2 class="department-title">24/7 Emergency Vet Clinics</h2>
                  <p class="department-description">
                    Our veterinary clinics provide round-the-clock emergency care for pets,
                    offering immediate medical attention, advanced treatment, and compassionate
                    support when your animals need it most.
                  </p>
                  <div class="department-features">
                    <div class="feature-item">
                      <i class="fas fa-check-circle"></i>
                      <span>24/7 Emergency Pet Care</span>
                    </div>
                    <div class="feature-item">
                      <i class="fas fa-check-circle"></i>
                      <span>Advanced Diagnostic & Treatment Services</span>
                    </div>
                    <div class="feature-item">
                      <i class="fas fa-check-circle"></i>
                      <span>Experienced & Licensed Veterinarians</span>
                    </div>
                  </div>
                  <a href="#" class="cta-link">Find a Vet Clinic <i class="fas fa-arrow-right"></i></a>
                </div>
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

          <div class="departments-grid">
            <div class="row">
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="department-card">
                  <div class="card-icon"><i class="fas fa-heartbeat"></i></div>
                  <div class="card-content">
                    <h3 class="card-title">Experienced & Compassionate Veterinarians</h3>
                    <p class="card-description">Licensed professionals dedicated to providing gentle, high-quality care for all pets.</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="350">
                <div class="department-card">
                  <div class="card-icon"><i class="fas fa-brain"></i></div>
                  <div class="card-content">
                    <h3 class="card-title">Modern Diagnostic Equipment</h3>
                    <p class="card-description">Advanced tools for accurate and fast diagnosis, including laboratory testing and imaging.</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="department-card">
                  <div class="card-icon"><i class="fas fa-cut"></i></div>
                  <div class="card-content">
                    <h3 class="card-title">Comprehensive Pet Care Services</h3>
                    <p class="card-description">From wellness exams and vaccinations to surgery and grooming — all in one clinic.</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="450">
                <div class="department-card">
                  <div class="card-icon"><i class="fas fa-shield-alt"></i></div>
                  <div class="card-content">
                    <h3 class="card-title">Clean, Safe & Pet-Friendly Facility</h3>
                    <p class="card-description">A hygienic, stress-free environment designed for the comfort of pets and their owners.</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="department-card">
                  <div class="card-icon"><i class="fas fa-clock"></i></div>
                  <div class="card-content">
                    <h3 class="card-title">Convenient Appointments & Emergency Support</h3>
                    <p class="card-description">Flexible scheduling with reliable care when your pet needs it most.</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="550">
                <div class="department-card">
                  <div class="card-icon"><i class="fas fa-mobile-alt"></i></div>
                  <div class="card-content">
                    <h3 class="card-title">Online Booking & Records</h3>
                    <p class="card-description">Easy appointment scheduling and access to your pet's health records anytime.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== FEATURED SERVICES SECTION ==================== -->
    <section id="featured-services" class="featured-services section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Featured Services</h2>
        <p>Comprehensive veterinary services to keep your pets healthy and happy throughout their lives</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-card">
              <div class="service-icon"><i class="fas fa-heartbeat"></i></div>
              <div class="service-image">
                <img src="assets/img/health/generalcheckups.jpg" alt="Service" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content">
                <h3>General Check-Ups & Consultations</h3>
                <p>Routine health examinations to assess your pet's overall condition, detect early signs of illness, and provide health advice.</p>
                <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-card">
              <div class="service-icon"><i class="fas fa-syringe"></i></div>
              <div class="service-image">
                <img src="assets/img/health/vaccination.jpg" alt="Service" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content">
                <h3>Vaccination & Preventive Care</h3>
                <p>Core and non-core vaccinations, deworming, flea and tick control, and parasite prevention to keep pets healthy.</p>
                <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-card">
              <div class="service-icon"><i class="fas fa-microscope"></i></div>
              <div class="service-image">
                <img src="assets/img/health/diagnostics.jpg" alt="Service" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content">
                <h3>Diagnostic Services</h3>
                <p>Laboratory tests, blood work, fecal exams, urinalysis, and basic imaging (X-ray, ultrasound) to diagnose illnesses.</p>
                <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-card">
              <div class="service-icon"><i class="fas fa-procedures"></i></div>
              <div class="service-image">
                <img src="assets/img/health/surgical.jpg" alt="Service" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content">
                <h3>Surgical Procedures</h3>
                <p>Minor and major surgeries such as spaying/neutering, wound treatment, mass removal, and soft-tissue procedures.</p>
                <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-card">
              <div class="service-icon"><i class="fas fa-cut"></i></div>
              <div class="service-image">
                <img src="assets/img/health/grooming.jpg" alt="Service" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content">
                <h3>Grooming & Basic Pet Care</h3>
                <p>Nail trimming, ear cleaning, bathing, and basic grooming to maintain hygiene and comfort.</p>
                <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="700">
            <div class="service-card">
              <div class="service-icon"><i class="fas fa-tooth"></i></div>
              <div class="service-image">
                <img src="assets/img/health/dental.jpg" alt="Service" class="img-fluid" loading="lazy">
              </div>
              <div class="service-content">
                <h3>Dental Care</h3>
                <p>Professional teeth cleaning, dental examinations, and oral health treatments for optimal pet dental hygiene.</p>
                <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- ==================== FOOTER ==================== -->
  <footer>
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">VetClinics</span>
          </a>
          <div class="footer-contact pt-3">
            <p>123 PawCare Avenue</p>
            <p>New York, NY 10001</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 555 234 5678</span></p>
            <p><strong>Email:</strong> <span>contact@vetclinics.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href="#"><i class="bi bi-twitter-x"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Our Vets</a></li>
            <li><a href="#">Appointments</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">General Checkups</a></li>
            <li><a href="#">Vaccinations</a></li>
            <li><a href="#">Pet Surgery</a></li>
            <li><a href="#">Dental Care</a></li>
            <li><a href="#">Emergency Care</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Pet Care</h4>
          <ul>
            <li><a href="#">Nutrition Advice</a></li>
            <li><a href="#">Preventive Care</a></li>
            <li><a href="#">Parasite Control</a></li>
            <li><a href="#">Senior Pet Care</a></li>
            <li><a href="#">Puppy & Kitten Care</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Support</h4>
          <ul>
            <li><a href="#">FAQs</a></li>
            <li><a href="#">Pet Insurance</a></li>
            <li><a href="#">Client Resources</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <p>&copy; 2024 VetClinics. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
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

    // Modal Scripts
    document.addEventListener('DOMContentLoaded', function() {
      // Password Toggle
      const togglePassword = document.getElementById('toggleLoginPassword');
      const passwordInput = document.getElementById('loginPassword');

      if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          this.classList.toggle('fa-eye');
          this.classList.toggle('fa-eye-slash');
        });
      }

      // Input focus animation
      document.querySelectorAll('.modal-login-form .form-control').forEach(input => {
        input.addEventListener('focus', function() {
          this.closest('.modal-input-wrapper')?.classList.add('focused');
        });
        input.addEventListener('blur', function() {
          this.closest('.modal-input-wrapper')?.classList.remove('focused');
        });
      });

      // Reset animations on modal open
      const loginModal = document.getElementById('loginModal');
      if (loginModal) {
        loginModal.addEventListener('show.bs.modal', function() {
          document.querySelectorAll('.modal-animate-row').forEach((row, index) => {
            row.style.animation = 'none';
            row.offsetHeight;
            row.style.animation = `modalFadeInUp 0.45s ease forwards ${0.05 + (index * 0.05)}s`;
          });
        });

        loginModal.addEventListener('hidden.bs.modal', function() {
          const form = document.getElementById('loginForm');
          if (form) form.reset();
        });
      }

      // Form submission
      const loginForm = document.getElementById('loginForm');
      if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const submitBtn = document.getElementById('loginSubmitBtn');
          const originalHTML = submitBtn.innerHTML;
          const wrapper = document.getElementById('modalLoginWrapper');
          
          const email = document.getElementById('loginEmail').value.trim();
          const password = document.getElementById('loginPassword').value;

          if (!email || !password) {
            Swal.fire({
              icon: 'warning',
              title: 'Missing Fields',
              text: 'Please fill in all required fields.',
              confirmButtonColor: '#1a56db'
            });
            return;
          }

          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Signing In...</span>';
          submitBtn.disabled = true;

          const formData = new FormData(this);
          formData.append('ajax', '1');

          fetch('login_process.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            submitBtn.innerHTML = originalHTML;
            submitBtn.disabled = false;

            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                html: '<div style="text-align: center;"><i class="fas fa-check-circle fa-4x mb-3" style="color: #22c55e;"></i><p>Login successful! Redirecting...</p></div>',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
              }).then(() => {
                if (data.redirect) {
                  window.location.href = data.redirect;
                } else {
                  window.location.reload();
                }
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: data.message || 'Invalid email or password.',
                confirmButtonColor: '#1a56db'
              });
              
              wrapper.classList.add('shake');
              setTimeout(() => wrapper.classList.remove('shake'), 500);
            }
          })
          .catch(error => {
            submitBtn.innerHTML = originalHTML;
            submitBtn.disabled = false;
            
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: 'Unable to connect. Please try again.',
              confirmButtonColor: '#1a56db'
            });
          });
        });
      }

      // Remember Me
      const rememberCheckbox = document.getElementById('rememberMe');
      const emailInput = document.getElementById('loginEmail');

      if (localStorage.getItem('rememberedEmail')) {
        emailInput.value = localStorage.getItem('rememberedEmail');
        rememberCheckbox.checked = true;
      }

      if (loginForm) {
        loginForm.addEventListener('submit', function() {
          if (rememberCheckbox.checked && emailInput.value) {
            localStorage.setItem('rememberedEmail', emailInput.value);
          } else {
            localStorage.removeItem('rememberedEmail');
          }
        });
      }
    });
  </script>
</body>

</html> 