<?php

include 'Helpers/Util.php';

class RESTRequest extends Request {

    private $action;
    private $args;

    public function __construct($cgiInfo) {
        parent::__construct($cgiInfo);
        $this->parseRequestPath();
    }

    private function parseRequestPath() {
        $path = $this->getPath();
        $parts = explode('/', $_SERVER['REQUEST_URI']);
        $parts = array_values(array_filter($parts, 'stringNotEmpty'));
        array_shift($parts); # Shift off the first element - it is always the word 'api';
        $this->action = explode('?', array_shift($parts))[0]; # Remove the ? if exists
# After the action is shifted, all other parts of the path are arguments
# and will be stored in the order, in which they are given
        $this->args = $parts;
    }

    public function getAction() {
        return $this->action;
    }

    public function getArgs() {
        return $this->args;
    }
}

?>