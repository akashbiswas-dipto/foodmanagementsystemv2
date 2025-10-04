<?php
interface User {
    public function getRole();
}

class Admin implements User {
    public function getRole() { return "Admin"; }
}

class Donor implements User {
    public function getRole() { return "Donor"; }
}

class NGO implements User {
    public function getRole() { return "NGO"; }
}

class UserFactory {
    public static function create($role) {
        return match($role) {
            'admin' => new Admin(),
            'donor' => new Donor(),
            'ngo' => new NGO(),
            default => throw new Exception("Invalid role"),
        };
    }
}
