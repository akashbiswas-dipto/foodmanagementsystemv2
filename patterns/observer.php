<?php
interface Observer { 
    public function update($message); 
}
interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify();
}

class FoodSubject implements Subject {
    private $observers = [];
    private $message;
    public function attach(Observer $observer) { 
        $this->observers[] = $observer; 
    }
    public function detach(Observer $observer) { 
        $index = array_search($observer, $this->observers); 
        if($index!==false) unset($this->observers[$index]); 
    }
    public function setMessage($message) { 
        $this->message = $message; $this->notify(); 
    }
    public function notify() { 
        foreach($this->observers as $obs) $obs->update($this->message); 
    }
}

class NGOObserver implements Observer { 
    public function update($message) { 
        echo "NGO Notification: $message<br>"; 
    } 
}
class DonorObserver implements Observer { 
    public function update($message) { 
        echo "Donor Notification: $message<br>"; 
    } 
}
