<?php
declare(strict_types=1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/controller/taskController.php';


if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = '68e0120059a1af5c240c370e'; 
    $_SESSION['user_name'] = 'Akash Biswas';
    $_SESSION['role'] = 2; // donor role
}

// Instantiate controller
$controller = new FoodController($db, $base_url);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json');

try {
    // Call shareFood method
    $controller->shareFood($input);

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Food shared successfully'
    ]);
} catch (RuntimeException $e) {
    // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (InvalidArgumentException $e) {
    // Validation error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    // Other errors
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
