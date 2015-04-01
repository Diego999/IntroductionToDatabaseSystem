<?php

class ILARIA_CoreDispatcher
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $request = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($requestChain)
    {
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::__construct] Loading dispatcher');
        $requestChain = ILARIA_ConfigurationGlobal::cleanRequestChain($requestChain);
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::__construct] Request chain : ' . $requestChain);
        $this->request = new ILARIA_CoreRequest($requestChain);
    }

    public function dispatch()
    {
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::dispatch] Starting dispatch process');

        // Load all modules
        $this->loadModules();

        // Find primary authentification security module
        try
        {
            $primaryAuth = ILARIA_SecurityManager::getInstance()->getPrimaryAuth();
        }
        catch (ILARIA_CoreError $e)
        {
            ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] Primary authentification module failed to load');
            $e->writeToLog();
            throw $e;
        }

        // Wrapper for action call
        $view = $this->actionWrap($primaryAuth);

        // Prepare template wrapping given view
        $template = $this->prepareTemplate($view);

        // Return template ready to be rendered
        return $template;
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################

    private function loadModules()
    {
        ILARIA_CoreModule::loadModules();
    }

    private function actionWrap($primaryAuth)
    {
        // Build controller
        $controllerName = $this->request->getControllerName();
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] Building controller with name ' . $controllerName);
        try
        {
            $controller = ILARIA_CoreLoader::getInstance()->loadController($controllerName);
        }
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] File not found for controller ' . $controllerName);
                    $e->writeToLog();
                    throw $e;
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] Class not found for controller ' . $controllerName);
                    $e->writeToLog();
                    throw $e;
                    break;
                default:
                    throw $e;
            }
        }

        // Check for security authorization
        $actionName = 'action_' . $this->request->getActionName();
        if (!$controller->isAuthorized($this->request->getActionName(), $primaryAuth->getUserToken()))
        {
            ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] Permission denied for action ' . $actionName . ' of controller ' . $controllerName);
            $e = new ILARIA_CoreError('Permission denied for action ' . $actionName . ' of controller ' . $controllerName,
                ILARIA_CoreError::GEN_PERMISSION_DENIED,
                ILARIA_CoreError::LEVEL_ADMIN);
            $e->writeToLog();
            throw $e;
        }

        // Launch action
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] Calling action with name ' . $actionName);
        if (method_exists($controller, $actionName))
        {
            return $controller->$actionName($this->request);
        }
        else
        {
            ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::actionWrap] Action ' . $actionName . ' not found in controller ' . $controllerName);
            $e = new ILARIA_CoreError('Action ' . $actionName . ' not found in controller ' . $controllerName,
                ILARIA_CoreError::GEN_ACTION_NOT_FOUND, ILARIA_CoreError::LEVEL_SERVER);
            $e->writeToLog();
            throw $e;
        }
    }

    private function prepareTemplate($view)
    {
        // Apply default template if needed
        if ($view->getTemplateName() == NULL)
        {
            switch ($this->request->getRequestType())
            {
                case ILARIA_CoreRequest::TYPE_HTTP:
                    $view->setTemplateName(ILARIA_ConfigurationGlobal::getDefaultTemplateHttp());
                    break;
                case ILARIA_CoreRequest::TYPE_AJAX:
                    $view->setTemplateName(ILARIA_ConfigurationGlobal::getDefaultTemplateAjax());
                    break;
                default:
                    $view->setTemplateName(ILARIA_ConfigurationGlobal::getDefaultTemplateHttp());
                    break;
            }
        }

        // Build template
        $templateName = $view->getTemplateName();
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::prepareTemplate] Building template with name ' . $templateName);
        try
        {
            $template = ILARIA_CoreLoader::getInstance()->loadTemplate($templateName);
        }
        catch (ILARIA_CoreError $e)
        {
            switch ($e->getType())
            {
                case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
                    ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::prepareTemplate] File not found for template ' . $templateName);
                    $e->writeToLog();
                    throw $e;
                    break;
                case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
                    ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher::prepareTemplate] Class not found for template ' . $templateName);
                    $e->writeToLog();
                    throw $e;
                    break;
                default:
                    throw $e;
            }
        }

        // Prepare template
        $template->prepare($view);

        return $template;
    }
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreDispatcher.php] class loaded');