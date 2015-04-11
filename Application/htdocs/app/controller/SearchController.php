<?php

class SearchController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'index':
                return true;
            case 'result':
                return true;
            case 'charactersbase':
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

    public function action_result($request)
    {
        // Create view
        $view = $this->getView('searchresult');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_SEARCH);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Load asynchronous module
        $asyncCharacters = $this->getAsynchronous("searchcharactersbase");

        // Output to view
        $view->prepare(array(
            'search' => $request->getPostArg('input-value'),
            'charactersbase' => $asyncCharacters
        ));

        // Return view
        return $view;
    }

    public function action_charactersbase($request)
    {
        $params = array();
        if ($request->existGetArg('name'))
        {
            $params['name'] = $request->getGetArg('name');
        }
        return $this->getAsynchronous("searchcharactersbase")->getContent($params);
    }

}
