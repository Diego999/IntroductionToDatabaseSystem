<?php

class Milestone3Controller extends ILARIA_ApplicationController
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
            case 'queryh':
                return true;
            case 'queryi':
                return true;
            case 'queryj':
                return true;
            case 'refreshk':
                return true;
            case 'queryk':
                return true;
            case 'queryl':
                return true;
            case 'querym':
                return true;
            case 'refreshn':
                return true;
            case 'queryn':
                return true;
            default:
                return false;
        }
    }

    public function action_index($request)
    {
        // Create view
        $view = $this->getView('milestone3index');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MS3_QUERIES);
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
        $view = $this->getView('milestone3display');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MS3_QUERIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Load asynchronous module
        $params = array();
        $isRefresh = false;
        if ($request->existGetArg('a'))
        {
            if ($request->getGetArg('a') == 'r')
            {
                $isRefresh = true;
            }
        }
        switch ($request->getGetArg('q'))
        {
            case 'a':
                $asyncModule = $this->getAsynchronous("milestone3querya");
                break;
            case 'b':
                $asyncModule = $this->getAsynchronous("milestone3queryb");
                $params = array(
                    'actorid' => ($request->existPostArg('actorid') ? $request->getPostArg('actorid') : $request->getGetArg('actorid')),
                );
                break;
            case 'c':
                $asyncModule = $this->getAsynchronous("milestone3queryc");
                $params = array(
                    'year' => $request->getPostArg('year'),
                );
                break;
            case 'd':
                $asyncModule = $this->getAsynchronous("milestone3queryd");
                break;
            case 'e':
                $asyncModule = $this->getAsynchronous("milestone3querye");
                break;
            case 'f':
                $asyncModule = $this->getAsynchronous("milestone3queryf");
                break;
            case 'g':
                $asyncModule = $this->getAsynchronous("milestone3queryg");
                break;
            case 'h':
                $asyncModule = $this->getAsynchronous("milestone3queryh");
                break;
            case 'i':
                $asyncModule = $this->getAsynchronous("milestone3queryi");
                break;
            case 'j':
                $asyncModule = $this->getAsynchronous("milestone3queryj");
                break;
            case 'k':
                if ($isRefresh)
                {
                    $asyncModule = $this->getAsynchronous("milestone3refreshk");
                }
                else
                {
                    $asyncModule = $this->getAsynchronous("milestone3queryk");
                }
                break;
            case 'l':
                $asyncModule = $this->getAsynchronous("milestone3queryl");
                break;
            case 'm':
                $asyncModule = $this->getAsynchronous("milestone3querym");
                break;
            case 'n':
                if ($isRefresh)
                {
                    $asyncModule = $this->getAsynchronous("milestone3refreshn");
                }
                else
                {
                    $asyncModule = $this->getAsynchronous("milestone3queryn");
                }
                break;
            default:
                break;
        }

        // Output to view
        $view->prepare(array(
            'async' => $asyncModule,
            'query' => strtoupper($request->getGetArg('q')),
            'params' => $params,
        ));

        // Return view
        return $view;
    }

    public function action_querya($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3querya")->getContent($params);
    }

    public function action_queryb($request)
    {
        $params = array('actorid' => $request->getGetArg('actorid'));
        return $this->getAsynchronous("milestone3queryb")->getContent($params);
    }

    public function action_queryc($request)
    {
        $params = array('year' => $request->getGetArg('year'));
        return $this->getAsynchronous("milestone3queryc")->getContent($params);
    }

    public function action_queryd($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryd")->getContent($params);
    }

    public function action_querye($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3querye")->getContent($params);
    }

    public function action_queryf($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryf")->getContent($params);
    }

    public function action_queryg($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryg")->getContent($params);
    }

    public function action_queryh($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryh")->getContent($params);
    }

    public function action_queryi($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryi")->getContent($params);
    }

    public function action_queryj($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryj")->getContent($params);
    }

    public function action_refreshk($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3refreshk")->getContent($params);
    }

    public function action_queryk($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryk")->getContent($params);
    }

    public function action_queryl($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryl")->getContent($params);
    }

    public function action_querym($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3querym")->getContent($params);
    }

    public function action_refreshn($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3refreshn")->getContent($params);
    }

    public function action_queryn($request)
    {
        $params = array();
        return $this->getAsynchronous("milestone3queryn")->getContent($params);
    }
}