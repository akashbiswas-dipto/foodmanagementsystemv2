<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: ../login.php");
    exit();
}
else{
include_once("navbar.php");

$userId = $_SESSION['user_id'];
$foodItemsCursor = $foodCollection->find(['user_id' => $userId]);
$foodItems = iterator_to_array($foodItemsCursor); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/dashboard.css">
    <title>Welcome - <?php echo $_SESSION['user_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="box">
    <div class="container mt-4">
        <h1 class="mb-4">Hello, <?php echo $_SESSION['user_name']; ?>!<br> Here are your shared meals:</h1>
        <div class="row">
            <?php 
            if (count($foodItems) === 0) {
                echo "<p>No food shared yet. <a href='shareFood.php'>Share your first meal!</a></p>";
            } else {
                foreach ($foodItems as $item) { ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['food_item']); ?></h5>
                                <p class="card-text">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($item['food_category']); ?><br>
                                    <strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?><br>
                                    <strong>Pickup Time:</strong> <?php echo htmlspecialchars($item['pickup_time']); ?><br>
                                    <strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?><br>
                                    <small class="text-muted">Published on: <?php echo htmlspecialchars($item['created_at']); ?></small><br><br>
                                    <a href="updateFood.php?id=<?php echo $item['_id']; ?>" class="btn btn-sm btn btn-warning">Update</a>
                                    <a href="<?php echo $base_url;?>/src/controller/taskController.php?Delete=<?php echo $item['_id']; ?>" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Are you sure you want to delete this food item?');">Delete</a>
                                </p>
                                
                            </div>
                        </div>
                    </div>
            <?php } } ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}   

?>