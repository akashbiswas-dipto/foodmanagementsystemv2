<?php
require 'vendor/autoload.php'; // Make sure path is correct
use MongoDB\Client;
use MongoDB\Driver\ServerApi;

// MongoDB Atlas connection string
$uri = 'mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/?retryWrites=true&w=majority&appName=foodmanagement';

try {
    // Set Stable API version
    $apiVersion = new ServerApi(ServerApi::V1);

    // Create client
    $client = new Client($uri, [], ['serverApi' => $apiVersion]);

    // Select database
    $db = $client->selectDatabase('foodmanagement'); // replace with your database name

    // Select collections
    $usersCollection = $db->users;
    $foodCollection = $db->food;
    $foodRequestsCollection = $db->foodRequests;

} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
