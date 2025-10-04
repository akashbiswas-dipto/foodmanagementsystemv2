<?php
class TaskManager { 
    public function createTask($name){ 
    return "Task $name created"; 
} 
}
class Calendar { 
    public function addEvent($event){ 
        return "Event $event added"; 
    } 
}
class Notifier { 
    public function notify($msg){ 
        return "Notify: $msg"; 
    } 
}

class ProjectFacade {
    private $taskMgr; private $calendar; private $notifier;
    public function __construct() { 
        $this->taskMgr=new TaskManager(); 
        $this->calendar=new Calendar(); 
        $this->notifier=new Notifier(); 
    }
    public function createProject($taskName, $eventName, $msg){
        return $this->taskMgr->createTask($taskName) . " | " . $this->calendar->addEvent($eventName) . " | " . $this->notifier->notify($msg);
    }
}
