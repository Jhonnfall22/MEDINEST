<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = $_POST['admin_name'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($admin_name) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, admin_name, password FROM admin WHERE admin_name = ?");
        $stmt->bind_param("s", $admin_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($admin = $result->fetch_assoc()) {
            // Check if password is hashed or plain (assuming hashed for security, but checking both for flexibility if it's a new setup)
            if (password_verify($password, $admin['password']) || $password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Administrator not found.";
        }
        $stmt->close();
    } else {
        $error = "Please enter both name and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Super Admin Login | Vet Network</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root { --admin-dark: #1a1a1a; --bg-soft: #f8f9fa; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-soft); height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .auth-card { width: 100%; max-width: 420px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 40px; }
        .auth-icon { width: 60px; height: 60px; background: var(--admin-dark); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .form-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; font-weight: 700; }
        .form-control { padding: 12px; border-radius: 8px; border: 1px solid #ddd; }
        .btn-dark-admin { background-color: var(--admin-dark); color: white; padding: 14px; font-weight: 600; border-radius: 8px; width: 100%; border: none; transition: 0.3s; }
        .btn-dark-admin:hover { background-color: #000; }
        .register-footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 0.85rem; }
        .register-footer a { color: var(--admin-dark); font-weight: 700; text-decoration: none; }
        .alert-error { background-color: #fff5f5; border: 1px solid #feb2b2; color: #c53030; padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-icon"><i data-feather="shield"></i></div>
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">Super Admin Access</h4>
            <p class="text-muted small">Secure Network Console</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-error">
                <i data-feather="alert-circle" style="width: 14px; height: 14px; margin-right: 5px;"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Administrator Name</label>
                <input type="text" name="admin_name" class="form-control" placeholder="Admin Name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Secure Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-check mb-4 mt-3">
                <input class="form-check-input" type="checkbox" id="rememberMe">
                <label class="form-check-label small text-muted" for="rememberMe">Remember Session</label>
            </div>
            <button type="submit" class="btn btn-dark-admin shadow-sm">Login to Dashboard</button>
        </form>
        <div class="register-footer"><a href="../register.php">Apply for New Clinic Registration</a></div>
    </div>
    <script>feather.replace();</script>
</body>
</html>
