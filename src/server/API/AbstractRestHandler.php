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
        $responseBody    = $this->getResponseBody();
        return new Response($responseCode, $responseHeaders, $responseBody); 
    }

# The buffering might be useful if the handler should return a response
# But continue to process other stuff after that
    public function requiresBuffering(): bool {
        return false;
    }

# If the handler requires buffering, the postProcess method is called after the response
# has been already sent
    public function postProcess() {
        return true;
    }

# ---------------- Protected Methods --------------------------------------

# Each handler defines what its behaviour will be
    protected abstract function process();

# Each handler defines how a given parameter is being retrieved
    protected function getParam($key) {
        return null;
    }

# Each handler defines whether it requires that the user has logged in
    protected function requiresAuthentication(): bool {
        return false;
    }

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

    protected function getResponseBody() {
        return json_encode($this->getResponseObject());
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