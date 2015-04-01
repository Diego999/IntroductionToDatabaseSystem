<?php

// start session
session_start();

// include framework
require_once '../ilaria/include.php';

// UTF-8 output
header('Content-Type: text/html; charset=utf-8');

// Gather request chain
$requestChain = urldecode($_SERVER['REQUEST_URI']);

// Evict Apple bad requests
if (!(strpos($requestChain, "apple_touch_icon") === FALSE))
{
    exit(-1);
}

// Dispatch and display
$dispatcher = new ILARIA_CoreDispatcher($requestChain);
try
{
    // Dispatch request
    $template = $dispatcher->dispatch();

    // Display result
    $template->display();
}
catch (ILARIA_CoreError $e)
{
    switch ($e->getType())
    {
        case ILARIA_CoreError::GEN_FILE_NOT_FOUND:
            require_once 'errorpages/404.php';
            break;
        case ILARIA_CoreError::GEN_CLASS_NOT_FOUND:
            require_once 'errorpages/404.php';
            break;
        case ILARIA_CoreError::GEN_ACTION_NOT_FOUND:
            require_once 'errorpages/404.php';
            break;
        case ILARIA_CoreError::GEN_MODULE_NOT_FOUND:
            require_once 'errorpages/500.php';
            break;
        case ILARIA_CoreError::GEN_PERMISSION_DENIED:
            require_once 'errorpages/403.php';
            break;
        default:
            require_once 'errorpages/500.php';
            break;
    }
}

// Output loggers to file
ILARIA_LogManager::getInstance()->outputAll();