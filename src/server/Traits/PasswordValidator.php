<?php

trait passwordValidator {

    function validatePassword($password, $passwordKey) {
        if (strlen($password) < 8) {
            $this->addError("Password must be at least 8 characters long", $passwordKey);
            return 0;
        }

        if (!preg_match("/[A-Z]/", $password)) {
            $this->addError("Password must contain at least one uppercase letter", $passwordKey);
            return 0;
        }

        if (!preg_match("/[a-z]/", $password)) {
            $this->addError("Password must contain at least one lowercase letter", $passwordKey);
            return 0;
        }

        if (!preg_match("/[0-9]/", $password)) {
            $this->addError("Password must contain at least one number", $passwordKey);
            return 0;
        }

        return 1;
    }
}

?>