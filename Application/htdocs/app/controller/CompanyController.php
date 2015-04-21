<?php

class CompanyController extends ILARIA_ApplicationController
{
    const FORM_NAME = 'company';

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
            case 'insert':
                return true;
            case 'update':
                return true;
            case 'delete':
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

    public function action_insert($request)
    {
        // Instanciate model
        $model = $this->getModel("company");

        // Instanciate view
        $view = $this->getView("companyform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_COMPANIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_NAME . "_dataid"))
        {
            // Gather values
            $name = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_name"));
            $country = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_country"));

            // Insert values
            if (($id = $model->insert($name, $country)) >= 0)
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("company", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The company \"" . $name . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The company \"" . $name . "\" was not inserted");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'insert',
        ));

        // Return view
        return $view;
    }

    public function action_update($request)
    {
        // Instanciate model
        $model = $this->getModel("company");

        // Instanciate view
        $view = $this->getView("companyform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_COMPANIES);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Get ID if set
        if ($request->existGetArg('id'))
        {
            $id = $request->getGetArg('id');
        }

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_NAME . "_dataid"))
        {
            // Gather values
            $id = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_dataid"));
            $name = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_name"));
            $country = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_country"));

            // Update values
            if ($model->update($id, $name, $country) == 0)
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("company", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The company \"" . $name . "\" was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The company \"" . $name . "\" was not updated");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'update',
            'id' => $id,
        ));

        // Return view
        return $view;
    }

    public function action_delete($request)
    {
        // Instanciate model
        $model = $this->getModel("company");

        // Get ID
        $id = $request->getGetArg('id');

        // Gather infos
        $infos = $model->getCompanyInfos($id);

        // If deletion confirmed
        if ($request->existGetArg("confirm"))
        {
            // Proceed with deletion
            if ($model->delete($id) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("directaccess");
                $view = $controller->action_companies(NULL);
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The company \"" . $infos['name'] . "\" was successfully deleted");
            }
            else
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("company", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The type \"" . $infos['name'] . "\" failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>The company \\\"" . $infos['name'] . "\\\" will be deleted. Are you sure ?</p>";

            // Create title
            $title = "Confirmation required";

            return ILARIA_ApplicationAsynchronous::buildModalAjaxResponse(array(
                ILARIA_ApplicationAsynchronous::MODAL_TITLE => $title,
                ILARIA_ApplicationAsynchronous::MODAL_CONTENT => $view,
                ILARIA_ApplicationAsynchronous::MODAL_BUTTONS => array(
                    array(
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_STYLE => "default",
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_TITLE => "Cancel",
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_ACTION => ILARIA_ApplicationAsynchronous::MODAL_ACTION_DISMISS,
                    ),
                    array(
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_STYLE => "danger",
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_TITLE => "Delete",
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_ACTION => ILARIA_ApplicationAsynchronous::MODAL_ACTION_LINK,
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("company", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }
}