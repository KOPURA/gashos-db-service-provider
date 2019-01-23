<?php

class GetInstances extends AbstractRestHandler {

    protected function requiresAuthentication(): bool {
        return true;
    }

    protected function getResponseResult() {
        return ['asd', 'qwe', 'zxc'];
    }

    protected function process() {
        $userID = SessionManager::getInstance()->getUserID();
        // $instances = $this->getUserInstances($userID);
    }

    private function getUserInstances($userID) {
        $db = DBConnection::getInstance();
        $stmt = $db->prepare("SELECT * FROM Instances WHERE username=?");
    }
}

?>