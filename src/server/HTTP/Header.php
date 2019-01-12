<?php

class Header {
    private $key;
    private $val;

    public function __construct($key, $value) {
        $this->key = $key;
        $this->val = $value;
    }

    public function getKey() {
        return $this->key;
    }

    public function getValue() {
        return $this->val;
    }

    public function asString() {
        $key   = $this->getKey();
        $value = $this->getValue();
        return "$key: $value";
    }

}

?>