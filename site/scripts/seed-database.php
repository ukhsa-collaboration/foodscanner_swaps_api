<?php

/**
 * Seed the database with fake data.
 */

require_once(__DIR__ . '/../bootstrap.php');

SiteSpecific::getSwapsDatabase();

for ($i=1; $i<=30; $i++)
{
    $rows[] = array(
        'barcode' => "0000000084260",
        'rank' => $i,
        'swap_barcode' => "0000000084260",
    );
}

$db = SiteSpecific::getSwapsDatabase();
$query = \Programster\MysqliLib\MysqliLib::generateBatchInsertQuery($rows, "swaps", $db);
$db->query($query) or die("Failed to insert seed data.");
print "Database seeded." . PHP_EOL;


