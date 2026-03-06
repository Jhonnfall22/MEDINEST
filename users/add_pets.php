if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/db.php";

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = $_POST['pet_name'] ?? '';
    $type   = $_POST['pet_type'] ?? '';
    $breed  = $_POST['breed'] ?? '';
    $age    = $_POST['age'] ?? 0;
    $clinic = $_POST['clinic'] ?? '';
    $status = 'pending'; // default status

    // Get owner identification
    $username_reg = $_SESSION['username'] ?? 'Guest';
    
    // Simple validation
    if($name && $type && $clinic){
        $stmt = $conn->prepare("INSERT INTO pets (name, type, breed, age, clinic, status, user, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssisss", $name, $type, $breed, $age, $clinic, $status, $username_reg);
        if($stmt->execute()){
            header("Location: mypets.php"); 
            exit;
        } else {
            die("Error saving pet: " . $stmt->error);
        }
    } else {
        die("Please fill in all required fields.");
    }
} else {
    die("Invalid request method.");
}
?>
