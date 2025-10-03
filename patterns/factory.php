<?php
abstract class User {
    abstract public function getRole();
}

class Donor extends User {
    public function getRole() { return "Donor"; }
}

class NGO extends User {
    public function getRole() { return "NGO"; }
}

class UserFactory {
    public static function createUser($type) {
        switch (strtolower($type)) {
            case "donor": return new Donor();
            case "ngo": return new NGO();
            default: throw new Exception("Invalid user type");
        }
    }
}
