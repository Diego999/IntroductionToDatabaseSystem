<?php

class SearchController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'index':
                return true;
            default:
                return false;
        }
    }

    public function action_index($request)
    {
        // Create view
        $view = $this->getView('searchindex');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_SEARCH);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Output to view
        $view->prepare(array());

        // Return view
        return $view;
    }
}
