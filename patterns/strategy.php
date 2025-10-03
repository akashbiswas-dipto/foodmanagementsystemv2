<?php
interface SortStrategy {
    public function sort($tasks);
}

class DateSort implements SortStrategy {
    public function sort($tasks) {
        usort($tasks, fn($a,$b) => strtotime($a['date']) - strtotime($b['date']));
        return $tasks;
    }
}

class PrioritySort implements SortStrategy {
    public function sort($tasks) {
        usort($tasks, fn($a,$b) => $b['priority'] - $a['priority']);
        return $tasks;
    }
}

class TaskManager {
    private $strategy;
    public function __construct(SortStrategy $strategy) { $this->strategy = $strategy; }
    public function setStrategy(SortStrategy $strategy) { $this->strategy = $strategy; }
    public function getTasks($tasks) { return $this->strategy->sort($tasks); }
}
