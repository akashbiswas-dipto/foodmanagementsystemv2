<?php
use MongoDB\BSON\ObjectId;

interface FoodInterface {
    public function showFood();
}

class RealFood implements FoodInterface {
    private $food;
    public function __construct($food) {  // remove "array" type
        $this->food = $food;
    }
    public function showFood() { 
        return (array)$this->food; // optionally cast to array
    }
}


class FoodProxy {
    private $foodId;
    private $role;
    private $userId;
    private $db;
    private $realFood;

    public function __construct($foodId, $role, $userId, $db) {
        $this->foodId = $foodId;
        $this->role = $role;
        $this->userId = $userId;
        $this->db = $db;
    }

    // Fetch food with access control
    public function getFood() {
        $filter = ['_id' => new ObjectId($this->foodId)];

        // Admin can access everything
       if (!in_array($this->role, [1,2,3])) {
            return null; // Access denied
        }

        // Donor can only access their own food
        if ($this->role == 2) { 
            $filter['donor_id'] = $this->userId;
        }


        $food = $this->db->food->findOne($filter);
        if (!$food) return null;

        if (!$this->realFood) {
            $this->realFood = new RealFood($food);
        }

        return $this->realFood->showFood();
    }

    // New method to match your previous code
    public function showFood() {
    $food = $this->getFood();
    return $food ? htmlspecialchars($food['food_item']) : "Access Denied / Deleted";
    }
}


