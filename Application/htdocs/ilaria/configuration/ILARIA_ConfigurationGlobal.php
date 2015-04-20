<?php

define('DS', '/');

class ILARIA_ConfigurationGlobal
{
    // #################################################################################################################
    // ##                        DECLARE CONST VALUES => POSSIBLE VALUES FOR LOCAL PARAMETERS                         ##
    // #################################################################################################################

    // error management mode
    const ERROR_MODE_VERBOSE = 'bc19a287b3d99e671f0c2bf05bc99a713afdd3e5';
    const ERROR_MODE_MINIMAL = '3c2791acf5ae5f9dc5b8251cc00b628c7c895009';
    const ERROR_MODE_HIDDEN = '9b3ddbe01ee68fc7fa5e46b12e68b99f33f349bb';

    // logging modes
    const LOG_OUTPUT_NONE = '84d87ee05dbf42a528d2169ef27c88d2a25cb3d9';
    const LOG_OUTPUT_FILE_APPEND = '36b9e443ba8ec09e5a50011e9d1fee56c712e751';
    const LOG_OUTPUT_FILE_ERASE = '4a612f05ca63d360b3d67d4945cc3363609214c4';

    // #################################################################################################################
    // ##                                  PARAMETERS DEPENDING ON LOCAL CONFIGURATION                                ##
    // #################################################################################################################

    // site root folder
    private static $siteRoot = array(
        ILARIA_ConfigurationLocal::SERVER_MODE_DEVELOPMENT => '',
        ILARIA_ConfigurationLocal::SERVER_MODE_TEST => '',
        ILARIA_ConfigurationLocal::SERVER_MODE_PRODUCTION => '',
    );

    // error management mode
    private static $errorMode = array(
        ILARIA_ConfigurationLocal::SERVER_MODE_DEVELOPMENT => self::ERROR_MODE_VERBOSE,
        ILARIA_ConfigurationLocal::SERVER_MODE_TEST => self::ERROR_MODE_MINIMAL,
        ILARIA_ConfigurationLocal::SERVER_MODE_PRODUCTION => self::ERROR_MODE_HIDDEN,
    );

    // logging mode for errors
    private static $logErrorMode = array(
        ILARIA_ConfigurationLocal::SERVER_MODE_DEVELOPMENT => self::LOG_OUTPUT_FILE_ERASE,
        ILARIA_ConfigurationLocal::SERVER_MODE_TEST => self::LOG_OUTPUT_FILE_APPEND,
        ILARIA_ConfigurationLocal::SERVER_MODE_PRODUCTION => self::LOG_OUTPUT_FILE_APPEND,
    );

    // logging mode for debug
    private static $logDebugMode = array(
        ILARIA_ConfigurationLocal::SERVER_MODE_DEVELOPMENT => self::LOG_OUTPUT_FILE_ERASE,
        ILARIA_ConfigurationLocal::SERVER_MODE_TEST => self::LOG_OUTPUT_NONE,
        ILARIA_ConfigurationLocal::SERVER_MODE_PRODUCTION => self::LOG_OUTPUT_NONE,
    );

    // #################################################################################################################
    // ##                                          GLOBAL CONTEXT-FREE PARAMETERS                                     ##
    // #################################################################################################################

    // folders names
    private static $siteApp = 'app';
    private static $siteAppController = 'controller';
    private static $siteAppModel = 'model';
    private static $siteAppTemplate = 'template';
    private static $siteAppView = 'view';
    private static $siteAppMenu = 'menu';
    private static $siteAppAsynchronous = 'asynchronous';
    private static $siteFra = 'ilaria';
    private static $siteFraApplication = 'application';
    private static $siteFraConfiguration = 'configuration';
    private static $siteFraCore = 'core';
    private static $siteFraDatabase = 'database';
    private static $siteFraLog = 'log';
    private static $siteFraModule = 'module';
    private static $siteFraSecurity = 'security';
    private static $siteWeb = 'webroot';
    private static $siteWebStatic = 'static';
    private static $siteWebDynamic = 'dynamic';
    private static $siteWebStyle = 'style';
    private static $siteWebScript = 'script';
    private static $siteWebAssets = 'assets';
    private static $siteLog = 'logs';

    // framework classes to load
    private static $logFiles = array('Writer', 'Manager');
    private static $coreFiles = array('Error', 'Loader', 'Request', 'Dispatcher', 'Module');
    private static $securityFiles = array('Manager', 'Access', 'Encryption');
    private static $databaseFiles = array('Manager', 'Instance', 'Query');
    private static $applicationFiles = array('Model', 'View', 'Controller', 'Template', 'Menu', 'Asynchronous');
    private static $moduleFiles = array('Mysql', 'NoAuth', 'Formbuilder');

    // default controller and action to execute
    private static $defaultController = 'home';
    private static $defaultAction = 'index';

    // primary authentification module
    private static $primaryAuth = 'NoAuth';

    // default template names
    private static $defaultTemplateHttp = 'http';
    private static $defaultTemplateAjax = 'ajax';

    // #################################################################################################################
    // ##                                            PRIVATE STUBS FUNCTIONS                                          ##
    // #################################################################################################################

    // helper
    private static function simplifyPath($path)
    {
        if ($path == DS)
        {
            return '';
        }
        else
        {
            return $path;
        }
    }

    // folders names
    private static function getFsRoot()
    {
        $serverMode = ILARIA_ConfigurationLocal::getServerMode();
        return $_SERVER['DOCUMENT_ROOT'] . self::simplifyPath(DS . self::$siteRoot[$serverMode]);
    }

    private static function getFsApp()
    {
        return self::getFsRoot() . self::simplifyPath(DS . self::$siteApp);
    }

    private static function getFsFra()
    {
        return self::getFsRoot() . self::simplifyPath(DS . self::$siteFra);
    }

    private static function getFsWeb()
    {
        return self::getFsRoot() . self::simplifyPath(DS . self::$siteWeb);
    }

    private static function getFsFraApplication()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraApplication);
    }

    private static function getFsFraConfiguration()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraConfiguration);
    }

    private static function getFsFraCore()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraCore);
    }

    private static function getFsFraDatabase()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraDatabase);
    }

    private static function getFsFraLog()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraLog);
    }

    private static function getFsFraModule()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraModule);
    }

    private static function getFsFraSecurity()
    {
        return self::getFsFra() . self::simplifyPath(DS . self::$siteFraSecurity);
    }

    private static function getFsWebStatic()
    {
        return self::getFsWeb() . self::simplifyPath(DS . self::$siteWebStatic);
    }

    private static function getFsWebDynamic()
    {
        return self::getFsWeb() . self::simplifyPath(DS . self::$siteWebDynamic);
    }

    private static function getUrlRoot()
    {
        $serverMode = ILARIA_ConfigurationLocal::getServerMode();
        return self::simplifyPath(DS . self::$siteRoot[$serverMode]);
    }

    private static function getUrlWeb()
    {
        return self::getUrlRoot() . self::simplifyPath(DS . self::$siteWeb);
    }

    private static function getUrlWebStatic()
    {
        return self::getUrlWeb() . self::simplifyPath(DS . self::$siteWebStatic);
    }

    // #################################################################################################################
    // ##                                             PUBLIC STUBS FUNCTIONS                                          ##
    // #################################################################################################################

    public static function getErrorMode()
    {
        return self::$errorMode[ILARIA_ConfigurationLocal::getServerMode()];
    }

    public static function getLogErrorMode()
    {
        return self::$logErrorMode[ILARIA_ConfigurationLocal::getServerMode()];
    }

    public static function getLogDebugMode()
    {
        return self::$logDebugMode[ILARIA_ConfigurationLocal::getServerMode()];
    }

    public static function getFsAppController()
    {
        return self::getFsApp() . self::simplifyPath(DS . self::$siteAppController);
    }

    public static function getFsAppModel()
    {
        return self::getFsApp() . self::simplifyPath(DS . self::$siteAppModel);
    }

    public static function getFsAppTemplate()
    {
        return self::getFsApp() . self::simplifyPath(DS . self::$siteAppTemplate);
    }

    public static function getFsAppView()
    {
        return self::getFsApp() . self::simplifyPath(DS . self::$siteAppView);
    }

    public static function getFsAppMenu()
    {
        return self::getFsApp() . self::simplifyPath(DS . self::$siteAppMenu);
    }

    public static function getFsAppAsynchronous()
    {
        return self::getFsApp() . self::simplifyPath(DS . self::$siteAppAsynchronous);
    }

    public static function getFsWebStaticAssets()
    {
        return self::getFsWebStatic() . self::simplifyPath(DS . self::$siteWebAssets);
    }

    public static function getFsWebStaticScript()
    {
        return self::getFsWebStatic() . self::simplifyPath(DS . self::$siteWebScript);
    }

    public static function getFsWebStaticStyle()
    {
        return self::getFsWebStatic() . self::simplifyPath(DS . self::$siteWebStyle);
    }

    public static function getFsLogs()
    {
        return self::getFsRoot() . self::simplifyPath(DS . self::$siteLog);
    }

    public static function getUrlWebStaticAssets()
    {
        return self::getUrlWebStatic() . self::simplifyPath(DS . self::$siteWebAssets);
    }

    public static function getUrlWebStaticStyle()
    {
        return self::getUrlWebStatic() . self::simplifyPath(DS . self::$siteWebStyle);
    }

    public static function getUrlWebStaticScript()
    {
        return self::getUrlWebStatic() . self::simplifyPath(DS . self::$siteWebScript);
    }

    public static function cleanRequestChain($requestChain)
    {
        $serverMode = ILARIA_ConfigurationLocal::getServerMode();
        return str_replace(self::simplifyPath(DS . self::$siteRoot[$serverMode]), '', $requestChain);
    }

    public static function buildRequestChain($controllerName, $actionName, $getArgs)
    {
        $serverMode = ILARIA_ConfigurationLocal::getServerMode();
        $requestChain = self::simplifyPath(DS . self::$siteRoot[$serverMode]);
        $requestChain .= DS . $controllerName;
        $requestChain .= DS . $actionName;
        $requestChain .= DS;
        $firstArg = true;
        foreach ($getArgs as $key => $value)
        {
            $requestChain .= ($firstArg ? '' : '&') . $key . '=' . $value;
            $firstArg = false;
        }
        return $requestChain;
    }

    // #################################################################################################################
    // ##                                                 PUBLIC GETTERS                                              ##
    // #################################################################################################################

    // framework classes to load
    public static function getFilesToLoad()
    {
        $files = array();
        foreach (self::$logFiles as $file)
        {
            $files[] = self::getFsFraLog() . DS . "ILARIA_Log" . $file . ".php";
        }
        foreach (self::$coreFiles as $file)
        {
            $files[] = self::getFsFraCore() . DS . "ILARIA_Core" . $file . ".php";
        }
        foreach (self::$securityFiles as $file)
        {
            $files[] = self::getFsFraSecurity() . DS . "ILARIA_Security" . $file . ".php";
        }
        foreach (self::$databaseFiles as $file)
        {
            $files[] = self::getFsFraDatabase() . DS . "ILARIA_Database" . $file . ".php";
        }
        foreach (self::$applicationFiles as $file)
        {
            $files[] = self::getFsFraApplication() . DS . "ILARIA_Application" . $file . ".php";
        }
        foreach (self::$moduleFiles as $file)
        {
            $files[] = self::getFsFraModule() . DS . "ILARIA_Module" . $file . ".php";
        }
        return $files;
    }

    // default values
    public static function getDefaultController() { return self::$defaultController; }
    public static function getDefaultAction() { return self::$defaultAction; }

    // primary auth module
    public static function getPrimaryAuth() { return self::$primaryAuth; }

    // default template
    public static function getDefaultTemplateHttp() { return self::$defaultTemplateHttp; }
    public static function getDefaultTemplateAjax() { return self::$defaultTemplateAjax; }
}