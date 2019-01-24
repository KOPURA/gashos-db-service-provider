<?php

include "Helpers/DB/DBConnection.php";
include "Helpers/RemoteExecutor.php";
include "Traits/PasswordValidator.php";
include "AWS/aws-autoloader.php";

use Aws\Ec2\Ec2Client;

const STATUS_INIT       = "Initializing";
const STATUS_RUNNING    = "Running";
const STATUS_ERROR      = "Error";

class CreateInstance extends AbstractRestHandler {
    use PasswordValidator;

    protected function requiresAuthentication(): bool {
        return true;
    }

    public function requiresBuffering(): bool {
        return true;
    }

    protected function process() {
        #$this->postProcess();
        $this->setResponseCode(201);
    }

    protected function getParamKeys() {
        return ['DBType', 'DBUser', 'DBPassword', 'DBName'];
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

    protected function checkDBPassword($password) {
        return $this->validatePassword($password);
    }

    protected function checkDBUser($username) {
        if (strlen($username) < 1) {
            $this->addError("DB Admin username cannot be empty");
            return 0;
        }
        return 1;
    }

    protected function checkDBName($dbName) {
        if (strlen($dbName) < 1) {
            $this->addError("Database name cannot be empty");
            return 0;
        }

        if (!preg_match("/^[0-9a-zA-Z$_]+$/", $dbName)) {
            $this->addError("Database name should consist of upper- or lower-case letters, numbers, $ and _");
            return 0;
        }

        return 1;
    }

    public function postProcess() {
        $dbType = $this->getParam("DBType");
        $instanceID = md5(time() + SessionManager::getInstance()->getUserID());

        $this->setStatusInit($instanceID);

        $dns = $this->provisionVM($instanceID);
        if($this->runDBInstance($instanceID, $dns)){
            $status = STATUS_RUNNING;
            $this->updateRecord($instanceID, array(
                'STATUS'            => $status
            ));
        }
    }

    private function updateRecord($instanceID, $valuesMapping, $withErrorHandling = true) {
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

    private function setStatusError($instanceID, $error) {
        $dbConn = DBConnection::getInstance();
        $userID = SessionManager::getInstance()->getUserID();
        $status = STATUS_ERROR;

        $this->updateRecord($instanceID, array(
            'STATUS' => $status,
            'ERROR'  => $error
        ), false); # Avoid infinite recursion
    }

    private function setStatusInit($instanceID) {
        $dbConn = DBConnection::getInstance();
        $userID = SessionManager::getInstance()->getUserID();
        $status = STATUS_INIT;

        $stmt = $dbConn->prepare("INSERT INTO Instances(
            USER_ID,
            STATUS,
            INSTANCE_ID,
            DB_NAME,
            DB_USER
        ) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userID, $status, $instanceID, $this->getParam("DBName"), $this->getParam("DBUser"));
        $stmt->execute();
    }

    private function provisionVM($instanceID) {
        $ec2Client = Ec2Client::factory(array(
            'credentials' => array(
                'key'    => getenv('AWS_KEY'),
                'secret' => getenv('AWS_SECRET'),
            ),
            'region' => 'eu-central-1',
            'version'=> 'latest'
        ));

        $keyPairName = "$instanceID-keypair";
        $result = $ec2Client->createKeyPair(array(
            'KeyName' => $keyPairName
        ));

        $keyLocation = getenv("SSH_LOCATION")."/{$instanceID}.pem";
        file_put_contents($keyLocation, $result['KeyMaterial']);
        chmod($keyLocation, 0600);

        // Launch an instance with the key pair and security group
        $result = $ec2Client->runInstances(array(
            'ImageId'        => 'ami-0a1886cf45f944eb1',    # SUSE Linux
            'MinCount'       => 1,
            'MaxCount'       => 1,
            'InstanceType'   => 't2.micro',                 # Free tier VM
            'KeyName'        => $keyPairName,
            'SecurityGroups' => ['mysql-security-group', 'launch-wizard-1'], # Predefine group, which opens port 3306, and one for ssh
        ));

        $awsInstanceID = $result->search('Instances[0].InstanceId');
        // Wait until the instance is launched
        $ec2Client->waitUntil('InstanceRunning', [
            'InstanceIds' => [ $awsInstanceID ],
        ]);

        // Describe the now-running instance to get the public URL
        $result = $ec2Client->DescribeInstances([
            'InstanceIds' => [ $awsInstanceID ],
        ]);

        $dns = $result->search('Reservations[0].Instances[0].PublicDnsName');
        $this->updateRecord($instanceID, array(
            'AWS_INSTANCE_ID'   => $awsInstanceID,
            'PUBLIC_DNS'        => $dns
        ));

        return $dns;
    }

    private function runDBInstance($instanceID, $hostname) {
        $dbName = $this->getParam("DBName");
        $dbUser = $this->getParam("DBUser");
        $dbPwd  = $this->getParam("DBPassword");
        sleep(60); # Wait until ssh is running;

        try {
            $this->executeCommand('killall -9 zypper', $instanceID, $hostname);
        } catch(Throwable $e) { }# Ignore the error

        $commands = [
            "zypper update -y docker",
            "service docker start",
            "docker run -d --rm --name $dbName-container -p 3306:3306 -e MYSQL_ROOT_PASSWORD='$dbPwd' -e MYSQL_USER='$dbUser' -e MYSQL_PASSWORD='$dbPwd' -e MYSQL_DATABASE='$dbName' mysql:5.6",

        ];
        try {
            foreach ($commands as $cmd) {
                $this->executeCommand($cmd, $instanceID, $hostname);
            }
            return true;
        } catch(Throwable $e) {
            $this->setStatusError($instanceID, $e->getMessage());
            return false;
        }
    }

    private function executeCommand($command, $instanceID, $hostname) {
        RemoteExecutor::executeScriptSSH($command, [
            'host' => $hostname,
            'username' => 'ec2-user',
            'key' => getenv("SSH_LOCATION")."/{$instanceID}.pem",
            'elevate' => true,
        ]);
    }

}

?>