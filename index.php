<?php 
// Include config to get $base_url and BASE_PATH
include_once('config.php'); 

// Include navbar from public folder
include_once(BASE_PATH . 'public/navbar.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food Waste Management System</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/index.css">

<!-- Optional: Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<!-- Hero Section -->
<section class="hero text-center p-5 bg-success text-white">
    <div class="container">
        <h1>Welcome to Food Waste Management System</h1>
        <p>Reduce food waste. Share meals. Help the community.</p>
        <a href="<?php echo $base_url;?>public/login.php" class="btn btn-light btn-lg mx-2">Login</a>
        <a href="<?php echo $base_url;?>public/signup.php" class="btn btn-outline-light btn-lg mx-2">Sign Up</a>
    </div>
</section>

<!-- Features Section -->
<section class="features container text-center my-5">
    <h2 class="mb-5">Our Features</h2>
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card feature-card p-4 shadow-sm">
                <i class="bi bi-share-fill fs-1"></i>
                <h5 class="mt-3">Share Food</h5>
                <p>Donors can easily share surplus food with NGOs in need.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card p-4 shadow-sm">
                <i class="bi bi-people-fill fs-1"></i>
                <h5 class="mt-3">NGO Requests</h5>
                <p>NGOs can request meals and track the status of each request.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="text-center py-4 bg-light">
    <p>&copy; <?php echo date("Y"); ?> Food Waste Management System. All Rights Reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
