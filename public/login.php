<?php include_once('navbar.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>public/css/login.css">
    <title>Login</title>
</head>
<body>
    <div class="box">
        <h1>Welcome to Food Waste Management System</h1>
        <p>Your one-stop solution to manage and reduce food waste effectively.</p>
        <form action="../src/controller/authController.php" method="post">
            <input type="email" name='email' placeholder="Enter your email" required><br>
            <input type="password" name='password'  placeholder="Enter your password" required><br>
            <button type="submit" name='login'>Login</button>
        </form>
        <?php
        if(isset($_GET['registration']) && $_GET['registration'] == 'success') {
            echo "<p style='color: green;'>Registration successful! Please log in.</p>";
        }
        else{
        ?>
        <a href="<?php echo $base_url;?>public/signup.php">Don't have an account? Sign up</a>
        <?php } ?>
    </div>
</body>
</html>