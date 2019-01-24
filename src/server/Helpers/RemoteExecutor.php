<?php

class RemoteExecutor
{
    public static function executeScriptSSH($script, $config)
    {
        // Setup connection string
        $host           = $config['host'];
        $username       = $config['username'];
        $keyLocation    = $config['key'];
        $connectionString = '-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null';
        $connectionString = $connectionString . " -i $keyLocation $username@$host";

        // Execute script
        if ($config['elevate']) {
            $script = "sudo -i '$script'";
        }
        $cmd = "ssh $connectionString $script 2>&1";

        $output = null;
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode) {
            throw new Exception ("\nError sshing ($exitCode):". print_r($output, true));
        }

        return $output;
    }
}

?>