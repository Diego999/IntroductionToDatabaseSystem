<?php

abstract class ILARIA_ApplicationModel
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $database = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct()
    {
        $this->database = ILARIA_DatabaseManager::getInstance()->getRelationalInstance($this->getDbIdentifier(), $this->getDbModule());
        $this->database->openConnection($this->getDbConnectionSettings());
    }

    // #################################################################################################################
    // ##                                             PROTECTED FUNCTIONS                                             ##
    // #################################################################################################################

    protected function getDatabase()
    {
        return $this->database;
    }

    protected function quoteOrNull($value)
    {
        if ($value == '')
        {
            return "NULL";
        }
        else
        {
            return $this->getDatabase()->quote($value);
        }
    }

    protected function quote($value)
    {
        return $this->getDatabase()->quote($value);
    }

    abstract protected function getDbIdentifier();
    abstract protected function getDbModule();
    abstract protected function getDbConnectionSettings();

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################


}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_ApplicationModel.php] class loaded');