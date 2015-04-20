<?php

class DirectaccessController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'productions':
                return true;
            case 'persons':
                return true;
            case 'companies':
                return true;
            case 'miscellaneous':
                return true;
            case 'misclistroles':
                return true;
            case 'misclistgenders':
                return true;
            case 'misclisttypes':
                return true;
            case 'misclistkinds':
                return true;
            default:
                return false;
        }
    }

    public function action_productions($request)
    {
        // Create view
        $view = $this->getView('directaccessproductions');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PRODUCTIONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create model
        $model = $this->getModel("production");

        // Gather statistics
        $stats = $model->getStatistics();

        // Output to view
        $view->prepare(array(
            'stats' => $stats,
        ));

        // Return view
        return $view;
    }

    public function action_persons($request)
    {
        // Create view
        $view = $this->getView('directaccesspersons');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PERSONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create model
        $model = $this->getModel("person");

        // Gather statistics
        $stats = $model->getStatistics();

        // Output to view
        $view->prepare(array(
            'stats' => $stats,
        ));

        // Return view
        return $view;
    }

    public function action_companies($request)
    {
        // Create view
        $view = $this->getView('directaccesscompanies');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_COMPANIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create model
        $model = $this->getModel("company");

        // Gather statistics
        $stats = $model->getStatistics();

        // Output to view
        $view->prepare(array(
            'stats' => $stats,
        ));

        // Return view
        return $view;
    }

    public function action_miscellaneous($request)
    {
        // Create view
        $view = $this->getView('directaccessmiscellaneous');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MISCELLANEOUS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Output to view
        $view->prepare(array(
            'asyncroles' => $this->getAsynchronous("misclistroles"),
            'asyncgenders' => $this->getAsynchronous("misclistgenders"),
            'asynctypes' => $this->getAsynchronous("misclisttypes"),
            'asynckinds' => $this->getAsynchronous("misclistkinds"),
        ));

        // Return view
        return $view;

    }

    public function action_misclistroles($request)
    {
        $params = array();
        return $this->getAsynchronous("misclistroles")->getContent($params);
    }

    public function action_misclistgenders($request)
    {
        $params = array();
        return $this->getAsynchronous("misclistgenders")->getContent($params);
    }

    public function action_misclisttypes($request)
    {
        $params = array();
        return $this->getAsynchronous("misclisttypes")->getContent($params);
    }

    public function action_misclistkinds($request)
    {
        $params = array();
        return $this->getAsynchronous("misclistkinds")->getContent($params);
    }
}
