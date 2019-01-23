<?php

class Logout extends AbstractRestHandler {

    protected function requiresAuthentication(): bool {
        return true;
    }

    protected function process() {
        SessionManager::getInstance()->logoutUser();
    }
}

?>