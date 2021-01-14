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
                throw new Exception("Failed to connect to the swaps database.");
            }
        }

        return $db;
    }


    public static function getEtlDatabase() : mysqli
    {
        static $db = null;

        if ($db === null)
        {
            $db = new mysqli(
                $_ENV['ETL_DB_HOST'],
                $_ENV['ETL_DB_USER'],
                $_ENV['ETL_DB_PASSWORD'],
                $_ENV['ETL_DB_DATABASE'],
                $_ENV['ETL_DB_PORT']
            );

            if ($db->connect_errno)
            {
                throw new Exception("Failed to connect to the ETL database.");
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
                throw new Exception("Failed to connect to the food database.");
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


    /**
     * Checks that the request came from a remote admin (provided http_basic_auth username and password).
     * @throws ExceptionAuthenticationFailed - if authentication failed.
     */
    public static function checkIsRemoteAdminRequest()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']))
        {
            throw new ExceptionAuthenticationFailed("Missing required auth user");
        }

        if (!isset($_SERVER['PHP_AUTH_PW']))
        {
            throw new ExceptionAuthenticationFailed("Missing required auth password");
        }

        $user = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        if ($user !== "admin" || $password !== 'sJ}EG67H}UvI{NRi3}')
        {
            throw new ExceptionAuthenticationFailed("Authentication failed");
        }
    }
}
