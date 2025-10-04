<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], [1,2])) {
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
    <title>Share Food - <?php echo htmlspecialchars($_SESSION['user_name']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/sharefood.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-3">Share a Meal</h2>
            <p class="card-text mb-4">Use this form to share details about the food you want to donate.</p>

            <form action="<?php echo $base_url;?>src/controller/taskController.php" method="post" class="row g-3">

                <div class="col-12">
                    <label for="food_item" class="form-label">Food Item Name</label>
                    <input type="text" class="form-control" id="food_item" name="food_item" placeholder="Enter food item name" required>
                </div>

                <div class="col-12">
                    <label for="food_category" class="form-label">Food Category</label>
                    <select class="form-select" id="food_category" name="food_category" required>
                        <option value="" disabled selected>Select Food Category</option>
                        <option value="Cooked Meals">Cooked Meals</option>
                        <option value="Bakery">Bakery</option>
                        <option value="Produce">Produce</option>
                        <option value="Dairy">Dairy</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Packaged">Packaged</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="quantity" class="form-label">Quantity (in servings)</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" required>
                </div>

                <div class="col-md-6">
                    <label for="pickup_time" class="form-label">Pickup Time</label>
                    <input type="datetime-local" class="form-control" id="pickup_time" name="pickup_time" required>
                </div>

                <div class="col-12">
                    <label for="location" class="form-label">Pickup Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter pickup location" required>
                </div>

                <input type="hidden" name="status" value="1">

                <div class="col-12 mt-3">
                    <button type="submit" name="share_food" class="btn btn-success w-100">Share Food</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php } ?>
