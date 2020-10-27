<?php

use \Psr\Http\Message\ResponseInterface;

class ResponseLib
{
    /**
     * Create API success response.
     * @param type $data
     * @param \Swoole\Http\Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function createSuccessResponse(
        $data,
        ResponseInterface $response,
        int $httpStatusCode=200
    ) : ResponseInterface
    {
        $bodyJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $body = $response->getBody();
        $body->write($bodyJson);

        $returnResponse = $response->withStatus($httpStatusCode)
            ->withHeader("Content-Type", "application/json")
            ->withBody($body);

        return $returnResponse;
    }


    /**
     * Create API error response.
     * @param string $errorMessage
     * @param \Swoole\Http\Response $response
     * @param int $httpStatusCode
     * @param int $errorCode
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function createErrorResponse(
        int $httpStatusCode,
        string $errorMessage,
        ResponseInterface $response,
        int $errorCode = 0
    ) : ResponseInterface
    {
        $responseData = array(
            'error' => array(
                'message' => $errorMessage,
                'code' => $errorCode
            )
        );

        $bodyJson = json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $body = $response->getBody();
        $body->write($bodyJson);

        $returnResponse = $response->withStatus($httpStatusCode)
            ->withHeader("Content-Type", "application/json")
            ->withBody($body);

        return $returnResponse;
    }


    /**
     * Create the appropriate response for a missing environment variable.
     * @param ExceptionMissingEnvironmentVariable $ex
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function createMissingEnvironmentVariableResponse(
        ExceptionMissingEnvironmentVariable $ex,
        ResponseInterface $response
    ) : ResponseInterface
    {
        return ResponseLib::createErrorResponse(
            500,
            "Server is misconfigured. Missing required environment variable: " . $ex->getEnvironmentVariableName(),
            $response,
            ERROR_CODE_SERVER_MISSING_ENVIRONMENT_VARIABLE
        );
    }


    /**
     * Create the appropriate response for authentication failing.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function createAuthenticationFailedResponse(ResponseInterface $response) : ResponseInterface
    {
        return ResponseLib::createErrorResponse(
            401,
            "Authentication failed",
            $response,
            ERROR_CODE_AUTHENTICATION_FAILED
        );
    }


    /**
     * Create the appropriate response for the request is missing a required parameter.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function createMissingParameterResponse(
        ExceptionMissingParameter $ex,
        ResponseInterface $response
    ) : ResponseInterface
    {
        return ResponseLib::createErrorResponse(
            400,
            "Missing required parameter: " . $ex->getParameter(),
            $response,
            ERROR_CODE_MISSING_PARAMETER
        );
    }
}
