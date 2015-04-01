<?php

class ILARIA_DatabaseManager
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
            self::$instance = new ILARIA_DatabaseManager();
        }
        return self::$instance;
    }

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $relationalModules = array();
    private $relationalInstances = array();

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function addRelationalModule($name, $module)
    {
        $this->relationalModules[$name] = $module;
    }

    public function getRelationalInstance($identifier, $moduleName)
    {
        if (isset($this->relationalModules[$moduleName]))
        {
            if (!isset($this->relationalInstances[$identifier]))
            {
                $this->relationalInstances[$identifier] = new ILARIA_DatabaseInstance($this->relationalModules[$moduleName]);
            }
            return $this->relationalInstances[$identifier];
        }
        else
        {
            throw new ILARIA_CoreError('Relation database module "' . $moduleName . '" not found',
                ILARIA_CoreError::GEN_MODULE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_DatabaseManager.php] class loaded');