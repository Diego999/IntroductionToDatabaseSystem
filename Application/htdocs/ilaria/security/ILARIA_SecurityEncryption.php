<?php

class ILARIA_SecurityEncryption
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $module = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($module)
    {
        $this->module = $module;
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_SecurityEncryption.php] class loaded');