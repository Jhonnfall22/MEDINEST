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
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background:#f6f7fb; }
.auth-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 15px; }
.auth-card { max-width:600px; width:100%; }
</style>
</head>
<body>
<div class="auth-wrapper">
<div class="card auth-card shadow-sm">
<div class="card-body p-4">
<h4 class="text-center mb-1">Clinic Manager Registration</h4>
<p class="text-center text-muted mb-4">Admin Account Setup</p>
<form id="registerForm" enctype="multipart/form-data">
    <div class="mb-3"><label>Clinic Name</label><input type="text" name="clinic_name" class="form-control" required></div>
    <div class="row">
        <div class="col-md-6 mb-3"><label>First Name</label><input type="text" name="first_name" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label>Last Name</label><input type="text" name="last_name" class="form-control" required></div>
    </div>
    <div class="mb-3"><label>Contact Number</label><input type="text" name="contact" class="form-control" required></div>
    <div class="mb-3"><label>Address</label><input type="text" name="address" id="address" class="form-control" required></div>
    <div class="mb-4"><label>Pin Clinic Location (Dasmariñas Only)</label><div id="map" style="height:350px;border-radius:8px;"></div></div>
    <div class="mb-3 text-end"><button type="button" class="btn btn-outline-secondary btn-sm" onclick="locateUser()">📍 Locate My Location</button></div>
    <input type="hidden" name="lat" id="lat" required>
    <input type="hidden" name="lng" id="lng" required>
    <div class="mb-3"><label>Admin Email</label><input type="email" name="admin_email" class="form-control" required></div>
    <div class="row">
        <div class="col-md-6 mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
    </div>
    <div class="mb-3"><label>Clinic Verification Document</label><input type="file" name="verification_file" class="form-control" required></div>
    <div class="mb-3"><label>Face Authentication Image</label><input type="file" name="face_auth_file" class="form-control" required></div>
    <div class="mb-3"><label>Valid Government ID</label><input type="file" name="id_validation_file" class="form-control" required></div>
    <button type="submit" class="btn btn-primary w-100">Register Clinic</button>
</form>
</div></div></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Leaflet Map
const dasmaCenter=[14.3294,120.9367];
const map=L.map('map').setView(dasmaCenter,14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
let marker;
map.on('click', function(e){
    let lat=e.latlng.lat,lng=e.latlng.lng;
    if(lat<14.25||lat>14.40||lng<120.85||lng>121.00){
        Swal.fire({icon:'error',title:'Invalid Location',text:'Please select a location within Dasmariñas City only.'});
        return;
    }
    setMarker(lat,lng);
});
function setMarker(lat,lng){
    if(marker) map.removeLayer(marker);
    marker=L.marker([lat,lng]).addTo(map);
    map.setView([lat,lng],16);
    document.getElementById("lat").value=lat;
    document.getElementById("lng").value=lng;
}
function locateUser(){
    if(!navigator.geolocation){Swal.fire('Error','Geolocation not supported','error'); return;}
    navigator.geolocation.getCurrentPosition(function(pos){
        let lat=pos.coords.latitude,lng=pos.coords.longitude;
        if(lat<14.25||lat>14.40||lng<120.85||lng>121.00){
            Swal.fire({icon:'error',title:'Outside City',text:'Your location is outside Dasmariñas City.'});
            return;
        }
        setMarker(lat,lng);
    }, function(){Swal.fire({icon:'error',title:'Error',text:'Unable to get your location'});});
}

// AJAX form
document.getElementById('registerForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const form=this;
    const fd=new FormData(form);
    fd.append('ajax','1');

    if(!fd.get('lat') || !fd.get('lng')){
        Swal.fire({icon:'error',title:'Location Required',text:'Please pin your clinic location on the map.'});
        return;
    }

    try{
        const res = await fetch('', {method:'POST', body:fd});
        const data = await res.json();
        if(data.status==='success'){
            await Swal.fire({icon:'success',title:'Success',text:data.message});
            if(data.redirect) window.location.href=data.redirect;
        } else {
            Swal.fire({icon:'error',title:'Error',text:data.message});
        }
    } catch(err){
        Swal.fire({icon:'error',title:'Error',text:'Something went wrong.'});
        console.error(err);
    }
});
</script>
</body>
</html>