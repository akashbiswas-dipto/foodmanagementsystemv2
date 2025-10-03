<?php
session_start();
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
require_once(BASE_PATH . 'vendor/autoload.php');
use MongoDB\BSON\ObjectId;

if (isset($_POST['share_food'])) {
    // Get form values
    $foodItem = $_POST['food_item'] ?? '';
    $foodCategory = $_POST['food_category'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $pickupTime = $_POST['pickup_time'] ?? '';
    $location = $_POST['location'] ?? '';
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $status = $_POST['status'] ?? '';

    // Simple validation
    if ($foodItem && $foodCategory && $quantity && $pickupTime && $location) {
        try {
            $foodCollection->insertOne([
                'user_id' => $userId,
                'user_name' => $userName,
                'food_item' => $foodItem,
                'food_category' => $foodCategory,
                'quantity' => (int)$quantity,
                'pickup_time' => $pickupTime,
                'location' => $location,
                'status' => (int)$status,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Redirect after success
            header("Location: ".$base_url."public/donor/dashboard.php?success=Food Details Shared Successfully");
            exit();
        } catch (Exception $e) {
            die("Error inserting food data: " . $e->getMessage());
        }
    } else {
        die("Please fill in all required fields!");
    }
}

if (isset($_GET['Delete'])) {
    $foodId = $_GET['Delete'];

    try {
        $result = $foodCollection->deleteOne([
            '_id' => new ObjectId($foodId),
            'user_id' => $_SESSION['user_id'] // Ensure only owner can delete
        ]);

        if ($result->getDeletedCount() > 0) {
            header("Location: ".$base_url."public/donor/dashboard.php?success=Food item deleted successfully");
            exit();
        } else {
            die("Food item not found or you don't have permission to delete it.");
        }
    } catch (Exception $e) {
        die("Error deleting food item: " . $e->getMessage());
    }
}
if (isset($_POST['update_food'])) {
    $foodId = $_POST['food_id'] ?? '';
    $userId = $_SESSION['user_id'];

    if (!$foodId) {
        die("Food ID missing!");
    }

    try {
        $updateResult = $foodCollection->updateOne(
            [
                '_id' => new ObjectId($foodId),
                'user_id' => $userId // only update own food
            ],
            ['$set' => [
                'food_item' => $_POST['food_item'],
                'food_category' => $_POST['food_category'],
                'quantity' => (int)$_POST['quantity'],
                'pickup_time' => $_POST['pickup_time'],
                'location' => $_POST['location']
            ]]
        );

        if ($updateResult->getModifiedCount() > 0) {
            header("Location: ".$base_url."public/donor/dashboard.php?success=Food updated successfully");
        } else {
            header("Location: ".$base_url."public/donor/dashboard.php?error=No changes made or item not found");
        }
        exit();

    } catch (Exception $e) {
        die("Error updating food: ".$e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_food'])) {
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $foodId = $_POST['food_id'] ?? '';

    if ($foodId) {
        try {
            $foodRequestsCollection->insertOne([
                'food_id' => new ObjectId($foodId),
                'food_name' => $foodName,
                'requested_by_id' => $userId,
                'requested_by_name' => $userName,
                'status' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Redirect back to dashboard or wherever
            header("Location: ".$base_url."public/ngo/dashboard.php?success=Request sent successfully");
            exit();
        } catch (Exception $e) {
            die("Error creating request: " . $e->getMessage());
        }
    } else {
        die("Invalid food item selected!");
    }
} 

if(isset($_POST['approve_request'])){
    $requestId = new ObjectId($_POST['request_id']);

    // First, fetch the request to get the food_id
    $request = $foodRequestsCollection->findOne(['_id' => $requestId]);

    if($request){
        $foodId = $request['food_id'];

        // Update request status
        $foodRequestsCollection->updateOne(
            ['_id' => $requestId],
            ['$set' => ['status' => 3]]
        );

        // Update corresponding food item status to 2
        $foodCollection->updateOne(
            ['_id' => $foodId],
            ['$set' => ['status' => 2]]
        );
    }

    header("Location: ".$base_url."public/donor/ngorequest.php");
    exit();
}

// Decline request
if(isset($_POST['decline_request'])){
    $requestId = new ObjectId($_POST['request_id']);

    // Update request status
    $foodRequestsCollection->updateOne(
        ['_id' => $requestId],
        ['$set' => ['status' => 4]]
    );
    header("Location: ".$base_url."public/donor/ngorequest.php");
    exit();
}
?>