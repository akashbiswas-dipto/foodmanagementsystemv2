<?php
interface TaskStrategy { public function execute($tasks); }

class SortByDate implements TaskStrategy {
    public function execute($tasks) { usort($tasks, fn($a,$b)=>strtotime($a['date'])-strtotime($b['date'])); return $tasks; }
}

class SortByPriority implements TaskStrategy {
    public function execute($tasks) { 
        usort($tasks, fn($a,$b)=>$b['priority']-$a['priority']); 
        return $tasks; 
    }
}

class TaskController {
    private TaskStrategy $strategy;
    public function setStrategy(TaskStrategy $strategy) { 
        $this->strategy = $strategy; 
    }
    public function execute($tasks) { 
        return $this->strategy->execute($tasks); 
    }
}
