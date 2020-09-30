<?php

/*
 * This file is responsible for loading everything necessary for the webserver, or any scripts that run.
 * E.g. get the autoloader working and loading all the environment variables etc.
 */


require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/defines.php');

new \iRAP\Autoloader\Autoloader([
    __DIR__,
    __DIR__ . '/controllers',
    __DIR__ . '/exceptions',
    __DIR__ . '/libs',
    __DIR__ . '/models',
    __DIR__ . '/models/db',
]);

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->overload('/.env');