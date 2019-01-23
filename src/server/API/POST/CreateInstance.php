<?php

include "Helpers/DB/DBConnection.php";

class CreateInstance extends AbstractRestHandler {

    protected function requiresAuthentication(): bool {
        return true;
    }

    public function requiresBuffering(): bool {
        #return true;
        return false;
    }

    protected function process() {
        $this->setResponseCode(201);
        $this->postProcess();
    }

    protected function getParamKeys() {
        return ['DBType'];
    }

    protected function getParam($key) {        
        $request = $this->getRequest();
        $payload = $request->getJSONPayload();
        return $payload->{$key};
    }

    protected function checkDBType($dbType) {
        $validDBTypes = ['mysql'];
        if (!in_array($dbType, $validDBTypes)) {
            $this->addError(sprintf("Database type '%s' is not supported.", $dbType));
            return 0;
        }

        return 1;
    }

    public function postProcess() {
        $dbType = $this->getParam("DBType");

        $this->setStatusInit();


    }

    private function setStatusInit() {
        $dbConn = DBConnection::getInstance();
        $userID = SessionManager::getInstance()->getUserID();
        $status = "Initializing";

        $stmt = $dbConn->prepare("INSERT INTO Instances(USER_ID, STATUS) VALUES(?, ?)");
        $stmt->bind_param("ss", $userID, $status);
        $stmt->execute();
    }

    private function provisionVM() {

    }

}

?>