<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}

// Only allow Admin (1) and Donor (2)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: ../login.php");
    exit();
}

// Base URL
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
} else {
    $base_url = "https://mop-zilla.com/"; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= $base_url ?>public/content/FWMlogo.ico">
    <link rel="stylesheet" href="<?= $base_url ?>public/css/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color: #3BB143 !important;">
    <div class="container">
        <a class="navbar-brand" href="<?= $base_url ?>">
            <img src="<?= $base_url ?>public/content/FWM.png" alt="Logo" width="50">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $base_url ?>public/donor/dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base_url ?>public/donor/sharefood.php">Share a Meal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base_url ?>public/donor/ngorequest.php">Pending NGO Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base_url ?>public/profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base_url ?>src/controller/authController.php?logout=logout">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
