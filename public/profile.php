<?php
use MongoDB\BSON\ObjectId;

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include navbar dynamically based on user role
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 1:
            include_once("donor/navbar.php");
            break;
        case 2:
            include_once("donor/navbar.php");
            break;
        case 3:
            include_once("ngo/navbar.php");
            break;
        default:
            include_once("navbar.php"); // fallback
            break;
    }
}
// Fetch user details from MongoDB
$userId = new ObjectId($_SESSION['user_id']);
$user = $usersCollection->findOne(['_id' => $userId]);

if (!$user) {
    echo "<p>User not found.</p>";
    exit();
}

$roleNames = [
    1 => "Admin",
    2 => "Donor",
    3 => "NGO"
];
$role = $roleNames[$user['role']] ?? "Unknown";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/dashboard.css">
</head>
<body>
<div class="box">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-header text-center">
                <h3><?php echo htmlspecialchars($user['name']); ?>'s Profile</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Role:</strong> <?php echo $role; ?></p>
                <p><strong>Joined on:</strong> <?php echo htmlspecialchars(date("d M Y", strtotime($user['created_at']))); ?></p>
            </div>
            <div class="card-footer text-center">
                <a href="editProfile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
