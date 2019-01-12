<?php

session_start();

class SessionManager {
    
    private static $instance;

    private static function getInstance(): SessionManager {
        if (self::$instance == null) {
            self::$instance = new SessionManager();
        }
        return self::$instance;
    }

    # ---------------------------------------------------------------
    private $SESS_KEY = 'user_id';

    public function isUserLogged(): boolean {
        return isset($_SESSION[$SESS_KEY]) && !empty($_SESSION[$SESS_KEY]);
    }
}


?>