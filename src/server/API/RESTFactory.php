<?php

include "API/AbstractRestHandler.php";

class RESTFactory {

    public static function createRestHandler($request) {
        $handler = null;
        switch($request->getMethod()) {
            case 'GET':
                $handler = self::createGETHandler($request);
                break;
            case 'POST':
                $handler = self::createPOSTHandler($request);
                break;
            case 'PUT':
                $handler = self::createPUTHandler($request);
                break;
            case 'DELETE':
                $handler = self::createDELETEHandler($request);
        }

        if ($handler == null) {
            $handler = self::createNotFoundHandler($request);
        }

        return $handler;
    }

    private static function createGETHandler($request) {
        $handler = null;
        // switch($request->getAction()) {

        // }
        return $handler;
    }

    private static function createPOSTHandler($request) {
        $handler = null;
        switch($request->getAction()) {
            case "register":
                $handler = self::createFromClass('Register', $request);
                break;
            case 'login':
                $handler = self::createFromClass('Login', $request);
                break;
        }
        return $handler;
    }

    private static function createPUTHandler($request) {
        $handler = null;
        // switch($request->getAction()) {

        // }
        return $handler;
    }

    private static function createDELETEHandler($request) {
        $handler = null;
        // switch($request->getAction()) {

        // }
        return $handler;
    }

    private static function createFromClass($class, $request) {
        try {
            include_once "API/".strtoupper($request->getMethod())."/$class.php";
            return new $class($request);
        } catch (Throwable $e) {
            return self::createServerErrorHandler($request, $e->getMessage());
        }
    }

    private static function createNotFoundHandler($request) {
        return new class($request) extends AbstractRestHandler {
            public function __construct($request) {
                parent::__construct($request);
                $action = $request->getAction();
                $message = $action
                           ? "Couldn't find REST endpoint for action '$action'."
                           : "Couldn't find REST endpoint for this action";
                $this->addError($message);
            }

            public function process() { $this->setResponseCode(404); }

            public function getParam($key) { return null; }
        };
    }

    private static function createServerErrorHandler($request, $error) {
        return new class($request, $error) extends AbstractRestHandler {
            public function __construct($request, $error) {
                parent::__construct($request);
                $this->addError("Internal server error: $error");
            }

            public function process() { $this->setResponseCode(500); }

            public function getParam($key) { return null; }
        };
    }
}

?>