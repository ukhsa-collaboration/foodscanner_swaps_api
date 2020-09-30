<?php


abstract class AbstractSlimController
{
    protected \Psr\Http\Message\ServerRequestInterface $m_request;
    protected \Psr\Http\Message\ResponseInterface $m_response;
    protected array $m_args;


    public function __construct(Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
    {
        $this->m_request = $request;
        $this->m_response = $response;
        $this->m_args = $args;
    }


    abstract public static function registerRoutes(Slim\App $app);
}