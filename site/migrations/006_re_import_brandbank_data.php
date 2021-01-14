<?php


class ReImportBrandbankData implements iRAP\Migrations\MigrationInterface
{
    public function up(\mysqli $mysqliConn)
    {
        $handle = fopen(__DIR__ . '/assets/006_brandbank_queries.sql', 'r');
        $truncateQuery = "TRUNCATE `brandbank_feed`";
        $result = $mysqliConn->query($truncateQuery);

        if ($result === false)
        {
            throw new Exception("Failed to truncate the brandbank_feed table.");
        }

        while (($line = fgets($handle)) !== false)
        {
            if (true)
            {
                $result = $mysqliConn->query($line);

                if ($result === false)
                {
                    print "query failed" . PHP_EOL . $mysqliConn->error . PHP_EOL . $query . PHP_EOL;
                    die();
                }
            }
        }
    }


    public function down(\mysqli $mysqliConn)
    {
        $truncateQuery = "TRUNCATE `brandbank_feed`";
        $result = $mysqliConn->query($truncateQuery);

        if ($result === false)
        {
            throw new Exception("Failed to truncate the brandbank_feed table.");
        }
    }
}

