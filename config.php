<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Paths
if (!defined('BASE_PATH')) define('BASE_PATH', realpath(__DIR__) . '/');
$base_url = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') 
    ? "http://{$_SERVER['HTTP_HOST']}/foodmanagementsystem/" 
    : 'http://localhost/foodmanagementsystem/';

// Autoload Composer
require_once BASE_PATH . 'vendor/autoload.php';
use MongoDB\Client;

// Singleton Database for MongoDB Atlas
class Database {
    private static $instance = null;
    private $client;
    private $db;

    private function __construct() {
        // Replace <db_username> and <db_password> with your actual credentials
        $username = "n12371661"; 
        $password = "n12371661admin"; 
        $dbname   = "foodmanagement";
        $cluster  = "foodmanagement.jrd7lmt.mongodb.net";

        // Encode username/password in case of special characters
        $username = urlencode($username);
        $password = urlencode($password);

        $uri = "mongodb+srv://n12371661:n12371661admin@foodmanagement.jrd7lmt.mongodb.net/foodmanagement?retryWrites=true&w=majority";
        $client = new MongoDB\Client($uri);
        $db = $client->foodmanagement; // your database name

        try {
            $this->client = new Client($uri);
            $this->db = $this->client->$dbname;
        } catch (Exception $e) {
            die("MongoDB Atlas Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new Database();
        return self::$instance;
    }

    public function getDB() { return $this->db; }
}

// Singleton App Config
class AppConfig {
    private static $instance = null;
    public $settings;

    private function __construct() {
        $this->settings = ['app_name' => 'FoodManagementSystem'];
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new AppConfig();
        return self::$instance;
    }
}

// Get database instance
$db = Database::getInstance()->getDB();
$config = AppConfig::getInstance();
