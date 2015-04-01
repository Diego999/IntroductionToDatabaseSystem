<?php

class ILARIA_LogManager
{
    // #################################################################################################################
    // ##                                         SINGLETON IMPLEMENTATION                                            ##
    // #################################################################################################################

    private function __construct()
    {
        $this->writerErrors = new ILARIA_LogWriter(ILARIA_ConfigurationGlobal::getLogErrorMode());
        $this->writerDebug = new ILARIA_LogWriter(ILARIA_ConfigurationGlobal::getLogDebugMode());
    }

    private static $instance = NULL;
    public static function getInstance()
    {
        if (self::$instance == NULL)
        {
            self::$instance = new ILARIA_LogManager();
        }
        return self::$instance;
    }

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $writerErrors = NULL;
    private $writerDebug = NULL;
    private $otherWriters = array();

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function getWriterErrors() { return $this->writerErrors; }
    public function getWriterDebug() { return $this->writerDebug; }

    public function registerWriter($writer, $name)
    {
        $this->otherWriters[$name] = $writer;
    }

    public function outputAll()
    {
        $this->writerErrors->output('error.log');
        $this->writerDebug->output('debug.log');
        foreach ($this->otherWriters as $name => $writer)
        {
            $writer->output($name . '.log');
        }
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_LogManager.php] class loaded');