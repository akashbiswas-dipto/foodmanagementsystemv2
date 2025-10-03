<?php
use MongoDB\BSON\ObjectId;
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../login.php");
    exit();
} else {
    include_once("navbar.php");

    $ngoId = $_SESSION['user_id'];

    // Fetch all food items where status == 1
    $foodItemsCursor = $foodCollection->find(['status' => 1]);
    $foodItems = iterator_to_array($foodItemsCursor); // Convert cursor to array

    // Fetch all requests made by this NGO
    $requestsCursor = $foodRequestsCollection->find(['requested_by_id' => $ngoId]);
    $requests = [];
    foreach ($requestsCursor as $req) {
        $requests[(string)$req['food_id']] = $req['status']; // Map food_id to request status
    }
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
        <h1 class="mb-4">Hello, <?php echo $_SESSION['user_name']; ?>!<br> Here are the active shared meals</h1>
        <div class="row">
            <?php 
            if (count($foodItems) === 0) {
                echo "<p>No active food items available.</p>";
            } else {
                foreach ($foodItems as $item): 
                    $foodIdStr = (string)$item['_id'];
                    $statusLabel = '';
                    if(isset($requests[$foodIdStr])) {
                        switch($requests[$foodIdStr]) {
                            case 2:
                                $statusLabel = '<span class="btn btn-sm disabled" style="background-color: orange; color:white !important;">Requested</span>';
                                break;
                            case 3:
                                $statusLabel = '<span class="btn btn-sm disabled" style="background-color: green; color:white;">Approved</span>';
                                break;
                            case 4:
                                $statusLabel = '<span class="btn btn-sm disabled" style="background-color: red; color:white;">Declined</span>';
                                break;
                            default:
                                $statusLabel = '<span class="btn btn-sm disabled" style="background-color: gray; color:white;">Unknown</span>';
                        }
                    }
            ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><strong>Shared by:</strong> <?php echo htmlspecialchars($item['user_name']); ?></h5>
                                <h5 class="card-title"><?php echo htmlspecialchars($item['food_item']); ?></h5>
                                <p class="card-text">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($item['food_category']); ?><br>
                                    <strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?><br>
                                    <strong>Pickup Time:</strong> <?php echo htmlspecialchars($item['pickup_time']); ?><br>
                                    <strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?><br>
                                    <small class="text-muted">Published on: <?php echo htmlspecialchars($item['created_at']); ?></small>
                                </p>
                                <?php 
                                if(isset($requests[$foodIdStr])) {
                                    echo $statusLabel;
                                } else { ?>
                                    <form action="<?php echo $base_url;?>src/controller/taskController.php" method="post">
                                        <input type="hidden" name="food_id" value="<?php echo $item['_id']; ?>">
                                        <button type="submit" name="request_food" class="btn btn-sm btn-primary">Request</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
            <?php endforeach; } ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}
?>
