<?php

/*
 * A controller for various diagnostics endopints. E.g. get information about the mysql connection etc.
 */

class DiagnosticsController extends AbstractSlimController
{
    public static function registerRoutes(Slim\App $app)
    {
        $app->get('/api/diagnostics/database', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, $args) {
            $controller = new DiagnosticsController($request, $response, $args);
            return $controller->handleGetDatabaseDiagnostics();
        });
    }


    /**
     * Handle the request to get diagnostic information about the database connection.
     * @param string $barcode
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleGetDatabaseDiagnostics() : \Psr\Http\Message\ResponseInterface
    {
        try
        {
            SiteSpecific::checkIsRemoteAdminRequest(); // throws erro
            $output = array();

            $connectionDetails = array(
                'swaps_database' => array(
                    'host' => $_ENV['SWAPS_DB_HOST'],
                    'user' => $_ENV['SWAPS_DB_USER'],
                    'password' => $_ENV['SWAPS_DB_PASSWORD'],
                    'database_name' => $_ENV['SWAPS_DB_DATABASE'],
                    'database_port' => $_ENV['SWAPS_DB_PORT']
                ),
                'etl_database' => array(
                    'host' => $_ENV['ETL_DB_HOST'],
                    'user' => $_ENV['ETL_DB_USER'],
                    'password' => $_ENV['ETL_DB_PASSWORD'],
                    'database_name' => $_ENV['ETL_DB_DATABASE'],
                    'database_port' => $_ENV['ETL_DB_PORT']
                ),
                'food_database' => array(
                    'host' => $_ENV['FOOD_DB_HOST'],
                    'user' => $_ENV['FOOD_DB_USER'],
                    'password' => $_ENV['FOOD_DB_PASSWORD'],
                    'database_name' => $_ENV['FOOD_DB_DATABASE'],
                    'database_port' => $_ENV['FOOD_DB_PORT']
                ),
            );

            $output = $connectionDetails;

            foreach ($connectionDetails as $databaseName => $dbConnectionDetails)
            {
                $statusStrings = $this->testDatabaseConnection(
                    $dbConnectionDetails['host'],
                    $dbConnectionDetails['user'],
                    $dbConnectionDetails['password'],
                    $dbConnectionDetails['database_name'],
                    $dbConnectionDetails['database_port']
                );

                $output['database_diagnostics'][$databaseName] = $statusStrings;
            }

            $response = ResponseLib::createSuccessResponse($output, $this->m_response);
        }
        catch (ExceptionAuthenticationFailed $ex)
        {
            $response = ResponseLib::createAuthenticationFailedResponse($this->m_response);
        }
        catch (Exception $e)
        {
            $response = ResponseLib::createErrorResponse(500, "Whoops, something went wrong.", $this->m_response);
        }

        return $response;
    }


    /**
     * Test that we can connect to the database and check its configuration variables if we can
     * @param string $host - the database host
     * @param string $user - the database user
     * @param string $password - the database password
     * @param string $dbName - the database name.
     * @param type $port
     * @return string - a string stating the result of trying to connect to the database.
     */
    private function testDatabaseConnection($host, $user, $password, $dbName, $port) : array
    {
        $returnStrings = [];
        // perform a timeout attempt to connect to the database.
        $link = mysqli_init();
        $timeout = 4;  /* thirty seconds for timeout */
        $optionsSet = $link->options(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);

        if ($optionsSet === false)
        {
            $returnStrings[] = "Failed to set mysql connection option on {$dbName}: {$link->error}";
        }
        else
        {
            $connected = $link->real_connect($host, $user, $password, $dbName, $port);

            if ($connected === false)
            {
                $returnStrings[] = "Failed to connect: {$link->error}";
            }
            else
            {
                $returnStrings[] = "Connected to {$dbName} successfully.";
                $maxAllowedPacket = $this->getMysqlVariable($link, 'max_allowed_packet');
                $maxConnections = $this->getMysqlVariable($link, 'max_connections');
                $threadsConnected = $this->getThreadsConnected($link);

                $returnStrings[] = "max_allowed_packet is {$maxAllowedPacket}";
                $returnStrings[] = "max_connections is {$maxConnections}";
                $returnStrings[] = "threads_connected is {$threadsConnected}";
            }
        }

        return $returnStrings;
    }


    private function getMysqlVariable(mysqli $db, string $variableName)
    {
        $result = $db->query("SELECT @@global.{$variableName}");
        /* @var $result mysqli_result */

        if ($result === false)
        {
            $result = "Failed to query the {$variableName}.";
        }
        else
        {
            $data = $result->fetch_assoc();
            $result = $data["@@global.{$variableName}"];
        }

        return $result;
    }


    private function getThreadsConnected(mysqli $db)
    {
        $result = $db->query("show status where `variable_name` = 'Threads_connected';");
        /* @var $result mysqli_result */

        if ($result === false)
        {
            $result = "Failed to query the {$variableName}.";
        }
        else
        {
            $data = $result->fetch_assoc();
            $result = $data['Value'];
        }

        return $result;
    }
}
