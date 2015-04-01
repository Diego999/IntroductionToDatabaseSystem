<?php

class ILARIA_SecurityManager
{
    // #################################################################################################################
    // ##                                         SINGLETON IMPLEMENTATION                                            ##
    // #################################################################################################################

    private function __construct()
    {
    }

    private static $instance = NULL;
    public static function getInstance()
    {
        if (self::$instance == NULL)
        {
            self::$instance = new ILARIA_SecurityManager();
        }
        return self::$instance;
    }

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $accessModules = array();
    private $encryptionModules = array();
    private $primaryAuth = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function addAccessModule($name, $module)
    {
        $this->accessModules[$name] = $module;
    }

    public function addEncryptionModule($name, $module)
    {
        $this->encryptionModules[$name] = $module;
    }

    public function getAccessInstance($moduleName)
    {
        if (isset($this->accessModules[$moduleName]))
        {
            return new ILARIA_SecurityAccess($this->accessModules[$moduleName]);
        }
        else
        {
            throw new ILARIA_CoreError('Access module "' . $moduleName . '" not found',
                ILARIA_CoreError::GEN_MODULE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    public function getEncryptionInstance($moduleName)
    {
        if (isset($this->encryptionModules[$moduleName]))
        {
            return new ILARIA_SecurityEncryption($this->encryptionModules[$moduleName]);
        }
        else
        {
            throw new ILARIA_CoreError('Encryption module "' . $moduleName . '" not found',
                ILARIA_CoreError::GEN_MODULE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    public function getPrimaryAuth()
    {
        if ($this->primaryAuth == NULL)
        {
            $this->primaryAuth = $this->getAccessInstance(ILARIA_ConfigurationGlobal::getPrimaryAuth());
        }
        return $this->primaryAuth;
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################

    // #################################################################################################################
    // ##                                     STATIC INPUT/OUTPUT FUNCTIONS                                           ##
    // #################################################################################################################

    public static function in($data)
    {
        if (is_array($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[self::in($key)] = self::in($value);
            }
            return $result;
        }
        else
        {
            if (ctype_digit($data))
            {
                return intval($data);
            }
            else
            {
                return trim($data);
            }
        }
    }

    public static function out($data)
    {
        return htmlspecialchars($data);
    }
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_SecurityManager.php] class loaded');