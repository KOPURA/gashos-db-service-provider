<?php

include "Traits/PasswordValidator.php";
include "Helpers/DB/DBConnection.php";

class Register extends AbstractRestHandler {
    use passwordValidator;

    protected function getParamKeys() {
        return ['Username', 'Password'];
    }

    protected function process() {
        $username = $this->getParam('Username');
        $password = $this->getParam('Password');

        $db = DBConnection::getInstance();
        if ($stmt = $db->prepare("INSERT INTO Users (username, password) VALUES (?, ?)")) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("ss", $username, $passwordHash);

            if ($stmt->execute()) {
                $this->setResponseCode(201);
            } else {
                $this->addError(sprintf("DB Insert Error occured (%s): %s", $db->connect_errno, $db->connect_error));
                $this->setResponseCode(500);
            }
        } else {
            $this->addError(sprintf("Failed to prepare insert statement (%s): %s", $db->errno, $db->error));
            $this->setResponseCode(500);
        }
    }

    protected function getParam($key) {
        $request = $this->getRequest();
        $payload = $request->getJSONPayload();
        return $payload->{$key};
    }

    protected function checkUsername($username) {
        if (strlen($username) < 1) {
            $this->addError("Username cannot be empty", "Username");
            return False;
        }

        $db = DBConnection::getInstance();
        $stmt = $db->prepare("SELECT * FROM Users WHERE username=?");
        if (!$stmt) {
            $this->addError(sprintf("Failed to prepare select statement (%s): %s", $db->errno, $db->error));
            return False;
        }

        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            $this->addError(sprintf("Failed to execute select statement (%s): %s", $db->errno, $db->error));
            return False;
        }

        if ($stmt->get_result()->num_rows > 0){
            $this->addError("This username already exists", "Username");
            return False;
        }

        return True;
    }

    protected function checkPassword($password) {
        return $this->validatePassword($password, "Password"); # From the trait
    }
};

?>