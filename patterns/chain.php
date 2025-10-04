<?php
abstract class Handler {
    protected $next;

    public function setNext(Handler $handler) {
        $this->next = $handler;
        return $handler;
    }

    public function handle($request) {
        if ($this->next) {
            return $this->next->handle($request);
        }
        return true;
    }
}

// Example: Auth middleware
class AuthMiddleware extends Handler {
    public function handle($request) {
        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in!";
            return false;
        }
        return parent::handle($request);
    }
}

// Example: Validation middleware
class ValidationMiddleware extends Handler {
    public function handle($request) {
        if (empty($request['food_name'])) {
            echo "Food name cannot be empty!";
            return false;
        }
        return parent::handle($request);
    }
}
?>
