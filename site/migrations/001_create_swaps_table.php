<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CreateSwapsTable implements iRAP\Migrations\MigrationInterface
{
    public function up(\mysqli $mysqliConn)
    {
        $this->createSwapsTables($mysqliConn);
        $this->createLogsTable($mysqliConn);
    }


    private function createLogsTable(\mysqli $mysqliConn)
    {
        $query =
            "CREATE TABLE `logs` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `message` varchar(255) NOT NULL,
                `priority` varchar(255) NOT NULL,
                `context` TEXT,
                `timestamp` TIMESTAMP default CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX (`priority`),
                INDEX (`timestamp`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to create the logs table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    private function createSwapsTables(\mysqli $mysqliConn)
    {
        $createTableQuery =
            "CREATE TABLE `swaps` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `barcode` varchar(255) NOT NULL,
                `swap_barcode` varchar(255) NOT NULL,
                `rank` int unsigned NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`barcode`),
                UNIQUE (`barcode`, `rank`),
                UNIQUE (`barcode`, `swap_barcode`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($createTableQuery);

        if ($result === false)
        {
            print "Failed to create the swaps table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }

        $createSwapsBufferTableQuery =
            "CREATE TABLE `swaps_buffer` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `barcode` varchar(255) NOT NULL,
                `swap_barcode` varchar(255) NOT NULL,
                `rank` int unsigned NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`barcode`),
                UNIQUE (`barcode`, `rank`),
                UNIQUE (`barcode`, `swap_barcode`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($createSwapsBufferTableQuery);

        if ($result === false)
        {
            print "Failed to create the swaps_buffer table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    public function down(\mysqli $mysqliConn)
    {
        $tables = array('swaps', 'swaps_buffer', 'logs');

        foreach ($tables as $table)
        {
            $result = $mysqliConn->query("DROP TABLE `{$table}`");

            if ($result === false)
            {
                throw new \Exception("Failed to drop the {$table} table.");
            }
        }
    }
}

