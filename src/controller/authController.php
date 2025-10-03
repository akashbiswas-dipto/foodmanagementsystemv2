<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/"; 
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/foodmanagementsystem/");
} else {
    $base_url = "https://foodmanagementsystem/"; 
    define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
}
include_once(BASE_PATH.'config.php');  
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<pre>';
print_r($_POST);
echo '</pre>';
if (isset($_POST['signup'])) {
    echo 'i am here';
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Simple validation (you can expand)
    if ($name && $phone && $location && $email && $password && $role) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into MongoDB
        try {
            $usersCollection->insertOne([
                'name' => $name,
                'phone' => $phone,
                'location' => $location,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => (int)$role,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $success = "User registered successfully!";
            header("Location: ".$base_url."public/login.php?registration=success");
            exit();
        } catch (Exception $e) {
            $error = "Error registering user: " . $e->getMessage();
        }
    } else {
        $error = "Please fill all fields!";
    }
}

if(isset($_POST['login'])) {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $user = $usersCollection->findOne(['email' => $email]);
            if ($user && password_verify($password, $user['password'])) {
                
                session_start();
                $_SESSION['user_id'] = (string)$user['_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                if($user['role'] == 1 || $user['role'] === '1' || $user['role'] == 2 || $user['role'] === '2'){
                    $_SESSION['role_name'] = 'Self Donor';
                    header("Location: ".$base_url."public/donor/dashboard.php");
                    exit();
                } elseif($user['role'] == 3 || $user['role'] === '3'){
                    $_SESSION['role_name'] = 'NGO';
                    header("Location: ".$base_url."public/ngo/dashboard.php");
                    exit();
                } elseif($user['role'] == 4 || $user['role'] === '4'){
                    $_SESSION['role_name'] = 'Admin';
                    header("Location: ".$base_url."public/admin/dashboard.php");
                    exit();
                } else {
                    $_SESSION['role_name'] = 'Unknown';
                }
            } else {
                echo "Invalid email or password!";
            }
        } catch (Exception $e) {
            echo "Error logging in: " . $e->getMessage();
        }
    } else {
        echo "Please enter email and password!";
    }
}

if(isset($_GET['logout']) && $_GET['logout'] == 'logout') {
    session_start();
    session_unset();
    session_destroy();
    header("Location: ".$base_url."public/login.php");
    exit();
}
?>