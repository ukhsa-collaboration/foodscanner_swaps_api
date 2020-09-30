<?php


class SiteSpecific
{
    public static function getSwapsDatabase() : mysqli
    {
        static $db = null;

        if ($db === null)
        {
            // getenv() wont work but $_ENV will due to nginx and dotenv.
            $db = new mysqli(
                $_ENV['SWAPS_DB_HOST'],
                $_ENV['SWAPS_DB_USER'],
                $_ENV['SWAPS_DB_PASSWORD'],
                $_ENV['SWAPS_DB_DATABASE'],
                $_ENV['SWAPS_DB_PORT']
            );

            if ($db->connect_errno)
            {
                $db = null;
                print "Failed to connect to database, raising exception." . PHP_EOL;
                throw new Exception("Failed to connect to the swaps databse.");
            }
        }

        return $db;
    }


    public static function getFoodDatabase() : mysqli
    {
        static $db = null;

        if ($db === null)
        {
            $db = new mysqli(
                $_ENV['FOOD_DB_HOST'],
                $_ENV['FOOD_DB_USER'],
                $_ENV['FOOD_DB_PASSWORD'],
                $_ENV['FOOD_DB_DATABASE'],
                $_ENV['FOOD_DB_PORT']
            );

            if ($db->connect_errno)
            {
                throw new Exception("Failed to connect to the swaps databse.");
            }
        }

        return $db;
    }


    /**
     * Fetches the logger to log to.
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger() : \Psr\Log\LoggerInterface
    {
        static $logger = null;

        if ($logger === null)
        {
            $logger = new Programster\Log\MysqliLogger(SiteSpecific::getSwapsDatabase(), "logs");
        }

        return $logger;
    }
}
