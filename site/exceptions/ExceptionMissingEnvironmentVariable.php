<?php

/*
 * An exception to throw when missing a required environment variable setting.
 */

class ExceptionMissingEnvironmentVariable extends Exception
{
    private string $m_environmentVariableName;


    /**
     * Create an exception for the server being misconfigured because it is missing a required environment variable.
     * @param string $environmentVariableName - the name of the environment variable that is missing.
     */
    public function __construct(string $environmentVariableName)
    {
        $this->m_environmentVariableName = $environmentVariableName;

        parent::__construct("Missing required environment variable: " . $environmentVariableName);
    }


    public function getEnvironmentVariableName() : string { return $this->m_environmentVariableName; }
}
