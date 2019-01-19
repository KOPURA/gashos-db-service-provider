<?php

class DBConnection extends mysqli {

    private static $instance = null;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    # ------------------------------------------------------------
    private $DB_USER = "Admin";
    private $DB_PWD  = "Admin";
    private $DB_NAME = "Database";
    private $DB_HOST = "mysql";

    private function __construct() {
        parent::__construct($this->DB_HOST, $this->DB_USER, $this->DB_PWD, $this->DB_NAME);
        if ($this->connect_error) {
            die(sprintf("Failed to connect to database (%s): %s", $this->connect_errno, $this->connect_error));
        }
    }

    public function __destruct() {
        $this->close();
    }
}

?>