<?php
use MongoDB\BSON\ObjectId;
session_start();

// Base Config & DB
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    if (!defined("BASE_PATH")) define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'patterns/observer.php';
require_once BASE_PATH . 'patterns/proxy.php';
require_once BASE_PATH . 'patterns/chain.php';

// Ensure NGO login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 3) {
    header("Location: ".$base_url."public/login.php");
    exit();
}

include_once("navbar.php");

// Observer + Middleware
$subject = new FoodSubject();
$subject->attach(new NGOObserver());
$auth = new AuthMiddleware();

// Collections
$foodCollection = $db->food;
$foodRequestsCollection = $db->food_requests;
$usersCollection = $db->users;

$ngoId = $_SESSION['user_id'];

// Fetch all requests made by this NGO
$requestsCursor = $foodRequestsCollection->find(['requested_by_id' => $ngoId]);
$requestedFoodIds = [];
$requestStatusMap = [];

foreach ($requestsCursor as $req) {
    if (!empty($req['food_id'])) {
        try {
            $foodIdStr = (string)$req['food_id'];
            $requestedFoodIds[] = new ObjectId($foodIdStr);
            $requestStatusMap[$foodIdStr] = $req['status'] ?? 0;
        } catch (Exception $e) {
            // skip invalid ObjectId
            continue;
        }
    }
}

// Fetch corresponding food items
$foodItems = [];
$donors = [];

if (!empty($requestedFoodIds)) {
    $foodItemsCursor = $foodCollection->find([
        '_id' => ['$in' => $requestedFoodIds]
    ]);
    $foodItems = iterator_to_array($foodItemsCursor);

    // Collect donor IDs safely
    $donorIds = [];
    foreach ($foodItems as $item) {
        if (!empty($item['user_id'])) {
            try {
                $donorIds[(string)$item['user_id']] = true;
            } catch (Exception $e) {
                continue;
            }
        }
    }
    $donorIds = array_keys($donorIds);

    if (!empty($donorIds)) {
        $donorsCursor = $usersCollection->find([
            '_id' => ['$in' => array_map(fn($id) => new ObjectId($id), $donorIds)]
        ]);

        foreach ($donorsCursor as $donor) {
            $donors[(string)$donor['_id']] = $donor;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>public/css/dashboard.css">
    <title>Requested Meals - <?= htmlspecialchars($_SESSION['user_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="box">
        <div class="container mt-4">
            <h1 class="mb-4">Hello, <?= htmlspecialchars($_SESSION['user_name']); ?>!<br> Here are the meals you requested</h1>
            <div class="row">
                <?php if (count($foodItems) === 0): ?>
                    <p>You have not requested any meals yet.</p>
                <?php else: ?>
                    <?php foreach ($foodItems as $item): 
                        $foodIdStr = (string)$item['_id'];
                        $requestStatus = $requestStatusMap[$foodIdStr] ?? null;
                        $donorIdStr = isset($item['user_id']) ? (string)$item['user_id'] : null;
                        $donor = $donorIdStr && isset($donors[$donorIdStr]) ? $donors[$donorIdStr] : null;

                        // Proxy: secure food info
                        $foodProxy = new FoodProxy(
                            (string)$item['_id'], // ✅ correct ObjectId string
                            $_SESSION['role'],
                            $donor ? (string)$donor['_id'] : null,
                            $db
                        );
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><strong>Shared by:</strong> <?= htmlspecialchars($item['user_name'] ?? 'Unknown'); ?></h5>
                                <h5 class="card-title"><?= $foodProxy->showFood(); ?></h5>
                                <p class="card-text">
                                    <strong>Category:</strong> <?= htmlspecialchars($item['food_category'] ?? 'N/A'); ?><br>
                                    <strong>Quantity:</strong> <?= htmlspecialchars($item['quantity'] ?? 'N/A'); ?><br>
                                    <strong>Pickup Time:</strong> <?= htmlspecialchars($item['pickup_time'] ?? 'N/A'); ?><br>
                                    <strong>Location:</strong> <?= htmlspecialchars($item['location'] ?? 'N/A'); ?><br>
                                    <small class="text-muted">Published on: <?= htmlspecialchars($item['created_at'] ?? ''); ?></small><br>
                                    <strong>Request Status:</strong><br>
                                    <?php 
                                        if ($requestStatus == 2) echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                        elseif ($requestStatus == 3) {
                                            echo "<span class='badge bg-success'>Approved</span><br>";
                                            if ($donor) echo "<strong>Donor Contact:</strong> ".htmlspecialchars($donor['phone'] ?? '');
                                        }
                                        elseif ($requestStatus == 4) echo "<span class='badge bg-danger'>Declined</span>";
                                        else echo "<span class='badge bg-secondary'>Unknown/Deleted</span>";
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
