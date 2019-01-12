<?php

include 'API/IRestHandler.php';
include 'HTTP/Request.php';
include 'HTTP/Response.php';
include 'HTTP/Header.php';
include 'Helpers/Session/LoginManager.php';


abstract class AbstractRestHandler implements IRestHandler {

    private $request = null;

# ---------------- Public Methods -----------------------------------------

    public function __construct(Request $request) {
        $this->setRequest($request);
        $this->setResponseCode(200);
        $this->errors = array();
    }

    public function requiresAuthentication(): boolean {
        return false;
    }

    public function respond(): Response {
        $isUserLogged = LoginManager::getInstance()->isUserLogged();
        if ($this->requiresAuthentication() && !$isUserLogged) {
            $this->handleUnauthenticatedRequest();
        } else {
            $this->processRequest();
        }

        $responseCode    = $this->getResponseCode();
        $responseHeaders = $this->getResponseHeaders();
        $responseBody    = json_encode($this->getResponseStructure());
        return new Response($responseCode, $responseHeaders, $responseBody); 
    }

# ---------------- Protected Methods --------------------------------------

    protected abstract function process();

    protected function getResponseStructure() {
        if (!$this->isSuccess()) {
            return array(
                "errors" => $this->getErrors(); 
            );
        }
        return ((object)[]);
    }

    protected function isSuccess(): boolean {
        $responseCode = $this->getResponseCode();
        return $responseCode >= 200 && $responseCode < 300;
    }

    protected function handleUnauthenticatedRequest() {
        $this->setResponseCode(401);
        $this->addError('User not logged-in');
    }

    protected function processRequest() {
        try {
            $this->process();
        } catch (Throwable $e) {
            $this->setResponseCode(500);
            $this->addError($e->getMessage());
        }
    }

    protected function getResponseCode(): int {
        return $this->responseCode;
    }

    protected function setResponseCode(int $responseCode) {
        $this->responseCode = $responseCode;
    }

    protected function getResponseHeaders() {
        return [ new Header("Content-Type", "application/json") ];
    }

    protected function addError($error) {
        array_push($this->errors, $error);
    }

    protected function getErrors() {
        return $this->errors;
    }

    protected function getRequest(): Request {
        return $this->request;
    }

    protected function setRequest(Request $request) {
        $this->request = $request;
    }

}

?>