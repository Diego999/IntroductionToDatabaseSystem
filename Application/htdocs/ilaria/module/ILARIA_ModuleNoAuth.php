<?php

class ILARIA_ModuleNoAuth extends ILARIA_CoreModule
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $logWriter = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct()
    {
        // Register services
        $this->registerService(self::SERVICE_SECURITY_ACCESS);

        // Create log writer
        $this->logWriter = new ILARIA_LogWriter(ILARIA_ConfigurationGlobal::LOG_OUTPUT_FILE_ERASE);
        ILARIA_LogManager::getInstance()->registerWriter($this->logWriter, 'mod_NoAuth');
    }

    public function call($key, $value)
    {
        switch($key)
        {
            case ILARIA_SecurityAccess::MOD_USER_TOKEN:
                $this->logWriter->write('Asked for MOD_USER_TOKEN, returning 0');
                return 0;
            case ILARIA_SecurityAccess::MOD_LOGIN_WRAP:
                $this->logWriter->write('Asked for login, returning true');
                return true;
            case ILARIA_SecurityAccess::MOD_LOGOUT_WRAP:
                $this->logWriter->write('Asked for logout, returning true');
                return true;
            default:
                $this->logWriter->write('N/A query key, throwing error');
                throw new ILARIA_CoreError('N/A key ' . $key . ' in module NoAuth',
                    ILARIA_CoreError::GEN_MODULE_UNKNOWN_KEY,
                    ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_CoreModule::registerModule('NoAuth');