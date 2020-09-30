<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
        Psr\Http\Message\ResponseInterface $response,
        int $httpStatusCode=200
    ) : \Psr\Http\Message\ResponseInterface
    {
        $bodyJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $body = $response->getBody();
        $body->write($bodyJson);
        $returnResponse = $response->withStatus($httpStatusCode)->withHeader("Content-Type", "application/json")->withBody($body);
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
        \Psr\Http\Message\ResponseInterface $response,
        int $errorCode = 0
    ) : \Psr\Http\Message\ResponseInterface
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
        $returnResponse = $response->withStatus($httpStatusCode)->withHeader("Content-Type", "application/json")->withBody($body);
        return $returnResponse;
    }
}
