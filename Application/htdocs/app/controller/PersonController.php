<?php

class PersonController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'details':
                return true;
            case 'asyncaltnames':
                return true;
            case 'asyncrolessingle':
                return true;
            case 'asyncrolesseries':
                return true;
            default:
                return false;
        }
    }

    public function action_details($request)
    {
        // Create view
        $view = $this->getView('persondetails');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PERSONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create model
        $model = $this->getModel('person');

        // Gather person base infos
        $infos = $model->getPersonInfosGeneral($request->getGetArg('id'));

        // Load asynchronous modules
        $asyncAlternativeNames = $this->getAsynchronous("personalternativenames");
        $asyncRolesSingleProd = $this->getAsynchronous("personrolessingleprod");
        $asyncRolesSeries = $this->getAsynchronous("personrolesseries");

        // Output to view
        $view->prepare(array(
            'infos' => $infos,
            'asyncaltnames' => $asyncAlternativeNames,
            'asyncrolessingle' => $asyncRolesSingleProd,
            'asyncroleseries' => $asyncRolesSeries,
        ));

        // Return view
        return $view;
    }

    public function action_asyncaltnames($request)
    {
        $params = array(
            'person_id' => $request->getGetArg('person_id'),
            'mainname_id' => $request->getGetArg('mainname_id'),
        );
        return $this->getAsynchronous("personalternativenames")->getContent($params);
    }

    public function action_asyncrolessingle($request)
    {
        $params = array(
            'person_id' => $request->getGetArg('person_id'),
        );
        return $this->getAsynchronous("personrolessingleprod")->getContent($params);
    }

    public function action_asyncrolesseries($request)
    {
        $params = array(
            'person_id' => $request->getGetArg('person_id'),
        );
        return $this->getAsynchronous("personrolesseries")->getContent($params);
    }
}