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

require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'patterns/decorator.php';
require_once BASE_PATH . 'patterns/factory.php';

// Check access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: " . $base_url . "public/login.php");
    exit();
}

include_once(BASE_PATH . "public/donor/navbar.php");

// Initialize MongoDB collection
$foodCollection = $db->food; // <-- Add this line

// Fetch donor's food items
$userId = $_SESSION['user_id'];
$foodItemsCursor = $db->food->find(['donor_id' => $userId]);
$foodItems = iterator_to_array($foodItemsCursor);


// Prepare base dashboard content
$donorContent = "<div class='container mt-4'>";
$donorContent .= "<h1 class='mb-4'>Hello, " . htmlspecialchars($_SESSION['user_name']) . "!<br> Here are your shared meals:</h1>";
$donorContent .= "<div class='row'>";
if (count($foodItems) === 0) {
    $donorContent .= "<p>No food shared yet. <a href='shareFood.php'>Share your first meal!</a></p>";
} else {
    foreach ($foodItems as $item) {
        $donorContent .= "<div class='col-md-4 mb-3'>
            <div class='card shadow-sm'>
                <div class='card-body'>
                    <h5 class='card-title'>" . htmlspecialchars($item['food_item']) . "</h5>
                    <p class='card-text'>
                        <strong>Category:</strong> " . htmlspecialchars($item['food_category']) . "<br>
                        <strong>Quantity:</strong> " . htmlspecialchars($item['quantity']) . "<br>
                        <strong>Pickup Time:</strong> " . htmlspecialchars($item['pickup_time']) . "<br>
                        <strong>Location:</strong> " . htmlspecialchars($item['location']) . "<br>
                        <small class='text-muted'>Published on: " . htmlspecialchars($item['created_at']) . "</small><br><br>
                        <a href='updateFood.php?id=" . $item['_id'] . "' class='btn btn-sm btn-warning'>Update</a>
                        <a href='" . $base_url . "/src/controller/taskController.php?Delete=" . $item['_id'] . "' class='btn btn-danger btn-sm' 
                        onclick=\"return confirm('Are you sure you want to delete this food item?');\">Delete</a>
                    </p>
                </div>
            </div>
        </div>";
    }
}
$donorContent .= "</div></div>";

// Wrap with decorator
$dashboard = new Dashboard($donorContent);

// Add decorators based on role
$role = (int)$_SESSION['role'];

// Display
echo $dashboard->display();
?>
<link rel="stylesheet" type="text/css" href="<?= $base_url ?>public/css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
