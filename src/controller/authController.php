<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL & Path
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if ($host === 'localhost') {
    $base_url = "http://localhost/foodmanagementsystem/";
    if (!defined("BASE_PATH")) define("BASE_PATH", __DIR__ . '/../../'); // adjust as needed
} else {
    $base_url = "https://mop-zilla.com/";
    if (!defined("BASE_PATH")) define("BASE_PATH", __DIR__ . '/../../');
}

// Autoload Composer & Config
require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'patterns/factory.php';

use MongoDB\BSON\ObjectId;

// Initialize MongoDB Atlas DB
try {
    $db = Database::getInstance()->getDB(); // Database singleton from config.php
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ----------------- SIGNUP -----------------
if (isset($_POST['signup'])) {
    $name     = $_POST['name'] ?? '';
    $phone    = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($name && $phone && $location && $email && $password && $role) {
        try {
            // Check if user already exists
            $existing = $db->users->findOne(['email' => $email]);
            if ($existing) {
                echo "Email already registered!";
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $result = $db->users->insertOne([
                'name'       => $name,
                'phone'      => $phone,
                'location'   => $location,
                'email'      => $email,
                'password'   => $hashedPassword,
                'role'       => (int)$role,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Success
            header("Location: " . $base_url . "public/login.php?registration=success");
            exit();

        } catch (Exception $e) {
            die("Error registering user: " . $e->getMessage());
        }
    } else {
        echo "Please fill all fields!";
        exit();
    }
}

// ----------------- LOGIN -----------------
if (isset($_POST['login'])) {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $user = $db->users->findOne(['email' => $email]);
            echo var_dump($password, $user['password']);
            if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id']   = (string)$user['_id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['role']      = (int)$user['role'];
                    $role = isset($user['role']) ? (int)$user['role'] : 0;

                    switch ($role) {
                        case 1: 
                            $_SESSION['role_name'] = 'Self Donor';
                            header("Location: " . $base_url . "public/donor/dashboard.php");
                            break;

                        case 2:
                            $_SESSION['role_name'] = 'Restaurant';
                            header("Location: " . $base_url . "public/donor/dashboard.php");
                            break;

                        case 3:
                            echo " ngo here";
                            $_SESSION['role_name'] = 'NGO';
                            header("Location: " . $base_url . "public/ngo/dashboard.php");
                            break; 

                        case 4:
                            $_SESSION['role_name'] = 'Admin';
                            header("Location: " . $base_url . "public/admin/dashboard.php");
                            break;

                        default:
                            $_SESSION['role_name'] = 'Unknown';
                            echo "Role not recognized!";
                            exit();
                    }
                    exit();
                } else {
                    echo "Invalid email or password!";
                    exit();
                }

        } catch (Exception $e) {
            die("Error logging in: " . $e->getMessage());
        }
    } else {
        echo "Please enter email and password!";
        exit();
    }
}

// ----------------- LOGOUT -----------------
if (isset($_GET['logout']) && $_GET['logout'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: " . $base_url . "public/login.php");
    exit();
}
?>
