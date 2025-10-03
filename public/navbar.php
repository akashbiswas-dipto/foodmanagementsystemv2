<?php 
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}
require_once __DIR__ . '/../vendor/autoload.php'; // adjust path relative to file

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="<?php echo $base_url;?>public/content/FWMlogo.ico">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/navbar.css">
<title>Food Waste Management System</title>

<style>
/* Ensure navbar-toggler icon is visible on mobile */
.navbar-toggler-icon {
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30'
     xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255, 255, 255, 1%29' 
     stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
}

/* Stack menu items on mobile */
@media (max-width: 768px) {
    .navbar-nav {
        text-align: center;
    }
    .navbar-nav .nav-item {
        margin: 10px 0;
    }
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg" style="background-color: #3BB143 !important;">
  <div class="container">
    <a class="navbar-brand" href="<?php echo $base_url;?>">
      <img src="<?php echo $base_url;?>public/content/FWM.png" alt="Logo" width="50">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" href="<?php echo $base_url;?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url;?>public/login.php">Login/Signup</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
