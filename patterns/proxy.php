<?php
interface Report {
    public function view();
}

class FinancialReport implements Report {
    public function view() {
        return "Showing sensitive financial data...";
    }
}

class ReportProxy implements Report {
    private $userRole;
    private $realReport;

    public function __construct($userRole) {
        $this->userRole = $userRole;
        $this->realReport = new FinancialReport();
    }

    public function view() {
        if ($this->userRole === "admin") {
            return $this->realReport->view();
        }
        return "Access Denied: You are not an admin.";
    }
}
