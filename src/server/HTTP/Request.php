<?php

class Request {
    private $requestMethod;
    private $requestPath;
    private $requestURL;
    private $userAgent;
    private $payload;

    public function __construct($cgiInfo) {
        $this->requestMethod    = $cgiInfo['REQUEST_METHOD'];
        $this->requestPath      = $cgiInfo['REQUEST_URI'];
        $this->requestURL       = $this->buildRequestURL($cgiInfo);
        $this->userAgent        = $cgiInfo['HTTP_USER_AGENT'];
        $this->payload          = null;
    }

    protected function buildRequestURL($cgiInfo) {
        $protocol   = isset($cgiInfo['HTTPS']) ? 'https://' : 'http://';
        $host       = $cgiInfo['HTTP_HOST'];
        $path       = $cgiInfo['REQUEST_URI'];
        return $protocol.$host.$path;   
    }

    public function getMethod() {
        return $this->requestMethod;
    }

    public function getPath() {
        return $this->requestPath;
    }

    public function getURL() {
        return $this->requestURL;
    }

    public function getUserAgent() {
        return $this->userAgent;
    }

    public function getJSONPayload() {
        if (!$this->payload) {
            $this->payload = json_decode(file_get_contents('php://input'));
        }
        return $this->payload;
    }
}

?>