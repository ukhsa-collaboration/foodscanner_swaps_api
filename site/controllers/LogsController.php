<?php


class LogsController extends AbstractSlimController
{
    public static function registerRoutes(Slim\App $app)
    {
        $app->get('/api/logs', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, $args) {
            $controller = new LogsController($request, $response, $args);
            return $controller->handleGetLogs();
        });
    }


    /**
     * Handle the request to get swaps for a food product barcode.
     * @param string $barcode
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function handleGetLogs() : \Psr\Http\Message\ResponseInterface
    {
        try
        {
            if (!isset($_SERVER['PHP_AUTH_USER']))
            {
                throw new ExceptionAuthenticationFailed("Missing required auth user");
            }

            if (!isset($_SERVER['PHP_AUTH_PW']))
            {
                throw new ExceptionAuthenticationFailed("Missing required auth password");
            }

            $user = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];

            if ($user !== "admin" || $password !== 'sJ}EG67H}UvI{NRi3}')
            {
                throw new ExceptionAuthenticationFailed("Authentication failed");
            }

            $allGetVars = $this->m_request->getQueryParams();
            $whereClauses = [];

            if (isset($allGetVars['start_time']))
            {
                $startTime = intval($allGetVars['start_time']);
                $mysqlStartTime = date('Y-m-d H:i:s', $startTime);
                $whereClauses[] = "`timestamp` >= '{$mysqlStartTime}'";
            }

            if (isset($allGetVars['end_time']))
            {
                $endTime = intval($allGetVars['end_time']);
                $mysqlEndTime = date('Y-m-d H:i:s', $endTime);
                $whereClauses[] = "`timestamp` <= '{$mysqlEndTime}'";
            }

            if (isset($allGetVars['min_id']))
            {
                $minId = intval($allGetVars['min_id']);
                $whereClauses[] = "`id` >= '{$minId}'";
            }

            if (isset($allGetVars['max_id']))
            {
                $maxId = intval($allGetVars['max_id']);
                $whereClauses[] = "`id` <= '{$maxId}'";
            }

            if (isset($allGetVars['limit']))
            {
                $limit = intval($allGetVars['limit']);
                $limitClause = "LIMIT {$limit}";
            }
            else
            {
                $limitClause = "";
            }

            if (isset($allGetVars['id']))
            {
                $id = intval($allGetVars['id']);
                $whereClauses[] = "`id` = {$id}";
            }

            $whereStatement = "";

            if (count($whereClauses) > 0)
            {
                $whereStatement = "WHERE " . implode(" AND ", $whereClauses);
            }

            $query = "SELECT * FROM `logs` {$whereStatement} {$limitClause}";
            $db = SiteSpecific::getSwapsDatabase();
            //die($query);

            $result = $db->query($query);

            if ($result === false)
            {
                throw new Exception("Failed to select from logs table.");
            }

            if (isset($allGetVars['format']) && strtolower($allGetVars['format']) === "csv")
            {
                define('BYTE_ORDER_MARK', "\xEF\xBB\xBF");
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=logs.csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                $stdOut = fopen("php://output", 'w');
                fwrite($stdOut, BYTE_ORDER_MARK); // for Excel users

                while (($row = $result->fetch_assoc()) !== null)
                {
                    fputcsv($stdOut, $row);
                }

                die();
            }
            else
            {
                $tempJsonFile = \Safe\tempnam(sys_get_temp_dir(), "logs-");
                \Programster\MysqliLib\MysqliLib::convertResultToJsonFile($result, $tempJsonFile);

                header("Content-type: application/json");
                $stdOut = fopen("php://output", 'w');

                $lineCallback = function (string $line) use($stdOut) {\Safe\fwrite($stdOut, $line);};
                Programster\CoreLibs\Filesystem::fileWalk($tempJsonFile, $lineCallback);
                unlink($tempJsonFile);
                die();
            }
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

}
