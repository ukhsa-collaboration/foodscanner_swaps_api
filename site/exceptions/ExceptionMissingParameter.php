<?php

/*
 * An exception to throw when a request is mssing a required parameter.
 */

class ExceptionMissingParameter extends Exception
{
    private string $m_parameter;


    /**
     * Create an exception for when the request is missing a required parameter.
     * @param string $parameter - the name of the parameter that is missing.
     */
    public function __construct(string $parameter)
    {
        $this->m_parameter = $parameter;

        parent::__construct("Missing required parameter: " . $parameter);
    }


    public function getParameter() : string { return $this->m_parameter; }
}
