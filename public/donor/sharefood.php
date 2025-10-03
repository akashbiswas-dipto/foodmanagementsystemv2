<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: ../login.php");
    exit();
} else {
    include_once("navbar.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/sharefood.css">
    <title>Share Food - <?php echo $_SESSION['user_name'];?></title>

</head>
<body>
    <div class="box">
        <h1>Share a Meal</h1>
        <p>Use this form to share details about the food you want to donate.</p>
        <form action="<?php echo $base_url;?>/src/controller/taskController.php" method="post">
            
                <input type="text" name="food_item" placeholder="Enter food item name" required>
                <span id="food_item_error"></span><br>
                <select name="food_category" required>
                    <option value="" disabled selected>Select Food Category</option>
                    <option value="Cooked Meals">Cooked Meals</option>
                    <option value="Bakery">Bakery</option>
                    <option value="Produce">Produce</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Packaged">Packaged</option>
                    <option value="Other">Other</option>
                </select>
                <span id="food_category_error"></span><br>
        
                <input type="number" name="quantity" placeholder="Enter quantity (in servings)" required>
                <span id="quantity_error"></span><br>
            
                <input type="datetime-local" name="pickup_time" placeholder="Enter pickup time" required>
                <span id="pickup_time_error"></span><br>
            
                <input type="text" name="location" placeholder="Enter pickup location" required>
                <span id="location_error"></span><br>
                <input type="hidden" name="status" value="1">
            <button type="submit" name="share_food">Share Food</button>
        </form>
    </div>
</body>
</html>
<?php
}
?>
