<?php
use MongoDB\BSON\ObjectId;
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ../login.php");
    exit();
} 

include_once("navbar.php");

$ngoId = $_SESSION['user_id'];

// Fetch all requests made by this NGO
$requestsCursor = $foodRequestsCollection->find(['requested_by_id' => $ngoId]);
$requestedFoodIds = [];
$requestStatusMap = []; // Map food_id => request status

foreach ($requestsCursor as $req) {
    $foodIdStr = (string)$req['food_id'];
    $requestedFoodIds[] = new ObjectId($foodIdStr);
    $requestStatusMap[$foodIdStr] = $req['status'];
}

// Fetch the corresponding food items
$foodItems = [];
$donors = [];

if (!empty($requestedFoodIds)) {
    $foodItemsCursor = $foodCollection->find([
        '_id' => ['$in' => $requestedFoodIds]
    ]);
    $foodItems = iterator_to_array($foodItemsCursor);

    // Collect donor IDs to fetch their phone numbers
    $donorIds = [];
    foreach ($foodItems as $item) {
        $donorIds[(string)$item['user_id']] = true;
    }
    $donorIds = array_keys($donorIds);

    // Fetch donor info
    $donorsCursor = $usersCollection->find([
        '_id' => ['$in' => array_map(fn($id) => new ObjectId($id), $donorIds)]
    ]);

    foreach ($donorsCursor as $donor) {
        $donors[(string)$donor['_id']] = $donor;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/dashboard.css">
    <title>Requested Meals - <?php echo $_SESSION['user_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="box">
    <div class="container mt-4">
        <h1 class="mb-4">Hello, <?php echo $_SESSION['user_name']; ?>!<br> Here are the meals you requested</h1>
        <div class="row">
            <?php if (count($foodItems) === 0): ?>
                <p>You have not requested any meals yet.</p>
            <?php else: ?>
                <?php foreach ($foodItems as $item): 
                    $foodIdStr = (string)$item['_id'];
                    $requestStatus = $requestStatusMap[$foodIdStr] ?? null;
                    $donorIdStr = (string)$item['user_id'];
                    $donor = $donors[$donorIdStr] ?? null;
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
                                <small class="text-muted">Published on: <?php echo htmlspecialchars($item['created_at']); ?></small><br>
                                <strong>Request Status:</strong> 
                                <br>
                                <?php 
                                    if ($requestStatus == 1) echo "Pending for Approval";
                                    elseif ($requestStatus == 2) echo "<span style='color: orange;'>Pending for Approval</span>";
                                    elseif ($requestStatus == 3) {
                                        echo "<span style='color: #3BB143;'>Approved</span><br>";
                                        if ($donor) {
                                            echo "<strong>Donor Contact:</strong> " . htmlspecialchars($donor['phone']);
                                        }
                                    }
                                    elseif ($requestStatus == 4) echo "<span style='color: red;'>Declined</span>";
                                    else echo "<span style='color: red;'>Unknown/Deleted</span>";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
