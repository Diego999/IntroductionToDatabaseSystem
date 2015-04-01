<?php

abstract class ILARIA_ApplicationController
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct() {}

    abstract public function isAuthorized($actionName, $userToken);

    // #################################################################################################################
    // ##                                            PROTECTED FUNCTIONS                                              ##
    // #################################################################################################################

    protected function getView($viewName)
    {
        try
        {
            $view = ILARIA_CoreLoader::getInstance()->loadView($viewName);
            return $view;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $e->changeType(ILARIA_CoreError::GEN_VIEW_UNLOADABLE);
            throw $e;
        }
    }

    protected function getModel($modelName)
    {
        try
        {
            $model = ILARIA_CoreLoader::getInstance()->loadModel($modelName);
            return $model;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $e->changeType(ILARIA_CoreError::GEN_MODEL_UNLOADABLE);
            throw $e;
        }
    }

    protected function getMenu($menuName)
    {
        try
        {
            $menu = ILARIA_CoreLoader::getInstance()->loadMenu($menuName);
            return $menu;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $e->changeType(ILARIA_CoreError::GEN_MENU_UNLOADABLE);
            throw $e;
        }
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_ApplicationController.php] class loaded');