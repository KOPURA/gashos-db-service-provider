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
        return isset($_SESSION[$SESS_KEY]) && !empty($_SESSION[$SESS_KEY]);
    }

    public function loginUser($userID) {
        $_SESSION[$SESS_KEY] = $userID;
    }
}


?>