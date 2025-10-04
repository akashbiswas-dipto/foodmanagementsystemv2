<?php
session_start();

// Base URL & Path
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}

require_once BASE_PATH.'config.php';
require_once BASE_PATH.'patterns/factory.php';
include_once(BASE_PATH.'public/navbar.php'); ?> <!-- Include navbar -->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= $base_url ?>public/css/login.css">
<title>NGO Login</title>
</head>
<body>
<div class="box">
    <h1>Login</h1>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="<?php echo $base_url;?>src/controller/authController.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="<?= $base_url ?>public/signup.php">Sign Up here</a></p>
</div>
</body>
</html>
