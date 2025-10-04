<?php
declare(strict_types=1);

require_once '../config.php';
require_once '../src/controller/taskController.php';

header('Content-Type: application/json');

try {
    $controller = new FoodController($db, $base_url, $_SESSION['user_id'] ?? null, $_SESSION['user_name'] ?? null, $_SESSION['role'] ?? null);

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['food_id'])) throw new InvalidArgumentException("Food ID is required");

    $controller->deleteFood($data['food_id']);
    echo json_encode(['success' => true, 'message' => 'Food deleted successfully']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
