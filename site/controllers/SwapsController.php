<?php


class SwapsController extends AbstractSlimController
{
    public static function registerRoutes(Slim\App $app)
    {
        $app->post('/api/swaps/update-cache', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, $args) {
            $controller = new SwapsController($request, $response, $args);
            return $controller->handleUpdateCacheRequest();
        });

        $app->get('/api/swaps/{barcode}', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, $args) {
            $barcode = $args['barcode'];
            $controller = new SwapsController($request, $response, $args);
            return $controller->handleSwapRequest($barcode);
        });

        # ping endpoint for checking up and running.
        $app->get('/api/ping', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, $args) {
            $responseData = ["message" => "API is up and running"];
            return ResponseLib::createSuccessResponse($responseData, $response);
        });
    }


    /**
     * Handle the request to get swaps for a food product barcode.
     * @param string $barcode
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleSwapRequest(string $barcode) : \Psr\Http\Message\ResponseInterface
    {
        try
        {
            $swaps = SwapTable::getInstance()->loadForBarcode($barcode);

            if (count($swaps) === 0)
            {
                // barcode not found, throw an error
                $response = ResponseLib::createErrorResponse(404, "Barcode not found.", $this->m_response, -100);
            }
            else
            {
                $swapResponseObjects = SwapResponseObject::createForSwaps(...$swaps);
                $compareFunc = function(SwapResponseObject $a, SwapResponseObject $b) { return $a->getRank() <=> $b->getRank(); };

                if (RANDOMIZATION_ENABLED)
                {
                    $compareFunc = function(SwapResponseObject $a, SwapResponseObject $b) { return $a->getRank() <=> $b->getRank(); };
                    $top3 = array();

                    foreach ($swapResponseObjects as $index => $swapResponseObject)
                    {
                        /* @var $swapResponseObject SwapResponseObject */
                        if ($swapResponseObject->getRank() < 4)
                        {
                            $top3[] = $swapResponseObject;
                            unset($swapResponseObjects[$index]);
                        }
                    }

                    shuffle($swapResponseObjects);
                    $swapResponseObjects = [...$top3, ...$swapResponseObjects];
                }
                else
                {
                    uasort($swapResponseObjects, $compareFunc);
                }

                $swapResponseObjects = array_slice($swapResponseObjects, 0, 30);
                $response = ResponseLib::createSuccessResponse($swapResponseObjects, $this->m_response);
            }
        }
        catch (Exception $ex)
        {
            $response = ResponseLib::createErrorResponse(500, "Whoops, something went wrong.", $this->m_response);
        }

        return $response;
    }


    private function handleUpdateCacheRequest()
    {
        try
        {
            $authTokenHeaderValues = $this->m_request->getHeader('X-AUTH-TOKEN');

            if (count($authTokenHeaderValues) === 0 || $authTokenHeaderValues[0] !== $_ENV['X_AUTH_TOKEN'])
            {
                throw new ExceptionAuthenticationFailed();
            }

            $requiredEnvironmentVariables = [
                'AWS_EC2_KEY',
                'AWS_EC2_SECRET',
                'EC2_REGION',
                'COMPUTE_INSTANCE_ID'
            ];

            foreach ($requiredEnvironmentVariables as $requiredVariableName)
            {
                if (!isset($_ENV[$requiredVariableName]) || empty($_ENV[$requiredVariableName]))
                {
                    throw new ExceptionMissingEnvironmentVariable($requiredVariableName);
                }
            }

            $region = Programster\AwsWrapper\Enums\AwsRegion::create_from_string($_ENV['EC2_REGION']);

            // starting exisitng instance instead of sending request to spawn a
            $ec2Client = new Programster\AwsWrapper\Ec2\Ec2Client(
                $_ENV['AWS_EC2_KEY'],
                $_ENV['AWS_EC2_SECRET'],
                $region
            );

            $requestDescribeInstances = new \Programster\AwsWrapper\Requests\RequestDescribeInstances($region, [$_ENV['COMPUTE_INSTANCE_ID']]);
            $ec2Client->describeInstances($requestDescribeInstances);
            $instances = $requestDescribeInstances->get_instances();

            if (count($instances) !== 1)
            {
                throw new ExceptionMissingComputeInstance("Cache compute server has disappeared.");
            }

            /* @var $computeInstance \Programster\AwsWrapper\Ec2\Ec2Instance */
            $computeInstance = $instances[0];

            if ($computeInstance->getStateString() !== "stopped")
            {
                throw new ExceptionComputeInstanceNotReady("Compute instance is in the " . $computeInstance->getStateString() . " state and is not ready to be started");
            }

            $startInstanceRequest = new \Programster\AwsWrapper\Requests\RequestStartInstances($computeInstance->getInstanceId());
            $ec2Client->startInstances($startInstanceRequest);

            $response = ResponseLib::createSuccessResponse(
                "Notification recieved. Triggering update.",
                $this->m_response
            );
        }
        catch (ExceptionAuthenticationFailed $ex)
        {
            $response = ResponseLib::createErrorResponse(
                401,
                "Authentication failed",
                $this->m_response,
                ERROR_CODE_AUTHENTICATION_FAILED
            );
        }
        catch (ExceptionMissingEnvironmentVariable $missingSettingException)
        {
            $response = ResponseLib::createErrorResponse(
                500,
                "Server is misconfigured. " . $missingSettingException->getMessage(),
                $this->m_response,
                ERROR_CODE_SERVER_MISSING_ENVIRONMENT_VARIABLE
            );
        }
        catch (ExceptionMissingComputeInstance $e)
        {
            $response = ResponseLib::createErrorResponse(
                500,
                "Cannot find cache computing instance: " . $_ENV['COMPUTE_INSTANCE_ID'] . ". Did an admin terminate it?",
                $this->m_response,
                ERROR_CODE_MISSING_COMPUTE_INSTANCE
            );
        }
        catch (ExceptionComputeInstanceNotReady $computeStateException)
        {
            $response = ResponseLib::createErrorResponse(
                503,
                $computeStateException->getMessage(),
                $this->m_response,
                ERROR_CODE_COMPUTE_INSTANCE_NOT_READY
            );
        }
        catch (Exception $unexpectedException)
        {
            SiteSpecific::getLogger()->error("Unexpected Exception", ["message" => $unexpectedException->getMessage()]);
            $response = ResponseLib::createErrorResponse(500, "Whoops, something wen't wrong.", $this->m_response);
        }

        return $response;
    }
}
