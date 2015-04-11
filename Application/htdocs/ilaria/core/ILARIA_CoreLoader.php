<?php

class ILARIA_CoreLoader
{
    // #################################################################################################################
    // ##                                         SINGLETON IMPLEMENTATION                                            ##
    // #################################################################################################################

    private function __construct() {}
    private static $instance = NULL;
    public static function getInstance()
    {
        if (self::$instance == NULL)
        {
            self::$instance = new ILARIA_CoreLoader();
        }
        return self::$instance;
    }

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function loadController($name)
    {
        // Prepare locations
        $folder = ILARIA_ConfigurationGlobal::getFsAppController();
        $name = ucfirst(strtolower($name)) . 'Controller';

        // Try to load the corresponding class
        try
        {
            $this->loadClass($name, $folder);
        }

        // Catch and deal with errors relative to class inclusion
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    throw new ILARIA_CoreError('File for controller ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    throw new ILARIA_CoreError('Class for controller ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                default:
                    throw $e;
            }
        }

        // Instanciate and return
        return new $name();
    }

    public function loadModel($name)
    {
        // Prepare locations
        $folder = ILARIA_ConfigurationGlobal::getFsAppModel();
        $name = ucfirst(strtolower($name)) . 'Model';

        // Try to load the corresponding class
        try
        {
            $this->loadClass($name, $folder);
        }

        // Catch and deal with errors relative to class inclusion
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    throw new ILARIA_CoreError('File for model ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    throw new ILARIA_CoreError('Class for model ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                default:
                    throw $e;
            }
        }

        // Instanciate and return
        return new $name();
    }

    public function loadView($name)
    {
        // Prepare locations
        $folder = ILARIA_ConfigurationGlobal::getFsAppView();
        $name = ucfirst(strtolower($name)) . 'View';

        // Try to load the corresponding class
        try
        {
            $this->loadClass($name, $folder);
        }

        // Catch and deal with errors relative to class inclusion
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    throw new ILARIA_CoreError('File for view ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    throw new ILARIA_CoreError('Class for view ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                default:
                    throw $e;
            }
        }

        // Instanciate and return
        return new $name();
    }

    public function loadTemplate($name)
    {
        // Prepare locations
        $folder = ILARIA_ConfigurationGlobal::getFsAppTemplate();
        $name = ucfirst(strtolower($name)) . 'Template';

        // Try to load the corresponding class
        try
        {
            $this->loadClass($name, $folder);
        }

        // Catch and deal with errors relative to class inclusion
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    throw new ILARIA_CoreError('File for template ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    throw new ILARIA_CoreError('Class for template ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                default:
                    throw $e;
            }
        }

        // Instanciate and return
        return new $name();
    }

    public function loadMenu($name)
    {
        // Prepare locations
        $folder = ILARIA_ConfigurationGlobal::getFsAppMenu();
        $name = ucfirst(strtolower($name)) . 'Menu';

        // Try to load the corresponding class
        try
        {
            $this->loadClass($name, $folder);
        }

        // Catch and deal with errors relative to class inclusion
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    throw new ILARIA_CoreError('File for menu ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    throw new ILARIA_CoreError('Class for menu ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                    break;
                default:
                    throw $e;
            }
        }

        // Instanciate and return
        return new $name();
    }

    public function loadAsynchronous($name)
    {
        // Prepare locations
        $folder = ILARIA_ConfigurationGlobal::getFsAppAsynchronous();
        $name = ucfirst(strtolower($name)) . 'Asynchronous';

        // Try to load the corresponding class
        try
        {
            $this->loadClass($name, $folder);
        }

        // Catch and deal with errors relative to class inclusion
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    throw new ILARIA_CoreError('File for asynchronous ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    throw new ILARIA_CoreError('Class for asynchronous ' . $name . ' not found on server',
                        ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                        ILARIA_CoreError::LEVEL_SERVER);
                default:
                    throw $e;
            }
        }

        // Instanciate and return
        return new $name();
    }

    public function includeStyle($file)
    {
        $filename = ILARIA_ConfigurationGlobal::getFsWebStaticStyle() . DS  . $file;
        if (!file_exists($filename))
        {
            throw new ILARIA_CoreError('Stylesheet ' . $file . ' not found on server',
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
        return '<link rel="stylesheet" type="text/css" href="' . ILARIA_ConfigurationGlobal::getUrlWebStaticStyle() . DS . $file . '" />';
    }

    public function includeScript($file)
    {
        $filename = ILARIA_ConfigurationGlobal::getFsWebStaticScript() . DS . $file;
        if (!file_exists($filename))
        {
            throw new ILARIA_CoreError('Script ' . $file . ' not found on server',
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
        return '<script type="text/javascript" src="' . ILARIA_ConfigurationGlobal::getUrlWebStaticScript() . DS . $file . '"></script>';
    }

    public function includeAsset($name)
    {
        $filename = ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . $name;
        if (!file_exists($filename))
        {
            throw new ILARIA_CoreError('Asset ' . $name . ' not found on server',
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
        return ILARIA_ConfigurationGlobal::getUrlWebStaticAssets() . DS . $name;
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################

    private function loadClass($name, $folder)
    {
        // Check for class or interface existence
        if (class_exists($name, false) or interface_exists($name, false))
        {
            return;
        }

        // Assemble name of class, extension and folder
        $file = $folder . DS . $name . '.php';

        // Check file existence
        if (!file_exists($file))
        {
            throw new ILARIA_CoreError('File ' . $file . ' not found',
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }

        // Import file
        require_once $file;

        // Check presence of class or interface in the file
        if (!class_exists($name, false) and !interface_exists($name, false))
        {
            throw new ILARIA_CoreError('Class ' . $name . ' not found',
                ILARIA_CoreError::GEN_CLASS_NOT_FOUND,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreLoader.php] class loaded');