<?php
use MongoDB\BSON\ObjectId;
session_start();

// Access control: only Admin (1) or Donor (2)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], [1,2])) {
    header("Location: ../login.php");
    exit();
}
else{
include_once("navbar.php");
require_once BASE_PATH . 'config.php';

// Include Observer pattern
require_once BASE_PATH . 'patterns/observer.php';

// Initialize collections
$donorId = $_SESSION['user_id'];
$foodCollection = $db->food;
$foodRequestsCollection = $db->foodRequestsCollection;

// Fetch all food items shared by this donor
$foodItemsCursor = $foodCollection->find(['donor_id' => $donorId]);
$foodItems = iterator_to_array($foodItemsCursor);

// Prepare Observer
require_once BASE_PATH . 'patterns/observer.php';
$requestsSubject = new FoodSubject();
$requestsSubject->attach(new NGOObserver());

// Get IDs of donor's food items
$foodIds = array_map(fn($item) => $item['_id'], $foodItems);

// Fetch all requests made to these food items
$requests = [];
if (!empty($foodIds)) {
    $requestsCursor = $foodRequestsCollection->find([
        'food_id' => ['$in' => $foodIds]
    ]);
    $requests = iterator_to_array($requestsCursor);

    // Notify NGOs about new requests
    foreach ($requests as $req) {
        $foodItem = $foodCollection->findOne(['_id' => new ObjectId((string)$req['food_id'])]);
        $requestsSubject->setMessage("New request for " . $foodItem['food_item']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= $base_url ?>public/css/dashboard.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Pending NGO Requests</title>
</head>
<body>
<div class="box">
    <div class="container mt-4">
        <h1 class="mb-4">Pending NGO Requests</h1>
        <div class="row">
            <?php if (empty($requests)): ?>
                <p>No requests have been made to your shared meals yet.</p>
            <?php else: ?>
                <?php foreach ($requests as $req):
                    $foodItem = $foodCollection->findOne(['_id' => new ObjectId((string)$req['food_id'])]);
                    if (!$foodItem) continue;
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($foodItem['food_item']) ?></h5>
                                <p class="card-text">
                                    <strong>Requested by:</strong> <?= htmlspecialchars($req['requested_by_name']) ?><br>
                                    <strong>Quantity:</strong> <?= htmlspecialchars($foodItem['quantity']) ?><br>
                                    <strong>Pickup Time:</strong> <?= htmlspecialchars($foodItem['pickup_time']) ?><br>
                                    <strong>Location:</strong> <?= htmlspecialchars($foodItem['location']) ?><br>
                                    <?php if ($req['status'] == 2): // pending ?>
                                        <form action="<?= $base_url ?>src/controller/taskController.php" method="post">
                                            <input type="hidden" name="request_id" value="<?= $req['_id'] ?>">
                                            <button type="submit" name="approve_request" class="btn btn-success btn-sm">Approve</button>
                                            <button type="submit" name="decline_request" class="btn btn-danger btn-sm">Decline</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-primary">
                                            <?= $req['status'] == 3 ? 'Approved' : 'Declined' ?>
                                        </span>
                                    <?php endif; ?>
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
<?php
} // end else for access control
?>