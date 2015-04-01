<?php

class ILARIA_DatabaseInstance
{
    // #################################################################################################################
    // ##                             CONST VALUES FOR KEY ACCESS INTO MODULE IMPLEMENTATIONS                         ##
    // #################################################################################################################

    const MOD_CONN_OPEN = '002799c6595df9feaf8d356360933ac7912ecac0';
    const MOD_CONN_CLOSE = 'bc5fe3ef8fa9f7b7e18f0115e8a917114318d116';
    const MOD_EXEC = 'a92766e90dba6cd67d91696cb96124b8de474a5b';
    const MOD_QUERY = '91b707214bb436dc3bbd9b21bd0789c80ed9f9da';
    const MOD_BEGIN_TRANSACTION = 'b09c5fac4fba7bb9f6ce878508c6d58d11fe4cc8';
    const MOD_COMMIT_TRANSACTION = 'a6e1dd9187f337129aa89014f299f3b1faecc546';
    const MOD_ROLLBACK_TRANSACTION = 'ff84b0b9f22da51a09bf29aa19ecf46115eec34f';
    const MOD_AUTOCOMMIT = 'a3336298c79c79534376e6a893a50f5996bae592';
    const MOD_LAST_INSERT_ID = '28c99d0428c46d18c46f1d6394705e9d870f5c91';
    const MOD_QUOTE = '00a5dd2182d9b21c5ef919955ea1fcaa9c4a302e';

    const KEY_QUERY_QUERY = 'a7eca97324bfcc5aff435ccfd1c3c84d3dfe8376';
    const KEY_QUERY_CONNECTION = 'dc5a9cecf27fe08baea88786ddcefdae58e92e23';
    const KEY_AUTOCOMMIT = 'dcb80c6ce76c34a13542e886cce157411f5754b0';
    const KEY_QUOTE_VALUE = 'e679d19c873f71765ac8f409f861b428286af34f';

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $module = NULL;
    private $connection = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function openConnection($settings)
    {
        if ($this->connection == NULL)
        {
            $this->connection = $this->module->call(self::MOD_CONN_OPEN, $settings);
            if (!$this->connection)
            {
                throw new ILARIA_CoreError('Database is unable to open',
                    ILARIA_CoreError::GEN_DB_FAILED_OPEN,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
        }
    }

    public function closeConnection()
    {
        if ($this->connection != NULL)
        {
            $this->module->call(self::MOD_CONN_CLOSE, $this->connection);
        }
        $this->connection = NULL;
    }

    public function query($query)
    {
        return $this->module->call(self::MOD_QUERY, array(
            self::KEY_QUERY_QUERY => $query,
            self::KEY_QUERY_CONNECTION => $this->connection));
    }

    public function exec($query)
    {
        return $this->module->call(self::MOD_EXEC, array(
            self::KEY_QUERY_QUERY => $query,
            self::KEY_QUERY_CONNECTION => $this->connection));
    }

    public function transactionBegin()
    {
        return $this->module->call(self::MOD_BEGIN_TRANSACTION, array(
            self::KEY_QUERY_CONNECTION => $this->connection,
        ));
    }

    public function transactionCommit()
    {
        return $this->module->call(self::MOD_COMMIT_TRANSACTION, array(
            self::KEY_QUERY_CONNECTION => $this->connection,
        ));
    }

    public function transactionRollback()
    {
        return $this->module->call(self::MOD_ROLLBACK_TRANSACTION, array(
            self::KEY_QUERY_CONNECTION => $this->connection,
        ));
    }

    public function transactionSetAutoCommit($autocommit)
    {
        return $this->module->call(self::MOD_AUTOCOMMIT, array(
            self::KEY_AUTOCOMMIT => $autocommit,
            self::KEY_QUERY_CONNECTION => $this->connection,
        ));
    }

    public function getLastInsertId()
    {
        return $this->module->call(self::MOD_LAST_INSERT_ID, array(
            self::KEY_QUERY_CONNECTION => $this->connection,
        ));
    }

    public function quote($value)
    {
        return $this->module->call(self::MOD_QUOTE, array(
            self::KEY_QUERY_CONNECTION => $this->connection,
            self::KEY_QUOTE_VALUE => $value,
        ));
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_DatabaseInstance.php] class loaded');