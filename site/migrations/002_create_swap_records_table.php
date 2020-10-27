<?php

/*
 * Migration to add a table to track users swapping to other products.
 */

class CreateSwapRecordsTable implements iRAP\Migrations\MigrationInterface
{
    public function up(\mysqli $mysqliConn)
    {
        $query =
            "CREATE TABLE `swap_records` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `from_barcode` varchar(255) NOT NULL,
                `to_barcode` varchar(255) NOT NULL,
                `device_identifier` varchar(255),
                `when` int unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX (`from_barcode`, `to_barcode`, `device_identifier`),
                INDEX (`to_barcode`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to create the swap_records table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    public function down(\mysqli $mysqliConn)
    {
        $result = $mysqliConn->query("DROP TABLE `swap_records`");

        if ($result === false)
        {
            throw new \Exception("Failed to drop the {$table} table.");
        }
    }
}

