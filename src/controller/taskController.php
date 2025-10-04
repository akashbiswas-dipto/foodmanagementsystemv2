<?php
declare(strict_types=1);

use MongoDB\BSON\ObjectId;

class FoodController {
    private $db;
    private $base_url;
    private $userId;
    private $userName;
    private $role;
    private $redirectHandler;

    /**
     * @param callable|null $redirectHandler Optional redirect function for testing
     */
    public function __construct($db, string $base_url, ?string $userId = null, ?string $userName = null, ?int $role = null, ?callable $redirectHandler = null) {
        $this->db = $db;
        $this->base_url = rtrim($base_url, '/') . '/';
        $this->userId = $userId ?? ($_SESSION['user_id'] ?? null);
        $this->userName = $userName ?? ($_SESSION['user_name'] ?? null);
        $this->role = $role ?? ($_SESSION['role'] ?? null);
        $this->redirectHandler = $redirectHandler ?? function(string $url) {
            header("Location: " . $url);
            exit();
        };
    }

    // --------------------- FOOD ---------------------
    public function shareFood(array $data): void {
        $this->ensureLoggedIn();
        $required = ['food_item', 'food_category', 'quantity', 'pickup_time', 'location'];
        foreach ($required as $field) {
            $value = (string)($data[$field] ?? '');
            if (empty(trim($value))) {
                throw new InvalidArgumentException("Field '$field' is required");
            }
        }

        $this->db->food->insertOne([
            'donor_id' => $this->userId,
            'user_name' => $this->userName,
            'food_item' => trim($data['food_item']),
            'food_category' => $data['food_category'],
            'quantity' => (int)$data['quantity'],
            'pickup_time' => $data['pickup_time'],
            'location' => $data['location'],
            'status' => (int)($data['status'] ?? 1),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->redirect("public/donor/dashboard.php?success=Food shared successfully");
    }

    public function updateFood(array $data): void {
        $this->ensureLoggedIn();
        if (empty($data['food_id'])) throw new InvalidArgumentException("Food ID missing");

        $updateResult = $this->db->food->updateOne(
            ['_id' => new ObjectId($data['food_id']), 'donor_id' => $this->userId],
            ['$set' => [
                'food_item' => trim($data['food_item']),
                'food_category' => $data['food_category'],
                'quantity' => (int)$data['quantity'],
                'pickup_time' => $data['pickup_time'],
                'location' => $data['location']
            ]]
        );

        $msg = ($updateResult->getModifiedCount() > 0) ? "Food updated successfully" : "No changes made";
        $this->redirect("public/donor/dashboard.php?" . ($updateResult->getModifiedCount() > 0 ? "success=$msg" : "error=$msg"));
    }

    public function deleteFood(string $foodId): void {
        $this->ensureLoggedIn();

        $result = $this->db->food->deleteOne([
            '_id' => new ObjectId($foodId),
            'donor_id' => $this->userId
        ]);

        $msg = ($result->getDeletedCount() > 0) ? "Food deleted successfully" : "Permission denied or not found";
        $this->redirect("public/donor/dashboard.php?" . ($result->getDeletedCount() > 0 ? "success=$msg" : "error=$msg"));
    }

    public function requestFood(string $foodId): void {
        $this->ensureLoggedIn();

        $existing = $this->db->food_requests->findOne([
            'food_id' => new ObjectId($foodId),
            'requested_by_id' => $this->userId
        ]);

        if ($existing) {
            $this->redirect("public/ngo/dashboard.php?error=Already requested");
        }

        $this->db->food_requests->insertOne([
            'food_id' => new ObjectId($foodId),
            'requested_by_id' => $this->userId,
            'status' => 2,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->redirect("public/ngo/dashboard.php?success=Food requested successfully");
    }

    public function approveRequest(string $requestId): void {
        $this->ensureLoggedIn();
        $this->db->food_requests->updateOne(['_id' => new ObjectId($requestId)], ['$set' => ['status' => 3]]);
        $this->redirect("public/donor/dashboard.php?success=Request approved");
    }

    public function declineRequest(string $requestId): void {
        $this->ensureLoggedIn();
        $this->db->food_requests->updateOne(['_id' => new ObjectId($requestId)], ['$set' => ['status' => 4]]);
        $this->redirect("public/donor/dashboard.php?success=Request declined");
    }

    private function ensureLoggedIn(): void {
        if (!$this->userId) throw new RuntimeException("Unauthorized");
    }

    private function redirect(string $url): void {
        call_user_func($this->redirectHandler, $this->base_url . $url);
    }
}
