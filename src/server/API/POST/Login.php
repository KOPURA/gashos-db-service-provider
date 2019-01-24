<?php

include "Traits/PasswordValidator.php";
include "Helpers/DB/DBConnection.php";

class Login extends AbstractRestHandler {
    use PasswordValidator;

    private $userInfo = null;

    protected function getParamKeys() {
        return ['Username', 'Password'];
    }

    protected function process() {
        $this->setResponseCode(200);
    }

    protected function checkUsername($username) {
        if(strlen($username) < 1) {
            $this->addError("Username is empty.", "Username");
            return 0;
        }

        return 1;
    }

    protected function checkPassword($password) {
        if(strlen($password) < 1) {
            $this->addError("Password is empty.", "Password");
            return 0;
        }

        $username = $this->getParam("Username");
        $db = DBConnection::getInstance();
        $stmt = $db->prepare("SELECT * FROM Users WHERE username = ?");
        if (!$stmt) {
            $this->addError("Failed to prepare DB statement");
            return 0;
        }

        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            $this->addError("Failed to execute DB statement.");
            return 0;
        }

        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $this->addError("Invalid username or password", "Password");
            return 0;
        }

        $userRow = $result->fetch_assoc();
        if (!password_verify($password, $userRow['password'])) {
            $this->addError("Invalid username or password", "Password");
            return 0;
        }

        # Login has succeeded
        SessionManager::getInstance()->loginUser($userRow['ID']);

        return 1;
    }

    protected function getParam($key) {
        $request = $this->getRequest();
        $payload = $request->getJSONPayload();
        return $payload->{$key};
    }
}

?>