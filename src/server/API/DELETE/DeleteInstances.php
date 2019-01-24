<?php

include "Helpers/DB/DBConnection.php";
include "AWS/aws-autoloader.php";
include 'Traits/RecordUpdater.php';

use Aws\Ec2\Ec2Client;

const STATUS_STOPPING = 'Stopping';

class DeleteInstances extends AbstractRestHandler {
    use RecordUpdater;

    protected function requiresAuthentication(): bool {
        return true;
    }

    public function requiresBuffering(): bool {
        return true;
    }

    protected function process() {
        $this->setResponseCode(200);
    }

    protected function getParamKeys() {
        return ['InstanceID'];
    }

    protected function getParam($key) {
        $request = $this->getRequest();
        $args = $request->getArgs();

        if (sizeof($args) == 0) {
            return '';
        }

        if ($key == 'InstanceID') {
            return $args[0];
        }

        return '';
    }

    protected function checkInstanceID($instanceID) {
        $dbConn = DBConnection::getInstance();
        $stmt = $dbConn->prepare("SELECT AWS_INSTANCE_ID FROM `Instances` WHERE `INSTANCE_ID` = ?");
        if (!$stmt) {
            $this->addError("Failed to prepare DB statement");
            return 0;
        }

        $stmt->bind_param("s", $instanceID);
        if (!$stmt->execute()) {
            $this->addError("Failed to execute DB statement.");
            return 0;
        }

        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $this->addError("Instance with id '$instanceID' does not exist.");
            return 0;
        }

        $row = $result->fetch_assoc();
        $this->setAWSInstanceID($row['AWS_INSTANCE_ID']);

        return 1;
    }

    public function postProcess() {
        $awsID = $this->getAWSInstanceID();

        $this->terminateVM($awsID);
        $this->deleteDBRecord();
    }

    private function deleteDBRecord() {
        $instanceID = $this->getParam('InstanceID');
        $dbConn = DBConnection::getInstance();

        $stmt = $dbConn->prepare("DELETE FROM `Instances` WHERE `INSTANCE_ID` = ?");
        $stmt->bind_param('s', $instanceID);
        $stmt->execute();
    }

    private function terminateVM($awsID) {
        $instanceID = $this->getParam('InstanceID');

        $ec2Client = Ec2Client::factory(array(
            'credentials' => array(
                'key'    => getenv('AWS_KEY'),
                'secret' => getenv('AWS_SECRET'),
            ),
            'region' => 'eu-central-1',
            'version'=> 'latest'
        ));

        // Terminate instance
        $ec2Client->terminateInstances([
            'InstanceIds' => [ $awsID ],
        ]);

        $status = STATUS_STOPPING;
        $this->updateRecord($instanceID, [
            'STATUS' => $status,
        ]);

        // Wait until the instance is launched
        $ec2Client->waitUntil('InstanceTerminated', [
            'InstanceIds' => [ $awsID ],
        ]);

        $keyPairName = "$instanceID-keypair";
        $result = $ec2Client->deleteKeyPair(array(
            'KeyName' => $keyPairName
        ));
    }

    private function setAWSInstanceID($awsID) {
        $this->awsID = $awsID;
    }

    private function getAWSInstanceID() {
        return $this->awsID;
    }

}

?>