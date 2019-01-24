<?php

trait RecordUpdater {

    function updateRecord($instanceID, $valuesMapping, $withErrorHandling = true) {
        $dbConn = DBConnection::getInstance();
        $kvPairs = [];
        foreach ($valuesMapping as $key => $value) {
            $escaped = $dbConn->real_escape_string($value);
            array_push($kvPairs, "`$key` = '$escaped'");
        }
        $kvPairsString = join(', ', $kvPairs);
        if (!$dbConn->query("UPDATE `Instances` SET $kvPairsString WHERE `INSTANCE_ID` = '$instanceID'") && $withErrorHandling) {
            $this->setStatusError($instanceID, $dbConn->error);
        }
    }

    function setStatusError($instanceID, $error) {
        $dbConn = DBConnection::getInstance();
        $userID = SessionManager::getInstance()->getUserID();
        $status = STATUS_ERROR;

        $this->updateRecord($instanceID, array(
            'STATUS' => $status,
            'ERROR'  => $error
        ), false); # Avoid infinite recursion
    }
}

?>