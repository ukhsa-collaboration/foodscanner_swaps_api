<?php


class AlterLogsTable implements iRAP\Migrations\MigrationInterface
{
    public function up(\mysqli $mysqliConn)
    {
        $query = "ALTER TABLE `logs` MODIFY context MEDIUMTEXT;";
        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to alter the logs table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    public function down(\mysqli $mysqliConn)
    {
        $query = "ALTER TABLE `logs` MODIFY context TEXT;";
        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to alter the logs table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }
}

