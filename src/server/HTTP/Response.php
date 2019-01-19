<?php

class Response {
    private $responseHeaders;
    private $responseCode;
    private $responseBody;
 
    public function __construct($responseCode, array $headers = [], $responseBody) {
        $this->responseHeaders = array();
        foreach($headers as $header) {
            $this->addHeader($header);
        }
        $this->setResponseCode($responseCode);
        $this->setResponseBody($responseBody);
    }
 
    private function addHeader($header) {
        array_push($this->responseHeaders, $header);
    }

    private function setResponseCode($responseCode) {
        $this->responseCode = $responseCode;
    }

    private function setResponseBody($responseBody) {
        $this->responseBody = $responseBody;
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function getResponseBody() {
        return $this->responseBody;
    }

    public function getHeaders() {
        return $this->responseHeaders;
    }

    public function getStatusString() {
        switch ($this->getResponseCode()) {   
            case 200: return "OK";
            case 400: return "Bad Request";
            case 401: return "Unauthorized";
            case 403: return "Forbidden";
            case 404: return "Not Found";
            case 408: return "Request Time-out";
            case 500: return "Internal server error";
            default: return $statusCode < 400 ? "OK" : "Error";
        }
    }
 
    public function getStatusLine() {
        $responseCode = $this->getResponseCode();
        $statusString = $this->getStatusString();
        return "HTTP/1.1 $responseCode $statusString";
    }
}

?>