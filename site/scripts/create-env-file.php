<?php

/*
 * This script creates a .env file that dotenv can then read later (owned by www-data user).
 * This script will be executed on startup by the user that will have access to the environment
 * variables passed into docker by -e or --env-file
 * We only write out the variables we expect, rather than all variables as testing showed
 * that just dumping output of env to .env caused dotenv to fail.
 */

$env = shell_exec("env");

$epectedEnvironmentVariables = array(
    "ENVIRONMENT",

    "SWAPS_DB_HOST",
    "SWAPS_DB_DATABASE",
    "SWAPS_DB_USER",
    "SWAPS_DB_PASSWORD",
    "SWAPS_DB_PORT",

    "FOOD_DB_HOST",
    "FOOD_DB_DATABASE",
    "FOOD_DB_USER",
    "FOOD_DB_PASSWORD",
    "FOOD_DB_PORT",
    "FOOD_DB_TABLE",

    "ETL_DB_HOST",
    "ETL_DB_DATABASE",
    "ETL_DB_USER",
    "ETL_DB_PASSWORD",
    "ETL_DB_PORT",
    "ETL_DB_TABLE",

    "X_AUTH_TOKEN",
    "X_APP_AUTH_TOKEN",

    "AWS_EC2_KEY",
    "AWS_EC2_SECRET",
    "COMPUTE_AMI_ID",
    "EC2_REGION",
    "COMPUTE_INSTANCE_ID",
);

$lines = explode(PHP_EOL, $env);

foreach ($lines as $index => $line)
{
    $parts = explode("=", $line);

    if (in_array($parts[0], $epectedEnvironmentVariables) === false)
    {
        unset($lines[$index]);
    }
}

$envFile = implode(PHP_EOL, $lines);
file_put_contents("/.env", $envFile);
chown("/.env", "www-data");