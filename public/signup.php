<?php 
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}

include_once(BASE_PATH . 'public/navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Login/Signup CSS -->
    <link rel="stylesheet" type="text/css" href="<?= $base_url ?>public/css/login.css">
    <title>Signup - Food Waste Management System</title>
</head>
<body>
    <div class="box">
        <h1>Welcome to Food Waste Management System</h1>
        <p>Your one-stop solution to manage and reduce food waste effectively.</p>

        <form action="<?= $base_url ?>src/controller/authController.php" method="post">
            <input type="text" name="name" placeholder="Enter your name" required><br>
            <input type="text" name="phone" placeholder="Enter your phone number" required><br>
            <input type="text" name="location" placeholder="Enter your location" required><br>
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>

            <select name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="1">Self Donor</option>
                <option value="2">Restaurant</option>
                <option value="3">NGO</option>
            </select><br>

            <button type="submit" name="signup" value="1">Signup</button>
        </form>
    </div>
</body>
</html>
