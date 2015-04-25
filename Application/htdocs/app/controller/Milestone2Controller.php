<?php

class Milestone2Controller extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'index':
                return true;
            case 'display':
                return true;
            case 'querya':
                return true;
            case 'queryb':
                return true;
            case 'queryc':
                return true;
            case 'queryd':
                return true;
            case 'querye':
                return true;
            case 'queryf':
                return true;
            case 'queryg':
                return true;
            default:
                return false;
        }
    }

    public function action_index($request)
    {
        // Create view
        $view = $this->getView('milestone2index');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MS2_QUERIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Output to view
        $view->prepare(array());

        // Return view
        return $view;
    }

    public function action_display($request)
    {
        // Create view
        $view = $this->getView('milestone2display');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MS2_QUERIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Load asynchronous module
        switch ($request->getGetArg('q'))
        {
            case 'a':
                $asyncModule = $this->getAsynchronous("milestone2querya");
                break;
            case 'b':
                $asyncModule = $this->getAsynchronous("milestone2queryb");
                break;
            case 'c':
                $asyncModule = $this->getAsynchronous("milestone2queryc");
                break;
            case 'd':
                $asyncModule = $this->getAsynchronous("milestone2queryd");
                break;
            case 'e':
                $asyncModule = $this->getAsynchronous("milestone2querye");
                break;
            case 'f':
                $asyncModule = $this->getAsynchronous("milestone2queryf");
                break;
            case 'g':
                $asyncModule = $this->getAsynchronous("milestone2queryg");
                break;
            default:
                break;
        }

        // Output to view
        $view->prepare(array(
            'async' => $asyncModule,
            'query' => strtoupper($request->getGetArg('q')),
        ));

        // Return view
        return $view;
    }

    public function action_querya($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2querya")->getContent($params);
    }

    public function action_queryb($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2queryb")->getContent($params);
    }

    public function action_queryc($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2queryc")->getContent($params);
    }

    public function action_queryd($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2queryd")->getContent($params);
    }

    public function action_querye($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2querye")->getContent($params);
    }

    public function action_queryf($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2queryf")->getContent($params);
    }

    public function action_queryg($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone2queryg")->getContent($params);
    }
}