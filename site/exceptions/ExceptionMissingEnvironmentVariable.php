<?php

/*
 * An exception to throw when missing a required environment variable setting.
 */

class ExceptionMissingEnvironmentVariable extends Exception
{
    public function __construct(string $environmentVariableName)
    {
        parent::__construct("Missing required environment variable: " . $environmentVariableName);
    }
}
