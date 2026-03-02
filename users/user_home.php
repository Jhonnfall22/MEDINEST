<?php
session_start();
// Optional: get user's name if stored in session
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home | VetClinic</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --admin-primary: #220bef;
            --bg-body: #f4f7fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-body);
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: #fff;
            padding: 15px 0;
            transition: all 0.3s;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 24px;
        }

        .navbar-brand span {
            color: var(--admin-primary);
        }

        .nav-link {
            font-weight: 500;
            color: #444 !important;
            margin: 0 10px;
        }

        .nav-link:hover {
            color: var(--admin-primary) !important;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            position: relative;
            height: 80vh;
            min-height: 500px;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('pic.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            overflow: hidden;
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 35px;
            opacity: 0.9;
            font-weight: 300;
        }

        /* ===== START BUTTON ===== */
        .btn-start {
            background-color: var(--admin-primary);
            color: #fff;
            font-weight: 600;
            padding: 15px 45px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(34, 11, 239, 0.3);
            border: none;
        }

        .btn-start:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(34, 11, 239, 0.4);
            color: #fff;
            background-color: #1a08c7;
        }

        /* Quick Info Bar */
        .info-bar {
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        .info-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }

        .info-card i {
            font-size: 2rem;
            color: var(--admin-primary);
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="user_home.php">Vet<span>clinic</span></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navMenu">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-3">
                    <span class="nav-link">Welcome, <?php echo htmlspecialchars($username); ?></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger rounded-pill px-4" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero mt-5">
    <div class="hero-content">
        <span class="badge rounded-pill mb-3 px-3 py-2" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);">Expert Pet Care 24/7</span>
        <h1>Compassionate Care <br> for Your Best Friend</h1>
        <p>Easily manage your pet’s health records, schedule clinic visits, and track appointments all in one place.</p>
        <a href="pets.php" class="btn-start">
            Get Started <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>

<div class="container info-bar">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="info-card">
                <i class="bi bi-calendar-check"></i>
                <h6 class="fw-bold">Fast Booking</h6>
                <p class="small text-muted mb-0">Book appointments in under 2 minutes.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <i class="bi bi-shield-check"></i>
                <h6 class="fw-bold">Trusted Clinics</h6>
                <p class="small text-muted mb-0">Verified network of professional veterinarians.</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 text-center">
    <hr class="my-5 opacity-10">
    <p class="small text-muted">&copy; 2026 VetClinic Network. All Rights Reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>