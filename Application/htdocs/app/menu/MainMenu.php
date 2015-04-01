<?php

class MainMenu extends ILARIA_ApplicationMenu
{
    const MAIN_MENU_KEY = 'd6450020ee9e52977f1fa06b5c1541a01a8f312d';

    const ENTRY_HOME = 'c650ea833e310cc3d78ec6f90b72b3280d788678';
    const ENTRY_SEARCH = 'a74c83afb70cb9e9d44825689b2f5274fae401d8';
    const ENTRY_DIRECT_ACCESS = '7ee4a95d49cb79994664ba7bdf03e5da963308f1';
    const ENTRY_PRODUCTIONS = '3ed499f315dce8678ce870578bb505e78041073d';
    const ENTRY_PERSONS = '87e2b43254b354ab754bfcad33ff4caa614294f9';
    const ENTRY_COMPANIES = 'da55515eda71d9e9d8199b0ad4b1843f161c559b';

    public function display()
    {
        // Gather active entry with recursion
        $activeEntry = NULL;
        switch ($this->getActiveEntry())
        {
            case self::ENTRY_PRODUCTIONS:
            case self::ENTRY_PERSONS:
            case self::ENTRY_COMPANIES:
                $activeEntry = self::ENTRY_DIRECT_ACCESS;
                break;
            default:
                $activeEntry = $this->getActiveEntry();
                break;
        }

        // Output result
        echo "<div class=\"btn-group\">\n";
        foreach($this->getEntries() as $entry)
        {
            $isTopLevelActive = ($activeEntry == $entry[ILARIA_ApplicationMenu::KEY_ID]);
            $isMultiLevels = isset($entry[ILARIA_ApplicationMenu::KEY_SUB]);
            if ($isMultiLevels)
            {
                echo "<div class=\"btn-group\">\n";
                echo "<button type=\"button\" class=\"btn btn-" . ($isTopLevelActive ? "primary" : "default")
                    . " dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\">" . "\n";
                echo $entry[ILARIA_ApplicationMenu::KEY_NAME] . " <span class=\"caret\"></span>" . "\n";
                echo "</button>" . "\n";
                echo "<ul class=\"dropdown-menu\" role=\"menu\">" . "\n";
                foreach ($entry[ILARIA_ApplicationMenu::KEY_SUB] as $subentry)
                {
                    echo "<li><a href=\"" . $subentry[ILARIA_ApplicationMenu::KEY_LINK] . "\""
                        . ($this->getActiveEntry() == $subentry[ILARIA_ApplicationMenu::KEY_ID] ? " class=\"submenu-active\"" : "")
                        . ">" . $subentry[ILARIA_ApplicationMenu::KEY_NAME] . "</a></li>" . "\n";
                }
                echo "</ul>" . "\n";
                echo "</div>\n";
            }
            else
            {
                echo "<a class=\"btn btn-" . ($isTopLevelActive ? "primary" : "default") . "\" href=\""
                    . $entry[ILARIA_ApplicationMenu::KEY_LINK] . "\" role=\"button\">" . $entry[ILARIA_ApplicationMenu::KEY_NAME] . "</a>" . "\n";
            }
        }
        echo "</div>\n";
    }

    protected function getEntries()
    {
        return array(
            array(
                ILARIA_ApplicationMenu::KEY_ID => self::ENTRY_HOME,
                ILARIA_ApplicationMenu::KEY_NAME => 'Home',
                ILARIA_ApplicationMenu::KEY_LINK => ILARIA_ConfigurationGlobal::buildRequestChain('home', 'index', array()),
            ),
            array(
                ILARIA_ApplicationMenu::KEY_ID => self::ENTRY_SEARCH,
                ILARIA_ApplicationMenu::KEY_NAME => 'Search',
                ILARIA_ApplicationMenu::KEY_LINK => ILARIA_ConfigurationGlobal::buildRequestChain('search', 'index', array()),
            ),
            array(
                ILARIA_ApplicationMenu::KEY_ID => self::ENTRY_DIRECT_ACCESS,
                ILARIA_ApplicationMenu::KEY_NAME => 'Direct access',
                ILARIA_ApplicationMenu::KEY_SUB => array(
                    array(
                        ILARIA_ApplicationMenu::KEY_ID => self::ENTRY_PRODUCTIONS,
                        ILARIA_ApplicationMenu::KEY_NAME => 'Productions',
                        ILARIA_ApplicationMenu::KEY_LINK => ILARIA_ConfigurationGlobal::buildRequestChain('directaccess', 'productions', array())
                    ),
                    array(
                        ILARIA_ApplicationMenu::KEY_ID => self::ENTRY_PERSONS,
                        ILARIA_ApplicationMenu::KEY_NAME => 'Persons',
                        ILARIA_ApplicationMenu::KEY_LINK => ILARIA_ConfigurationGlobal::buildRequestChain('directaccess', 'persons', array())
                    ),
                    array(
                        ILARIA_ApplicationMenu::KEY_ID => self::ENTRY_COMPANIES,
                        ILARIA_ApplicationMenu::KEY_NAME => 'Companies',
                        ILARIA_ApplicationMenu::KEY_LINK => ILARIA_ConfigurationGlobal::buildRequestChain('directaccess', 'companies', array())
                    ),
                ),
            ),
        );
    }
}