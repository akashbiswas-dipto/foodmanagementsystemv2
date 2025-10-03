<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 3)) {
    header("Location: ../login.php");
    exit();
}
else{
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://mop-zilla.com/"; 
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}
include_once(BASE_PATH.'config.php');

require_once __DIR__ . '/../vendor/autoload.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo $base_url;?>public/content/FWMlogo.ico">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
          <a class="nav-link active" aria-current="page" href="<?php echo $base_url;?>public/ngo/dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url;?>public/ngo/request.php">Requested Meals</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url;?>public/profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url;?>src/controller/authController.php?logout=logout">Sign Out</a>
        </li>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>
</html>

<?php } ?>