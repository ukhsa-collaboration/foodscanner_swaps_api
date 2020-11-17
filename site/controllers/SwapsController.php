<?php


use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;


class SwapsController extends AbstractSlimController
{
    public static function registerRoutes(Slim\App $app)
    {
        $app->post('/api/swaps/update-cache', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            $controller = new SwapsController($request, $response, $args);
            return $controller->handleUpdateCacheRequest();
        });

        # track that a user swapped a product.
        $app->post('/api/swaps/tracked', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            $controller = new SwapsController($request, $response, $args);
            return $controller->handleCreateSwapTrackingRecord();
        });

        # remove that a user swapped out a product
        $app->delete('/api/swaps/tracked', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            $controller = new SwapsController($request, $response, $args);
            return $controller->handleDeleteSwapTrackingRecord();
        });

        $app->get('/api/swaps/{barcode}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
            $barcode = $args['barcode'];
            $controller = new SwapsController($request, $response, $args);
            return $controller->handleGetSwapsRequest($barcode);
        });

        # ping endpoint for checking up and running.
        $app->get('/api/ping', function (Psr\Http\Message\ServerRequestInterface $request, ResponseInterface $response, $args) {
            $responseData = ["message" => "API is up and running"];
            return ResponseLib::createSuccessResponse($responseData, $response);
        });
    }


    /**
     * Handle a request that a user has swapped one product for another.
     * @return ResponseInterface
     */
    private function handleCreateSwapTrackingRecord() : \Psr\Http\Message\ResponseInterface
    {
        try
        {
            $authTokensArray = $this->m_request->getHeader("X-APP-AUTH-TOKEN");

            if (count($authTokensArray) !== 1)
            {
                throw new ExceptionAuthenticationFailed("Missing required authentication header.");
            }

            if (!isset($_ENV['X_APP_AUTH_TOKEN']) || empty($_ENV['X_APP_AUTH_TOKEN']))
            {
                throw new ExceptionMissingEnvironmentVariable("Missing required authentication header.");
            }

            if ($_ENV['X_APP_AUTH_TOKEN'] !== $authTokensArray[0])
            {
                throw new ExceptionAuthenticationFailed();
            }

            $requiredParameters = array(
                'from_barcode',
                'to_barcode',
                'device_identifier'
            );

            $allPostPutVars = $this->m_request->getParsedBody();

            foreach ($requiredParameters as $requiredParameter)
            {
                if (!isset($allPostPutVars[$requiredParameter]))
                {
                    throw new ExceptionMissingParameter($requiredParameter);
                }
            }

            $fromBarcode = $allPostPutVars['from_barcode'];
            $toBarcode = $allPostPutVars['to_barcode'];
            $deviceIdentifier = $allPostPutVars['device_identifier'];

            /* @var $swapRecordTable SwapRecordTable */
            $swapRecordTable = SwapRecordTable::getInstance();

            try
            {
                $swapRecord = $swapRecordTable->loadFromDeviceIdentifierAndBarcodes(
                    $deviceIdentifier,
                    $fromBarcode,
                    $toBarcode
                );

                throw new ExceptionSwapRecordAlreadyExists();
            }
            catch (ExceptionSwapRecordNotFound $ex)
            {
                // record doesn't exist (good), create it.
                SwapRecordTable::getInstance()->createFromDeviceIdentifierAndBarcodes(
                    $deviceIdentifier,
                    $fromBarcode,
                    $toBarcode
                );
            }

            $response = ResponseLib::createSuccessResponse(['message' => "Swap recorded."], $this->m_response);
        }
        catch (ExceptionSwapRecordAlreadyExists $swapRecordAlreadyExistsException)
        {
            $response = ResponseLib::createErrorResponse(409, "Conflict - record already exists.", $this->m_response);
        }
        catch (ExceptionSwapRecordNotFound $swapRecordNotFoundException)
        {
            $response = ResponseLib::createErrorResponse(404, "Could not delete, record not found.", $this->m_response);
        }
        catch (ExceptionAuthenticationFailed $authFailedException)
        {
            $response = ResponseLib::createAuthenticationFailedResponse($this->m_response);
        }
        catch (ExceptionMissingEnvironmentVariable $missingSettingException)
        {
            $response = ResponseLib::createMissingEnvironmentVariableResponse($missingSettingException, $this->m_response);
        }
        catch (ExceptionMissingParameter $missingRequredParameterException)
        {
            $response = ResponseLib::createMissingParameterResponse($missingRequredParameterException, $this->m_response);
        }
        catch (Exception $authFailedException)
        {
            $response = ResponseLib::createErrorResponse(500, "Whoops, something went wrong.", $this->m_response);
        }

        return $response;
    }


    private function handleDeleteSwapTrackingRecord() : \Psr\Http\Message\ResponseInterface
    {
        try
        {
            $authTokensArray = $this->m_request->getHeader("X-APP-AUTH-TOKEN");

            if (count($authTokensArray) !== 1)
            {
                throw new ExceptionAuthenticationFailed("Missing required authentication header.");
            }

            if (!isset($_ENV['X_APP_AUTH_TOKEN']) || empty($_ENV['X_APP_AUTH_TOKEN']))
            {
                throw new ExceptionMissingEnvironmentVariable("Missing required authentication header.");
            }

            if ($_ENV['X_APP_AUTH_TOKEN'] !== $authTokensArray[0])
            {
                throw new ExceptionAuthenticationFailed();
            }

            $requiredParameters = array(
                'from_barcode',
                'to_barcode',
                'device_identifier'
            );

            $allPostPutVars = $this->m_request->getParsedBody();

            foreach ($requiredParameters as $requiredParameter)
            {
                if (!isset($allPostPutVars[$requiredParameter]))
                {
                    throw new ExceptionMissingParameter($requiredParameter);
                }
            }

            $fromBarcode = $allPostPutVars['from_barcode'];
            $toBarcode = $allPostPutVars['to_barcode'];
            $deviceIdentifier = $allPostPutVars['device_identifier'];

            /* @var $swapRecordTable SwapRecordTable */
            $swapRecordTable = SwapRecordTable::getInstance();

            $swapRecord = $swapRecordTable->loadFromDeviceIdentifierAndBarcodes(
                $deviceIdentifier,
                $fromBarcode,
                $toBarcode
            );

            $swapRecord->delete();
            $response = ResponseLib::createSuccessResponse(['message' => "Swap record deleted."], $this->m_response);
        }
        catch (ExceptionSwapRecordNotFound $swapRecordNotFoundException)
        {
            $response = ResponseLib::createErrorResponse(404, "Could not delete, record not found.", $this->m_response);
        }
        catch (ExceptionAuthenticationFailed $authFailedException)
        {
            $response = ResponseLib::createAuthenticationFailedResponse($this->m_response);
        }
        catch (ExceptionMissingEnvironmentVariable $missingSettingException)
        {
            $response = ResponseLib::createMissingEnvironmentVariableResponse($missingSettingException, $this->m_response);
        }
        catch (ExceptionMissingParameter $missingRequredParameterException)
        {
            $response = ResponseLib::createMissingParameterResponse($missingRequredParameterException, $this->m_response);
        }
        catch (Exception $authFailedException)
        {
            $response = ResponseLib::createErrorResponse(500, "Whoops, something went wrong.", $this->m_response);
        }

        return $response;
    }


    /**
     * Handle the request to get swaps for a food product barcode.
     * @param string $barcode
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleGetSwapsRequest(string $barcode) : \Psr\Http\Message\ResponseInterface
    {
        try
        {
            /* @var $foodTable FoodTable */
            $foodTable = FoodTable::getInstance();
            /* @var $swapTable SwapTable */
            $swapTable = SwapTable::getInstance();

            // using getBarcode() on product as swap barcodes should line up with food table barcodes, and may need to
            // add/strip 0s etc, which gets figured out in $foodTable->findByBarcode
            $product = $foodTable->findByBarcode($barcode);
            $swaps = $swapTable->loadForBarcode($product->getBarcode());

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

                $swapResponseObjects = array_slice($swapResponseObjects, 0, 33);
                $response = ResponseLib::createSuccessResponse($swapResponseObjects, $this->m_response);
            }
        }
        catch (ExceptionProductNotFound $ex)
        {
            $response = ResponseLib::createErrorResponse(404, "Barcode not found.", $this->m_response, -100);
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

            $requestDescribeInstances = new \Programster\AwsWrapper\Requests\RequestDescribeInstances(
                $region,
                [$_ENV['COMPUTE_INSTANCE_ID']]
            );

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
            $response = ResponseLib::createAuthenticationFailedResponse($this->m_response);
        }
        catch (ExceptionMissingEnvironmentVariable $missingSettingException)
        {
            $response = ResponseLib::createMissingEnvironmentVariableResponse($missingSettingException, $this->m_response);
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
