<?php

include "Helpers/DB/DBConnection.php";

class GetInstances extends AbstractRestHandler {

    private $instances = [];

    protected function requiresAuthentication(): bool {
        return true;
    }

    protected function process() {
        $userID = SessionManager::getInstance()->getUserID();
        $this->instances = $this->getUserInstances($userID);
    }

    protected function getResponseResult() {
        return $this->instances;
    }

    private function getUserInstances($userID) {
        $instances = [];
        $db = DBConnection::getInstance();

        $stmt = $db->prepare("SELECT ID, STATUS, PUBLIC_DNS, ERROR FROM Instances WHERE USER_ID=?");

        $stmt->bind_param("i", $userID);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $instances = $result->fetch_all(MYSQLI_ASSOC);
        }

        return $instances;
    }
}

?>