<?php

session_start();

class SessionManager {
    
    private static $instance;

    public static function getInstance(): SessionManager {
        if (self::$instance == null) {
            self::$instance = new SessionManager();
        }
        return self::$instance;
    }

    # ---------------------------------------------------------------
    private $SESS_KEY = 'user_id';

    public function isUserLogged(): bool {
        return isset($_SESSION[$this->SESS_KEY]) && !empty($_SESSION[$this->SESS_KEY]);
    }

    public function loginUser($userID) {
        $_SESSION[$this->SESS_KEY] = $userID;
    }

    public function logoutUser() {
        $_SESSION[$this->SESS_KEY] = null;
    }

    public function getUserID() {
        if ($this->isUserLogged()) {
            return $_SESSION[$this->SESS_KEY];        
        }
        return null;
    }
}


?>