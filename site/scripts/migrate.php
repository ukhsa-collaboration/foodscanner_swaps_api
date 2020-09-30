<?php

/*
 * Script to run migrations.
 */

require_once(__DIR__ . '/../bootstrap.php');


function getDatabase() : mysqli
{
    $succeeded = false;
    $attempts = 0;

    do {
        try
        {
            $db = SiteSpecific::getSwapsDatabase();
            $succeeded = true;
            print "succeeded in getting swaps database." . PHP_EOL;
        }
        catch (Exception $ex)
        {
            $attempts++;
            print "caught exception, incrementing attempts to: {$attempts}" . PHP_EOL;
            sleep(1);
        }
    } while($succeeded === false && $attempts < 20);

    if ($succeeded === false)
    {
        throw new Exception("Failed to fetch database.");
    }

    return $db;
}



$db = getDatabase();
$migrationManager = new iRAP\Migrations\MigrationManager(__DIR__ . '/../migrations', $db);
$migrationManager->migrate();
print "Database migrated" . PHP_EOL;


