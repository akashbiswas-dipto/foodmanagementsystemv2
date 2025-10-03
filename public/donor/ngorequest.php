<?php
use MongoDB\BSON\ObjectId;
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) ||($_SESSION['role'] != 1 && $_SESSION['role'] != 2) ) { // Donor role = 1
    header("Location: ../login.php");
    exit();
} else {
    include_once("navbar.php");

    $donorId = $_SESSION['user_id'];

    // Fetch all food items shared by this donor
    $foodItemsCursor = $foodCollection->find(['user_id' => $donorId]);
    $foodItems = iterator_to_array($foodItemsCursor);

    // Get IDs of donor's food items
    $foodIds = [];
    foreach ($foodItems as $item) {
        $foodIds[] = $item['_id'];
    }

    // Fetch all requests made to these food items
    if (!empty($foodIds)) {
        $requestsCursor = $foodRequestsCollection->find([
            'food_id' => ['$in' => $foodIds]
        ]);
        $requests = iterator_to_array($requestsCursor);
    } else {
        $requests = [];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url;?>public/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Pending NGO Requests</title>
</head>
<body>
<div class="box">
    <div class="container mt-4">
        <h1 class="mb-4">Pending NGO Requests</h1>
        <div class="row">
            <?php if (count($requests) === 0): ?>
                <p>No requests have been made to your shared meals yet.</p>
            <?php else: ?>
                <?php foreach ($requests as $req):
                    // Find food item info
                    $foodItem = $foodCollection->findOne(['_id' => new ObjectId((string)$req['food_id'])]);
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($foodItem['food_item']); ?></h5>
                                <p class="card-text">
                                    <strong>Requested by:</strong> <?php echo htmlspecialchars($req['requested_by_name']); ?><br>
                                    <strong>Quantity:</strong> <?php echo htmlspecialchars($foodItem['quantity']); ?><br>
                                    <strong>Pickup Time:</strong> <?php echo htmlspecialchars($foodItem['pickup_time']); ?><br>
                                    <strong>Location:</strong> <?php echo htmlspecialchars($foodItem['location']); ?><br>
                                    <?php if ($req['status'] == 2): // show buttons only if pending ?>
                                    <form action="<?php echo $base_url;?>src/controller/taskController.php" method="post">
                                        <input type="hidden" name="request_id" value="<?php echo $req['_id']; ?>">
                                        <button type="submit" name="approve_request" class="btn btn-success btn-sm">Approve</button>
                                        <button type="submit" name="decline_request" class="btn btn-danger btn-sm">Decline</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-primary">
                                        <?php echo ($req['status'] == 3 ? 'Approved' : 'Declined'); ?>
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
}
?>
