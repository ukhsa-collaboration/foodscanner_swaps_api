<?php


class CreateFoodCategorisationsTable implements iRAP\Migrations\MigrationInterface
{
    public function up(\mysqli $mysqliConn)
    {
        $this->createCategorisationsTable($mysqliConn);
        $this->createBrandbankFeedTable($mysqliConn);
        $this->createEmptyPvidsTable($mysqliConn);
    }


    public function createCategorisationsTable(\mysqli $mysqliConn)
    {
        $query =
            "CREATE TABLE `food_ml_categorisations` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `barcode` varchar(255) NOT NULL,
                `phe_ml_cat_1` varchar(255),
                `phe_ml_cat_2` varchar(255),
                PRIMARY KEY (`id`),
                UNIQUE INDEX (`barcode`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to create the food_ml_categorisations table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    private function createBrandbankFeedTable(\mysqli $mysqliConn)
    {
        $query =
            "CREATE TABLE `brandbank_feed` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `pvid` INT UNSIGNED NOT NULL,
                `barcode` varchar(255) NOT NULL,
                `pack_type_string` varchar(255),
                `pack_type_id` int unsigned,
                `storage_type_string` varchar(255),
                `storage_type_id` int unsigned,
                `preparation_instructions` TEXT,
                `updated_from_bb_at` int unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX (`pvid`),
                INDEX (`barcode`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to create the brandbank_feed table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    private function createEmptyPvidsTable(\mysqli $mysqliConn)
    {
        $query =
            "CREATE TABLE `brandbank_empty_pvids` (
                `pvid` INT UNSIGNED NOT NULL,
                UNIQUE INDEX (`pvid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $result = $mysqliConn->query($query);

        if ($result === false)
        {
            print "Failed to create the brandbank_empty_pvids table." . PHP_EOL;
            print $mysqliConn->error . PHP_EOL;
            print $createTableQuery . PHP_EOL;
            die();
        }
    }


    public function down(\mysqli $mysqliConn)
    {
        $tables = array(
            'food_ml_categorisations',
            'brandbank_feed'
        );

        foreach ($tables as $table)
        {
            $result = $mysqliConn->query("DROP TABLE `food_ml_categorisations`");

            if ($result === false)
            {
                throw new \Exception("Failed to drop the {$table} table.");
            }
        }
    }
}

