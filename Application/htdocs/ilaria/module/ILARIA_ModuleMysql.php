<?php

class ILARIA_ModuleMysql extends ILARIA_CoreModule
{
    // #################################################################################################################
    // ##                                              STATIC PARAMETERS                                              ##
    // #################################################################################################################

    public static function getConnectionSettings()
    {
        return array(
            self::CONN_SERVER_HOST => '127.0.0.1',
            self::CONN_SERVER_PORT => '3306',
            self::CONN_USER_NAME => 'dbproject',
            self::CONN_USER_PASSWORD => 'dbproject',
            self::CONN_DB_NAME => 'imdb',
        );
    }

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    const CONN_SERVER_HOST = 'd99ce39e867ef6f42af4bb483b0c8248ff6cf676';
    const CONN_SERVER_PORT = '943d86bc8175cf12d3957a49bd138ad25b266ff8';
    const CONN_USER_NAME = 'e06a9201232d472f21e5719af163ac288fd758f9';
    const CONN_USER_PASSWORD = '99c529224a6b1540dfba53283b7630dd5495c8db';
    const CONN_DB_NAME = '33e00241db619530d019f66b7aa5443cc500cabe';

    private $logWriter = NULL;
    private $errorWriter = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct()
    {
        // Register services
        $this->registerService(self::SERVICE_DATABASE_RELATIONAL);

        // Create log writer
        //$this->logWriter = NULL;
        $this->logWriter = new ILARIA_LogWriter(ILARIA_ConfigurationGlobal::LOG_OUTPUT_FILE_APPEND);
        ILARIA_LogManager::getInstance()->registerWriter($this->logWriter, 'mod_Mysql');

        // Create error writer
        $this->errorWriter = new ILARIA_LogWriter(ILARIA_ConfigurationGlobal::LOG_OUTPUT_FILE_ERASE);
        ILARIA_LogManager::getInstance()->registerWriter($this->errorWriter, "mod_Mysql_ERR");
    }

    public function call($key, $value)
    {
        switch($key)
        {
            case ILARIA_DatabaseInstance::MOD_CONN_OPEN:
                return $this->openConnection($value);

            case ILARIA_DatabaseInstance::MOD_CONN_CLOSE:
                return true;

            case ILARIA_DatabaseInstance::MOD_QUERY:
                $this->query($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION], $value[ILARIA_DatabaseInstance::KEY_QUERY_QUERY]);
                return true;

            case ILARIA_DatabaseInstance::MOD_EXEC:
                $this->exec($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION], $value[ILARIA_DatabaseInstance::KEY_QUERY_QUERY]);
                return true;

            case ILARIA_DatabaseInstance::MOD_BEGIN_TRANSACTION:
                return $this->transactionBegin($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION]);

            case ILARIA_DatabaseInstance::MOD_COMMIT_TRANSACTION:
                return $this->transactionCommit($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION]);

            case ILARIA_DatabaseInstance::MOD_ROLLBACK_TRANSACTION:
                return $this->transactionRollback($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION]);

            case ILARIA_DatabaseInstance::MOD_AUTOCOMMIT:
                return $this->autocommit($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION], $value[ILARIA_DatabaseInstance::KEY_AUTOCOMMIT]);

            case ILARIA_DatabaseInstance::MOD_LAST_INSERT_ID:
                return $this->getLastInsertId($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION]);

            case ILARIA_DatabaseInstance::MOD_QUOTE:
                return $this->quote($value[ILARIA_DatabaseInstance::KEY_QUERY_CONNECTION], $value[ILARIA_DatabaseInstance::KEY_QUOTE_VALUE]);

            default:
                $this->logWriter->write('N/A query key, throwing error');
                throw new ILARIA_CoreError('N/A key ' . $key . ' in module Mysql',
                    ILARIA_CoreError::GEN_MODULE_UNKNOWN_KEY,
                    ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################

    private function writeLog($text)
    {
        if ($this->logWriter != NULL)
        {
            $this->logWriter->write($text);
        }
    }

    private function openConnection($settings)
    {
        // Debug
        $this->writeLog("<OPENING CONNECTION>");

        // Setup DNS and options for MySQL connection
        $dns = 'mysql:host=' . $settings[self::CONN_SERVER_HOST] . ';port=' . $settings[self::CONN_SERVER_PORT] . ';dbname=' . $settings[self::CONN_DB_NAME];
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

        // Try connecting
        try
        {
            return new PDO($dns, $settings[self::CONN_USER_NAME], $settings[self::CONN_USER_PASSWORD], $options);
        }

            // Deal with database error
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            return false;
        }
    }

    private function query($connection, $query)
    {
        // Debug message
        $this->writeLog('Querying "' . $query->getSql() . '"');

        // Execute query
        try
        {
            $result = $connection->query($query->getSql());

            if ($result)
            {
                // Set status code
                $query->setStatus(0);

                // Count number of rows
                $query->setCount($result->rowCount());

                // Gather column names
                $columnNames = array();
                for ($i=0; $i<$result->columnCount(); $i++)
                {
                    $columnNames[] = $result->getColumnMeta($i)['name'];
                }
                $query->setFields($columnNames);

                // Gather data
                $query->setData($result->fetchAll(PDO::FETCH_ASSOC));
            }
            else
            {
                $this->writeLog('Error while executing query "' . $query->getSql() . '"');
                $this->errorWriter->write('Error while executing query "' . $query->getSql() . '"');
                $query->setStatus(-1);
            }

        }

            // Deal with errors
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            $this->errorWriter->write($e->getMessage());
            $query->setStatus($e->getCode());
        }
    }

    private function exec($connection, $query)
    {
        // Debug message
        $this->writeLog('Executing "' . $query->getSql() . '"');

        // Execute query
        try
        {
            $result = $connection->exec($query->getSql());

            // Set status code
            $query->setStatus(0);

            // Number of rows updated
            $query->setCount($result);
        }

            // Deal with errors
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            $this->errorWriter->write($e->getMessage());
            $query->setStatus($e->getCode());
        }
    }

    private function transactionBegin($connection)
    {
        try
        {
            $this->writeLog("Beginning a transaction");
            $connection->beginTransaction();
            return true;
        }
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            $this->errorWriter->write($e->getMessage());
            return false;
        }
    }

    private function transactionCommit($connection)
    {
        try
        {
            $this->writeLog("Commit the current transaction");
            $connection->commit();
            return true;
        }
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            $this->errorWriter->write($e->getMessage());
            return false;
        }
    }

    private function transactionRollback($connection)
    {
        try
        {
            $this->writeLog("Rollback the current transaction");
            $connection->rollback();
            return true;
        }
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            $this->errorWriter->write($e->getMessage());
            return false;
        }
    }

    private function autocommit($connection, $autocommit)
    {
        try
        {
            $this->writeLog("Set AUTO_COMMIT to " . ($autocommit ? 1 : 0));
            $connection->setAttribute(PDO::ATTR_AUTOCOMMIT, ($autocommit ? 1 : 0));
            return true;
        }
        catch (PDOException $e)
        {
            $this->writeLog($e->getMessage());
            $this->errorWriter->write($e->getMessage());
            return false;
        }
    }

    private function getLastInsertId($connection)
    {
        $lastId = $connection->lastInsertId();
        $this->writeLog("Retrieved ID : " . $lastId);
        return $lastId;
    }

    private function quote($connection, $value)
    {
        return $connection->quote($value);
    }
}

ILARIA_CoreModule::registerModule('Mysql');