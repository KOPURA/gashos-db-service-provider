<?php

include 'API/IRestHandler.php';
include 'HTTP/Request.php';
include 'HTTP/Header.php';
include 'Helpers/Session/SessionManager.php';


abstract class AbstractRestHandler implements IRestHandler {

    private $request = null;

# ---------------- Public Methods -----------------------------------------

    public function __construct(Request $request) {
        $this->setRequest($request);
        $this->setResponseCode(200);
        $this->errors = array();
    }

    public function requiresAuthentication(): bool {
        return false;
    }

    public function respond(): Response {
        $isUserLogged = SessionManager::getInstance()->isUserLogged();
        if ($this->requiresAuthentication() && !$isUserLogged) {
            $this->handleUnauthenticatedRequest();
        } elseif(!$this->validateParams()) {
            $this->handleValidationError();
        } else {
            $this->processRequest();
        }

        $responseCode    = $this->getResponseCode();
        $responseHeaders = $this->getResponseHeaders();
        $responseBody    = json_encode($this->getResponseObject());
        return new Response($responseCode, $responseHeaders, $responseBody); 
    }

# ---------------- Protected Methods --------------------------------------

# Each handler defines what its behaviour will be
    protected abstract function process();

# Each handler defines how a given parameter is being retrieved
    protected abstract function getParam($key);


    protected function getParamKeys() {
        return [];
    }

    protected function getResponseResult() {
        return array();
    }

    protected function validateParams() {
        $result = True;
        foreach ($this->getParamKeys() as $key) {
            $checkerName = "check".$key;
            if (method_exists($this, $checkerName)) {
                $value = $this->getParam($key);
                $result = $this->$checkerName($value) && $result;
            }
        }
        return $result;
    }

    protected function getResponseObject() {
        if (!$this->isSuccess()) {
            return array(
                "errors" => $this->getErrors(),
            );
        }
        return $this->getResponseResult();
    }

    protected function isSuccess(): bool {
        $responseCode = $this->getResponseCode();
        return $responseCode >= 200 && $responseCode < 300;
    }

    protected function handleUnauthenticatedRequest() {
        $this->setResponseCode(401);
        $this->addError('User not logged-in');
    }

    protected function handleValidationError() {
        $this->setResponseCode(400);
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

    protected function addError($error, $section = null) {
        if ($section) {
            if (!isset($this->errors[$section])) {
                $this->errors[$section] = array();
            }
            array_push($this->errors[$section], $error);
        } else {
            array_push($this->errors, $error);        
        }
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