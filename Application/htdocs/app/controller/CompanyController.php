<?php

class CompanyController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'details':
                return true;
            case 'asyncworksingle':
                return true;
            case 'asyncworkseries':
                return true;
            default:
                return false;
        }
    }

    public function action_details($request)
    {
        // Create view
        $view = $this->getView('companydetails');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_COMPANIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create model
        $model = $this->getModel("company");

        // Gather company infos
        $infos = $model->getCompanyInfos($request->getGetArg('id'));

        // Load asynchronous modules
        $asyncWorkSingle = $this->getAsynchronous("companyworksingle");
        $asyncWorkSeries = $this->getAsynchronous("companyworkseries");

        // Output to view
        $view->prepare(array(
            'infos' => $infos,
            'asyncworksingle' => $asyncWorkSingle,
            'asyncworkseries' => $asyncWorkSeries,
        ));

        // Return view
        return $view;
    }

    public function action_asyncworksingle($request)
    {
        $params = array(
            'company_id' => $request->getGetArg('company_id'),
        );
        return $this->getAsynchronous("companyworksingle")->getContent($params);
    }

    public function action_asyncworkseries($request)
    {
        $params = array(
            'company_id' => $request->getGetArg('company_id'),
        );
        return $this->getAsynchronous("companyworkseries")->getContent($params);
    }
}