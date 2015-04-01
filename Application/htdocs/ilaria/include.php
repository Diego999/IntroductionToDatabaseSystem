<?php

// Load configuration, local first
require_once 'configuration/ILARIA_ConfigurationLocal.php';
require_once 'configuration/ILARIA_ConfigurationGlobal.php';

// Load framework
$ilariaFiles = ILARIA_ConfigurationGlobal::getFilesToLoad();
foreach ($ilariaFiles as $file)
{
    require_once $file;
}

// Debug message : end of loading
ILARIA_LogManager::getInstance()->getWriterDebug()->write('[include.php] whole framework loaded');