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
require_once BASE_PATH . 'patterns/proxy.php';
require_once BASE_PATH . 'patterns/observer.php';
require_once BASE_PATH . 'patterns/chain.php';

// Only allow Admin (1) and Donor (2)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: " . $base_url . "public/login.php");
    exit();
}

include_once(BASE_PATH . "public/donor/navbar.php");

// Initialize MongoDB collection
$foodCollection = $db->food;

// Middleware chain: Auth -> Validation
$auth = new AuthMiddleware();
$validate = new ValidationMiddleware();
$auth->setNext($validate);

// Check if food ID is provided
if (!isset($_GET['id'])) {
    die("No food ID provided");
}

$foodId = $_GET['id'];

// Proxy: Only allow owner or admin to access
$foodProxy = new FoodProxy($foodId, $_SESSION['role'], $_SESSION['user_id'], $db);
$foodItem = $foodProxy->getFood();

if (!$foodItem) {
    die("Food item not found or access denied");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= $base_url ?>public/css/sharefood.css">
<title>Update Food - <?= htmlspecialchars($_SESSION['user_name']) ?></title>
</head>
<body>
<div class="box">
    <h1>Update a Meal</h1>
    <p>Edit the details of the food item you previously shared.</p>

    <form action="<?php echo $base_url;?>src/controller/taskController.php" method="post">
        <input type="hidden" name="food_id" value="<?= htmlspecialchars($foodId) ?>">

        <input type="text" name="food_item" placeholder="Enter food item name" 
               value="<?= htmlspecialchars($foodItem['food_item']) ?>" required>
        <br>

        <select name="food_category" required>
            <option value="" disabled>Select Food Category</option>
            <?php
            $categories = ["Cooked Meals","Bakery","Produce","Dairy","Beverages","Packaged","Other"];
            foreach($categories as $category) {
                $selected = ($foodItem['food_category'] === $category) ? "selected" : "";
                echo "<option value='$category' $selected>$category</option>";
            }
            ?>
        </select>
        <br>

        <input type="number" name="quantity" placeholder="Enter quantity (in servings)" 
               value="<?= htmlspecialchars($foodItem['quantity']) ?>" required>
        <br>

        <input type="datetime-local" name="pickup_time" 
               value="<?= date('Y-m-d\TH:i', strtotime($foodItem['pickup_time'])) ?>" required>
        <br>

        <input type="text" name="location" placeholder="Enter pickup location" 
               value="<?= htmlspecialchars($foodItem['location']) ?>" required>
        <br>

        <input type="hidden" name="status" value="<?= htmlspecialchars($foodItem['status']) ?>">

        <button type="submit" name="update_food">Update Food</button>
    </form>
</div>
</body>
</html>
