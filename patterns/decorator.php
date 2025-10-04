<?php
interface DashboardInterface { public function display(); }

class Dashboard implements DashboardInterface {
    protected $content;
    public function __construct($content) { 
        $this->content = $content; 
    }
    public function display() { 
        return $this->content; 
    }
}

class DashboardDecorator implements DashboardInterface {
    protected $dashboard;
    public function __construct(DashboardInterface $dashboard) { 
        $this->dashboard = $dashboard; 
    }
    public function display() { 
        return $this->dashboard->display(); 
    }
}

class GraphDecorator extends DashboardDecorator {
    public function display() { 
        return parent::display() . " | [Graph Added]"; 
    }
}

class AlertsDecorator extends DashboardDecorator {
    public function display() { 
        return parent::display() . " | [Alerts Added]"; 
    }
}
