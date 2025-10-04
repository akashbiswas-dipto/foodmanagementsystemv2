<?php
use MongoDB\BSON\ObjectId;

session_start();

// Base URL & Path
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}

// Require config for MongoDB connection
require_once BASE_PATH . 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ".$base_url."public/login.php");
    exit();
}

// Dynamically include navbar based on user role
switch ($_SESSION['role']) {
    case 1: // Admin
    case 2: // Donor
        include_once(BASE_PATH . "public/donor/navbar.php");
        break;
    case 3: // NGO
        include_once(BASE_PATH . "public/ngo/navbar.php");
        break;
    default:
        include_once(BASE_PATH . "public/navbar.php"); // fallback
        break;
}

// Fetch user details from MongoDB
$userId = new ObjectId($_SESSION['user_id']);
$user = $db->users->findOne(['_id' => $userId]); // <-- use $db->users

if (!$user) {
    echo "<p>User not found.</p>";
    exit();
}

// Map role numbers to readable names
$roleNames = [
    1 => "Self Donor",
    2 => "Restaurant",
    3 => "NGO"
];
$role = $roleNames[$user['role']] ?? "Unknown";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= htmlspecialchars($user['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>public/css/dashboard.css">
</head>
<body>
<div class="box">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-header text-center">
                <h3><?= htmlspecialchars($user['name']); ?>'s Profile</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']); ?></p>
                <p><strong>Role:</strong> <?= $role; ?></p>
                <p><strong>Joined on:</strong> <?= htmlspecialchars(date("d M Y", strtotime($user['created_at']))); ?></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
