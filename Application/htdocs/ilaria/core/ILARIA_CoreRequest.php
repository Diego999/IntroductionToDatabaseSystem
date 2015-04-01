<?php

class ILARIA_CoreRequest
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    const TYPE_HTTP = 'b314affea2b4c8cd411c13d93c92eb94258d48fe';
    const TYPE_AJAX = '50adecba2b00f17a41ec9823857504d7c5e3ad4a';

    private $type = NULL;
    private $controllerName = NULL;
    private $actionName = NULL;
    private $argsGet = array();
    private $argsPost = array();
    private $argsFile = array();

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($requestChain)
    {
        // Detect and register request type
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower(ILARIA_SecurityManager::in($_SERVER['HTTP_X_REQUESTED_WITH'])) === 'xmlhttprequest')
        {
            $this->type = self::TYPE_AJAX;
        }
        else
        {
            $this->type = self::TYPE_HTTP;
        }
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest::__construct] request type is '
            . ($this->type == self::TYPE_HTTP ? 'TYPE_HTTP' : 'TYPE_AJAX'));

        // Prepare the request chain for parsing
        $requestChain = trim(ILARIA_SecurityManager::in($requestChain), '/&=');

        // Parse the request chain
        $requestTab = array();
        if (!empty($requestChain))
        {
            $requestTab = explode('/', $requestChain);
        }

        // Obtain controller name
        if (isset($requestTab[0]))
        {
            $this->controllerName = $requestTab[0];
        }
        else
        {
            $this->controllerName = ILARIA_ConfigurationGlobal::getDefaultController();
        }
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest::__construct] controller name : ' . $this->controllerName);

        // Obtain action name
        if (isset($requestTab[1]))
        {
            $this->actionName = strtolower($requestTab[1]);
        }
        else
        {
            $this->actionName = strtolower(ILARIA_ConfigurationGlobal::getDefaultAction());
        }
        ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest::__construct] action name : ' . $this->actionName);

        // Obtain arguments
        if (isset($requestTab[2]))
        {
            $tab = explode('&', $requestTab[2]);
            foreach ($tab as $arg)
            {
                $content = explode('=', $arg);
                if (isset($content[1]) && !isset($content[2]))
                {
                    $this->argsGet[$content[0]] = $content[1];
                    ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest::__construct] GET argument : ' . $content[0] . '=' . $content[1]);
                }
                else
                {
                    throw new ILARIA_CoreError('Incorrect argument ' . $content . ' in URL',
                        ILARIA_CoreError::GEN_INCORRECT_GET_ARG,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }
        }

        // Obtain POST args
        foreach ($_POST as $key => $value)
        {
            $this->argsPost[ILARIA_SecurityManager::in($key)] = ILARIA_SecurityManager::in($value);
            ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest::__construct] POST argument : ' . $key . '=' . $value);
        }

        // Obtain FILE args
        foreach ($_FILES as $key => $value)
        {
            $this->argsFile[ILARIA_SecurityManager::in($key)] = ILARIA_SecurityManager::in($value);
            ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest::__construct] FILE argument : ' . $key);
        }
    }

    public function existGetArg($key)
    {
        return isset($this->argsGet[$key]);
    }

    public function getGetArg($key)
    {
        if ($this->existGetArg($key))
        {
            return $this->argsGet[$key];
        }
        else
        {
            throw new ILARIA_CoreError('GET argument ' . $key . ' is not registered',
                ILARIA_CoreError::GEN_NOT_REG_GET_ARG,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    public function existPostArg($key)
    {
        return isset($this->argsPost[$key]);
    }

    public function getPostArg($key)
    {
        if ($this->existPostArg($key))
        {
            return $this->argsPost[$key];
        }
        else
        {
            throw new ILARIA_CoreError('POST argument ' . $key . ' is not registered',
                ILARIA_CoreError::GEN_NOT_REG_POST_ARG,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    public function existFileArg($key)
    {
        return isset($this->argsFile[$key]);
    }

    public function getFileArg($key)
    {
        if ($this->existFileArg($key))
        {
            return $this->argsFile[$key];
        }
        else
        {
            throw new ILARIA_CoreError('FILE argument ' . $key . ' is not registered',
                ILARIA_CoreError::GEN_NOT_REG_FILE_ARG,
                ILARIA_CoreError::LEVEL_SERVER);
        }
    }

    public function getControllerName()
    {
        return $this->controllerName;
    }

    public function getActionName()
    {
        return $this->actionName;
    }

    public function getRequestType()
    {
        return $this->type;
    }

    // #################################################################################################################
    // ##                                             PRIVATE FUNCTIONS                                               ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreRequest.php] class loaded');