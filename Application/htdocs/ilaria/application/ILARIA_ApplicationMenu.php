<?php

abstract class ILARIA_ApplicationMenu
{
    // #################################################################################################################
    // ##                                             STATIC MANAGEMENT                                               ##
    // #################################################################################################################

    const KEY_ID = '3d588627ff471e8d2e0635056324a61d1fe2f96b';
    const KEY_NAME = 'd7924e6f108574b63716093928d91123d7bf1b17';
    const KEY_LINK = 'c19d9a7a0b15f87d58a11ed66353b4417dfc4d2e';
    const KEY_SUB = '1252a839c55aa124d8a66077dddeca95b6857d2f';

    private static $menus = array();

    public static function registerMenu($key, $menu)
    {
        self::$menus[$key] = $menu;
    }

    public static function getMenu($key)
    {
        return self::$menus[$key];
    }

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $activeEntry = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function setActiveEntry($entry)
    {
        $this->activeEntry = $entry;
    }

    abstract public function display();

    // #################################################################################################################
    // ##                                            PROTECTED FUNCTIONS                                              ##
    // #################################################################################################################

    protected function getActiveEntry()
    {
        return $this->activeEntry;
    }

    abstract protected function getEntries();

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_ApplicationMenu.php] class loaded');