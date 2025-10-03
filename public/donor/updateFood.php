<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: ../login.php");
    exit();
}

include_once("navbar.php");
use MongoDB\BSON\ObjectId;

if (!isset($_GET['id'])) {
    die("No food ID provided");
}

$foodId = $_GET['id'];
$foodItem = $foodCollection->findOne([
    '_id' => new ObjectId($foodId),
    'user_id' => $_SESSION['user_id']
]);

if (!$foodItem) die("Food item not found");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/sharefood.css">
    <title>Update Food - <?php echo $_SESSION['user_name']; ?></title>
</head>
<body>
<div class="box">
    <h1>Update a Meal</h1>
    <p>Edit the details of the food item you previously shared.</p>
    <form action="<?php echo $base_url;?>src/controller/taskController.php" method="post">

        <input type="hidden" name="food_id" value="<?php echo $foodId; ?>">

        <input type="text" name="food_item" placeholder="Enter food item name" 
               value="<?php echo htmlspecialchars($foodItem['food_item']); ?>" required>
        <span id="food_item_error"></span><br>

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
        <span id="food_category_error"></span><br>

        <input type="number" name="quantity" placeholder="Enter quantity (in servings)" 
               value="<?php echo $foodItem['quantity']; ?>" required>
        <span id="quantity_error"></span><br>

        <input type="datetime-local" name="pickup_time" 
               value="<?php echo str_replace(' ', 'T', $foodItem['pickup_time']); ?>" required>
        <span id="pickup_time_error"></span><br>

        <input type="text" name="location" placeholder="Enter pickup location" 
               value="<?php echo htmlspecialchars($foodItem['location']); ?>" required>
        <span id="location_error"></span><br>

        <input type="hidden" name="status" value="<?php echo $foodItem['status']; ?>">

        <button type="submit" name="update_food">Update Food</button>
    </form>
</div>
</body>
</html>
