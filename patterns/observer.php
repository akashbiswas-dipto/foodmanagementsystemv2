<?php
interface Observer {
    public function update($message);
}

class NGOObserver implements Observer {
    private $name;
    public function __construct($name) { $this->name = $name; }
    public function update($message) {
        echo "NGO {$this->name} received notification: $message <br>";
    }
}

class FoodDonation {
    private $observers = [];

    public function attach(Observer $observer) { $this->observers[] = $observer; }

    public function notify($message) {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }

    public function newDonation($foodName) {
        $this->notify("New food available: $foodName");
    }
}
