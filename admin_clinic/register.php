<?php
// clinic_register.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config/db.php';

header('Content-Type: text/html; charset=UTF-8');

// Handle AJAX POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $response = ['status'=>'error','message'=>'Unknown error'];

    $clinic_name = htmlspecialchars($_POST['clinic_name']);
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);
    $lat = (float)$_POST['lat'];
    $lng = (float)$_POST['lng'];
    $admin_email = htmlspecialchars($_POST['admin_email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- Validations ---
    if($password !== $confirm_password){
        $response['message'] = 'Passwords do not match';
        echo json_encode($response); exit;
    }
    if(!filter_var($admin_email, FILTER_VALIDATE_EMAIL)){
        $response['message'] = 'Please enter a valid email address';
        echo json_encode($response); exit;
    }
    if(!preg_match('/^\d{10,15}$/',$contact)){
        $response['message'] = 'Contact number must be 10-15 digits';
        echo json_encode($response); exit;
    }
    if($lat==0 || $lng==0){
        $response['message'] = 'Please pin your clinic location on the map';
        echo json_encode($response); exit;
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM clinics WHERE admin_email=?");
    $check->bind_param("s",$admin_email);
    $check->execute();
    $check->store_result();
    if($check->num_rows>0){
        $response['message'] = 'Email already exists';
        $check->close();
        echo json_encode($response); exit;
    }
    $check->close();

    $hashed_password = password_hash($password,PASSWORD_DEFAULT);
    $created_at = date("Y-m-d H:i:s");
    $status = "pending";

    $uploadDir = "uploads/";
    if(!is_dir($uploadDir)) mkdir($uploadDir,0755,true);

    function uploadFile($inputName,$uploadDir){
        $allowedTypes = ['jpg','jpeg','png','pdf'];
        $allowedMime = ['image/jpeg','image/png','application/pdf'];
        $maxSize = 5*1024*1024;
        if(!isset($_FILES[$inputName]) || empty($_FILES[$inputName]['name'])){
            return ['error'=>'File not uploaded'];
        }
        $fileExt = strtolower(pathinfo($_FILES[$inputName]['name'],PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo,$_FILES[$inputName]["tmp_name"]);
        finfo_close($finfo);
        if(!in_array($fileExt,$allowedTypes)) return ['error'=>'Invalid file type'];
        if(!in_array($fileType,$allowedMime)) return ['error'=>'Invalid MIME type'];
        if($_FILES[$inputName]['size']>$maxSize) return ['error'=>'File too large (max 5MB)'];
        $fileName = time().'_'.uniqid().'_'.basename($_FILES[$inputName]["name"]);
        $targetFile = $uploadDir.$fileName;
        if(move_uploaded_file($_FILES[$inputName]["tmp_name"],$targetFile)) return ['name'=>$fileName];
        return ['error'=>'Failed to move uploaded file'];
    }

    $verification = uploadFile("verification_file",$uploadDir);
    $faceAuth = uploadFile("face_auth_file",$uploadDir);
    $idValidation = uploadFile("id_validation_file",$uploadDir);

    foreach([$verification,$faceAuth,$idValidation] as $file){
        if(isset($file['error'])){
            $response['message']=$file['error'];
            echo json_encode($response); exit;
        }
    }

    // Insert DB
    $stmt = $conn->prepare("INSERT INTO clinics 
        (clinic_name,first_name,last_name,contact,address,lat,lng,admin_email,password,verification_file,face_auth_file,id_validation_file,created_at,status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssddsssssss",
        $clinic_name,$first_name,$last_name,$contact,$address,$lat,$lng,$admin_email,$hashed_password,
        $verification['name'],$faceAuth['name'],$idValidation['name'],$created_at,$status
    );
    if($stmt->execute()){
        $response['status']='success';
        $response['message']='Clinic registration submitted. Please wait for admin approval.';
        $response['redirect']='login.php';
    }else{
        file_put_contents('db_errors.log',date('Y-m-d H:i:s').' - '.$stmt->error.PHP_EOL,FILE_APPEND);
        $response['message']='Database error. Please try again later.';
    }
    $stmt->close();
    echo json_encode($response); exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clinic Manager Registration</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
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
  max-width: 1200px;
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
  flex: 0.8;
  background: linear-gradient(135deg, #0c2d57 0%, #1a56db 50%, #3b82f6 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  position: relative;
  overflow: hidden;
  min-height: 700px;
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
  width: 250px;
  height: 250px;
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
  padding: 12px 0;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.9rem;
}

.features-list li i {
  width: 35px;
  height: 35px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
}

/* Medical icons decoration */
.med-icon {
  position: absolute;
  font-size: 2rem;
  color: rgba(255,255,255,0.1);
  z-index: 1;
}

.med-icon:nth-of-type(1) { top: 8%; left: 10%; transform: rotate(-30deg); }
.med-icon:nth-of-type(2) { top: 15%; right: 12%; transform: rotate(20deg); }
.med-icon:nth-of-type(3) { bottom: 25%; left: 8%; transform: rotate(-15deg); }
.med-icon:nth-of-type(4) { bottom: 12%; right: 15%; transform: rotate(45deg); }
.med-icon:nth-of-type(5) { top: 45%; left: 5%; transform: rotate(10deg); }
.med-icon:nth-of-type(6) { top: 60%; right: 8%; transform: rotate(-20deg); }

/* Right Side - Form Section */
.form-section {
  flex: 1.2;
  padding: 40px 45px;
  max-height: 95vh;
  overflow-y: auto;
}

.form-section::-webkit-scrollbar {
  width: 6px;
}

.form-section::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.form-section::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #3b82f6, #1a56db);
  border-radius: 10px;
}

.form-section::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #1a56db, #0c2d57);
}

.form-header {
  margin-bottom: 25px;
}

.form-header .logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.form-header .logo i {
  font-size: 2.2rem;
  color: #1a56db;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  padding: 12px;
  border-radius: 12px;
}

.form-header .logo span {
  font-size: 1.4rem;
  font-weight: 700;
  color: #0c2d57;
}

.form-header h3 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #0c2d57;
  margin-bottom: 5px;
}

.form-header p {
  color: #64748b;
  font-size: 0.9rem;
}

/* Section Dividers */
.section-title {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 25px 0 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid #e2e8f0;
}

.section-title i {
  color: #3b82f6;
  font-size: 1.1rem;
}

.section-title span {
  font-weight: 600;
  color: #1e40af;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Form Styling - Blue Theme */
.form-label {
  font-weight: 500;
  color: #1e40af;
  font-size: 0.85rem;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.form-label i {
  font-size: 0.75rem;
  color: #3b82f6;
}

.form-control, .form-select {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  background-color: #f8fafc;
}

.form-control:focus, .form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
  background-color: white;
}

.form-control:hover, .form-select:hover {
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
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  z-index: 10;
}

.input-group .form-control {
  padding-left: 45px;
}

.input-group.focused .input-icon {
  color: #3b82f6;
}

/* Map Container */
.map-container {
  border-radius: 16px;
  overflow: hidden;
  border: 3px solid #e2e8f0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.map-container:hover {
  border-color: #93c5fd;
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
}

#map {
  height: 280px;
  width: 100%;
}

.locate-btn {
  background: linear-gradient(135deg, #1a56db, #3b82f6);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  font-size: 0.85rem;
  font-weight: 500;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.locate-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 20px rgba(26, 86, 219, 0.4);
  background: linear-gradient(135deg, #1e40af, #2563eb);
  color: white;
}

.locate-btn i {
  animation: pulse-locate 2s infinite;
}

@keyframes pulse-locate {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

/* File Upload Styling */
.file-upload-wrapper {
  position: relative;
}

.file-upload-wrapper .form-control {
  padding: 12px 16px;
  cursor: pointer;
}

.file-upload-wrapper .form-control::file-selector-button {
  background: linear-gradient(135deg, #1a56db, #3b82f6);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  margin-right: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-upload-wrapper .form-control::file-selector-button:hover {
  background: linear-gradient(135deg, #1e40af, #2563eb);
}

.file-hint {
  font-size: 0.75rem;
  color: #64748b;
  margin-top: 5px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.file-hint i {
  color: #3b82f6;
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

/* Submit Button - Blue Theme */
.btn-register {
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
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(26, 86, 219, 0.4);
  background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
}

.btn-register:hover::before {
  left: 100%;
}

.btn-register:active {
  transform: translateY(0);
}

/* Login Link - Blue Theme */
.login-link {
  text-align: center;
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
  color: #64748b;
  font-size: 0.9rem;
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
.form-row:nth-child(10) { animation-delay: 0.55s; }

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

/* Info Badge */
.info-badge {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.85rem;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 20px;
  border-left: 4px solid #3b82f6;
}

.info-badge i {
  font-size: 1.2rem;
  color: #1a56db;
  margin-top: 2px;
}

/* Responsive Design */
@media (max-width: 992px) {
  .register-wrapper {
    flex-direction: column;
    max-width: 600px;
  }

  .image-section {
    min-height: 280px;
    padding: 30px;
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
    padding: 30px;
    max-height: none;
  }
}

@media (max-width: 576px) {
  .main-container {
    padding: 10px;
  }

  .form-section {
    padding: 25px 20px;
  }

  .image-section {
    min-height: 220px;
    padding: 25px;
  }

  .form-header h3 {
    font-size: 1.3rem;
  }

  #map {
    height: 220px;
  }

  .row > div {
    margin-bottom: 5px;
  }
}

/* Leaflet Custom Styling */
.leaflet-control-zoom a {
  background: linear-gradient(135deg, #1a56db, #3b82f6) !important;
  color: white !important;
  border: none !important;
}

.leaflet-control-zoom a:hover {
  background: linear-gradient(135deg, #1e40af, #2563eb) !important;
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
      <i class="fas fa-stethoscope med-icon"></i>
      <i class="fas fa-heartbeat med-icon"></i>
      <i class="fas fa-clinic-medical med-icon"></i>
      <i class="fas fa-syringe med-icon"></i>
      <i class="fas fa-paw med-icon"></i>
      <i class="fas fa-pills med-icon"></i>
      
      <div class="clinic-illustration">
        <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=400&h=400&fit=crop" alt="Veterinary Clinic">
      </div>
      
      <h2>Partner With Us</h2>
      <p>Register your veterinary clinic and connect with pet owners in Dasmariñas City.</p>
      
      <ul class="features-list">
        <li>
          <i class="fas fa-calendar-alt"></i>
          Online Booking System
        </li>
        <li>
          <i class="fas fa-users"></i>
          Expand Your Client Base
        </li>
        <li>
          <i class="fas fa-chart-line"></i>
          Analytics Dashboard
        </li>
        <li>
          <i class="fas fa-mobile-alt"></i>
          Mobile-Friendly Platform
        </li>
        <li>
          <i class="fas fa-shield-alt"></i>
          Verified Clinic Badge
        </li>
      </ul>
    </div>

    <!-- Right Side - Form Section -->
    <div class="form-section">
      <div class="form-header">
        <div class="logo">
          <i class="fas fa-hospital-user"></i>
          <span>Clinic Registration</span>
        </div>
        <h3>Register Your Clinic</h3>
        <p>Complete the form below to set up your admin account</p>
      </div>

      <div class="info-badge">
        <i class="fas fa-info-circle"></i>
        <div>
          <strong>Verification Required</strong><br>
          Your clinic will be reviewed within 24-48 hours after submission.
        </div>
      </div>

      <form id="registerForm" enctype="multipart/form-data">
        
        <!-- Clinic Information -->
        <div class="section-title form-row">
          <i class="fas fa-hospital"></i>
          <span>Clinic Information</span>
        </div>

        <div class="mb-3 form-row">
          <label class="form-label"><i class="fas fa-clinic-medical"></i> Clinic Name *</label>
          <div class="input-group">
            <i class="fas fa-building input-icon"></i>
            <input type="text" name="clinic_name" class="form-control" placeholder="Enter your clinic name" required>
          </div>
        </div>

        <div class="row form-row">
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fas fa-user"></i> First Name *</label>
            <input type="text" name="first_name" class="form-control" placeholder="John" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fas fa-user"></i> Last Name *</label>
            <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
          </div>
        </div>

        <div class="mb-3 form-row">
          <label class="form-label"><i class="fas fa-phone"></i> Contact Number *</label>
          <div class="input-group">
            <i class="fas fa-phone-alt input-icon"></i>
            <input type="text" name="contact" class="form-control" placeholder="09xxxxxxxxx" required maxlength="11">
          </div>
        </div>

        <div class="mb-3 form-row">
          <label class="form-label"><i class="fas fa-map-marker-alt"></i> Complete Address *</label>
          <div class="input-group">
            <i class="fas fa-location-dot input-icon"></i>
            <input type="text" name="address" id="address" class="form-control" placeholder="Street, Barangay, Dasmariñas" required>
          </div>
        </div>

        <!-- Map Section -->
        <div class="section-title form-row">
          <i class="fas fa-map-marked-alt"></i>
          <span>Clinic Location</span>
        </div>

        <div class="mb-3 form-row">
          <label class="form-label"><i class="fas fa-map-pin"></i> Pin Your Clinic Location (Dasmariñas Only) *</label>
          <div class="map-container">
            <div id="map"></div>
          </div>
          <div class="text-end mt-3">
            <button type="button" class="locate-btn" onclick="locateUser()">
              <i class="fas fa-crosshairs"></i> Detect My Location
            </button>
          </div>
        </div>

        <input type="hidden" name="lat" id="lat" required>
        <input type="hidden" name="lng" id="lng" required>

        <!-- Account Security -->
        <div class="section-title form-row">
          <i class="fas fa-lock"></i>
          <span>Account Security</span>
        </div>

        <div class="mb-3 form-row">
          <label class="form-label"><i class="fas fa-envelope"></i> Admin Email *</label>
          <div class="input-group">
            <i class="fas fa-at input-icon"></i>
            <input type="email" name="admin_email" class="form-control" placeholder="admin@yourclinic.com" required>
          </div>
        </div>

        <div class="row form-row">
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fas fa-key"></i> Password *</label>
            <div class="input-group">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" name="password" id="password" class="form-control" placeholder="Min. 8 characters" required minlength="8">
            </div>
            <div class="password-strength">
              <div class="password-strength-bar" id="strengthBar"></div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="fas fa-key"></i> Confirm Password *</label>
            <div class="input-group">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required minlength="8">
            </div>
          </div>
        </div>

        <!-- Verification Documents -->
        <div class="section-title form-row">
          <i class="fas fa-file-alt"></i>
          <span>Verification Documents</span>
        </div>

        <div class="mb-3 form-row file-upload-wrapper">
          <label class="form-label"><i class="fas fa-file-certificate"></i> Clinic Verification Document *</label>
          <input type="file" name="verification_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
          <div class="file-hint">
            <i class="fas fa-info-circle"></i>
            Business permit, DTI, or SEC registration (PDF, JPG, PNG)
          </div>
        </div>

        <div class="mb-3 form-row file-upload-wrapper">
          <label class="form-label"><i class="fas fa-camera"></i> Face Authentication Image *</label>
          <input type="file" name="face_auth_file" class="form-control" required accept=".jpg,.jpeg,.png">
          <div class="file-hint">
            <i class="fas fa-info-circle"></i>
            Clear front-facing photo for verification (JPG, PNG)
          </div>
        </div>

        <div class="mb-4 form-row file-upload-wrapper">
          <label class="form-label"><i class="fas fa-id-card"></i> Valid Government ID *</label>
          <input type="file" name="id_validation_file" class="form-control" required accept=".jpg,.jpeg,.png,.pdf">
          <div class="file-hint">
            <i class="fas fa-info-circle"></i>
            Any valid government-issued ID (PDF, JPG, PNG)
          </div>
        </div>

        <div class="d-grid form-row">
          <button type="submit" class="btn btn-register">
            <i class="fas fa-clinic-medical"></i>
            Register Clinic
          </button>
        </div>

        <div class="login-link form-row">
          Already have a clinic account? <a href="login.php">Login</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Leaflet Map
const dasmaCenter = [14.3294, 120.9367];
const map = L.map('map').setView(dasmaCenter, 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let marker;

// Custom marker icon
const clinicIcon = L.divIcon({
  html: '<i class="fas fa-map-marker-alt" style="color: #1a56db; font-size: 2.5rem; filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));"></i>',
  iconSize: [30, 42],
  iconAnchor: [15, 42],
  className: 'custom-marker'
});

map.on('click', function(e) {
  let lat = e.latlng.lat, lng = e.latlng.lng;
  if (lat < 14.25 || lat > 14.40 || lng < 120.85 || lng > 121.00) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Location',
      text: 'Please select a location within Dasmariñas City only.',
      confirmButtonColor: '#1a56db'
    });
    return;
  }
  setMarker(lat, lng);
});

function setMarker(lat, lng) {
  if (marker) map.removeLayer(marker);
  marker = L.marker([lat, lng], { icon: clinicIcon }).addTo(map);
  map.setView([lat, lng], 16);
  document.getElementById("lat").value = lat;
  document.getElementById("lng").value = lng;
  
  // Visual feedback
  Swal.fire({
    icon: 'success',
    title: 'Location Set!',
    text: 'Clinic location has been pinned successfully.',
    timer: 1500,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
  });
}

function locateUser() {
  if (!navigator.geolocation) {
    Swal.fire({
      icon: 'error',
      title: 'Not Supported',
      text: 'Geolocation is not supported by your browser.',
      confirmButtonColor: '#1a56db'
    });
    return;
  }
  
  // Show loading
  Swal.fire({
    title: 'Detecting Location...',
    html: '<i class="fas fa-spinner fa-spin fa-2x" style="color: #3b82f6;"></i>',
    showConfirmButton: false,
    allowOutsideClick: false
  });
  
  navigator.geolocation.getCurrentPosition(function(pos) {
    Swal.close();
    let lat = pos.coords.latitude, lng = pos.coords.longitude;
    if (lat < 14.25 || lat > 14.40 || lng < 120.85 || lng > 121.00) {
      Swal.fire({
        icon: 'error',
        title: 'Outside Coverage Area',
        text: 'Your location is outside Dasmariñas City. Please pin manually.',
        confirmButtonColor: '#1a56db'
      });
      return;
    }
    setMarker(lat, lng);
  }, function() {
    Swal.fire({
      icon: 'error',
      title: 'Location Error',
      text: 'Unable to retrieve your location. Please pin manually.',
      confirmButtonColor: '#1a56db'
    });
  });
}

// Password Strength Checker
document.getElementById('password').addEventListener('input', function() {
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

// AJAX Form Submission
document.getElementById('registerForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = this;
  const fd = new FormData(form);
  fd.append('ajax', '1');

  // Validation
  if (!fd.get('lat') || !fd.get('lng')) {
    Swal.fire({
      icon: 'warning',
      title: 'Location Required',
      text: 'Please pin your clinic location on the map.',
      confirmButtonColor: '#1a56db'
    });
    return;
  }

  if (fd.get('password') !== fd.get('confirm_password')) {
    Swal.fire({
      icon: 'error',
      title: 'Password Mismatch',
      text: 'Passwords do not match. Please try again.',
      confirmButtonColor: '#1a56db'
    });
    return;
  }

  // Add loading state
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering Clinic...';
  submitBtn.disabled = true;

  try {
    const res = await fetch('', { method: 'POST', body: fd });
    const data = await res.json();
    
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    if (data.status === 'success') {
      await Swal.fire({
        icon: 'success',
        title: 'Registration Successful!',
        html: `<div style="text-align: center;">
                <i class="fas fa-check-circle fa-4x mb-3" style="color: #22c55e;"></i>
                <p>${data.message}</p>
                <p class="text-muted">You will be redirected shortly...</p>
              </div>`,
        showConfirmButton: false,
        timer: 3000
      });
      if (data.redirect) window.location.href = data.redirect;
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Registration Failed',
        text: data.message,
        confirmButtonColor: '#1a56db'
      });
    }
  } catch (err) {
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    Swal.fire({
      icon: 'error',
      title: 'Connection Error',
      text: 'Something went wrong. Please try again.',
      confirmButtonColor: '#1a56db'
    });
    console.error(err);
  }
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