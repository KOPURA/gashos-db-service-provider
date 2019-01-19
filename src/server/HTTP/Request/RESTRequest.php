<?php

class RESTRequest extends Request {
    private $action;
    private $args;

    public function __construct($cgiInfo) {
        parent::__construct($cgiInfo);
    }

    public function getAction() {
        return $_GET['action'];
    }
}

?>