<?php

abstract class ILARIA_CoreModule
{
    // #################################################################################################################
    // ##                                        PUBLIC CONST MODULE SERVICES                                         ##
    // #################################################################################################################

    const SERVICE_DATABASE_RELATIONAL = 'eb41be327f859f9a1136462b63a5b097d6e4b241';
    const SERVICE_SECURITY_ACCESS = 'd540fdcd8810f3fbbbf81dabc6e6d07b90f31215';
    const SERVICE_SECURITY_ENCRYPTION = '334e3f72a2415471daab9434aedfc5f843957976';
    const SERVICE_HELPER = '67de1aeebed6ca5c070c6bca289ace5ebf061369';

    // #################################################################################################################
    // ##                                             STATIC REGISTRARS                                               ##
    // #################################################################################################################

    private static $modulesNames = array();
    public static function registerModule($name)
    {
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreModule::registerModule] registered module ' . $name);
        self::$modulesNames[] = $name;
    }

    private static $modules = array();
    public static function loadModules()
    {
        foreach (self::$modulesNames as $name)
        {
            $classname = 'ILARIA_Module' . $name;
            $modules[$name] = new $classname();
            $offeredServices = '';
            foreach($modules[$name]->getServices() as $service)
            {
                switch ($service)
                {
                    case self::SERVICE_DATABASE_RELATIONAL:
                        ILARIA_DatabaseManager::getInstance()->addRelationalModule($name, $modules[$name]);
                        $offeredServices .= 'SERVICE_DATABASE_RELATIONAL,';
                        break;
                    case self::SERVICE_SECURITY_ACCESS:
                        ILARIA_SecurityManager::getInstance()->addAccessModule($name, $modules[$name]);
                        $offeredServices .= 'SERVICE_SECURITY_ACCESS,';
                        break;
                    case self::SERVICE_SECURITY_ENCRYPTION:
                        ILARIA_SecurityManager::getInstance()->addEncryptionModule($name, $modules[$name]);
                        $offeredServices .= 'SERVICE_SECURITY_ENCRYPTION,';
                        break;
                    case self::SERVICE_HELPER:
                        $offeredServices .= 'SERVICE_HELPER,';
                        break;
                    default:
                        break;
                }
            }
            ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreModule::loadModules] loaded module ' . $name . ' offering services ' . $offeredServices);
        }
    }

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $services = array();

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function getServices()
    {
        return $this->services;
    }

    // Module interaction methods
    abstract public function call($key, $value);

    // #################################################################################################################
    // ##                                             PROTECTED FUNCTIONS                                             ##
    // #################################################################################################################

    protected function registerService($service)
    {
        $this->services[] = $service;
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreModule.php] class loaded');