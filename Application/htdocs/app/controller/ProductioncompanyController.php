<?php

class ProductioncompanyController extends ILARIA_ApplicationController
{
    const FORM_NAME = 'productioncompany';

    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'insert':
                return true;
            case 'update':
                return true;
            case 'delete':
                return true;
            case 'searchcompanies':
                return true;
            default:
                return false;
        }
    }

    public function action_insert($request)
    {
        // Instanciate model
        $model = $this->getModel("productioncompany");

        // Instanciate view
        $view = $this->getView("productioncompanyform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PRODUCTIONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Gather production ID
        $productionId = $request->getGetArg('prodid');

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_NAME . "_dataid"))
        {
            // Gather values
            $company = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_company_id"));
            $type = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_type"));

            // Insert values
            if (($id = $model->insert($productionId, $company, $type)) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $productionId))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "A productioncompany record was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "No productioncompany record was inserted");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'insert',
            'types' => $model->getListTypes(),
            'prodid' => $productionId,
        ));

        // Return view
        return $view;
    }

    public function action_update($request)
    {
        // Instanciate model
        $model = $this->getModel("productioncompany");

        // Instanciate view
        $view = $this->getView("productioncompanyform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PRODUCTIONS);
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
            $company = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_company_id"));
            $type = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_type"));

            // Update values
            if ($model->update($id, $company, $type) == 0)
            {
                // Gather production ID
                $productionId = $model->getFieldContent($id, 'production');

                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $productionId))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "A productioncompany record was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "A productioncompany record was not updated");
            }
        }

        // Gather production ID
        $productionId = $model->getFieldContent($id, 'production');

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'update',
            'types' => $model->getListTypes(),
            'prodid' => $productionId,
            'id' => $id,
        ));

        // Return view
        return $view;
    }

    public function action_delete($request)
    {
        // Instanciate model
        $model = $this->getModel("productioncompany");

        // Get ID
        $id = $request->getGetArg('id');

        // If deletion confirmed
        if ($request->existGetArg("confirm"))
        {
            // Gather production ID
            $productionId = $model->getFieldContent($id, 'production');

            // Load other controller, launch action and gather back view
            $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
            $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $productionId))));

            // Proceed with deletion
            if ($model->delete($id) == 0)
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "A productioncompany record was successfully deleted");
            }
            else
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "A productioncompany record failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>A productioncompany record will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("productioncompany", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }

    public function action_searchcompanies($request)
    {
        // Get model
        $model = $this->getModel("search");

        // Get search value
        $searchValue = str_replace("_", " ", $request->getGetArg('val'));

        // Gather list of companies
        $companies = $model->getCompaniesLikeName($searchValue);

        // Deal with error
        if (!is_array($companies))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the companies",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        // Create view
        $view = "<table class=\\\"table table-striped\\\">";
        $view .= "<tr><th>Name</th><th>Country</th><th></th></tr>";
        foreach ($companies as $company)
        {
            $view .= "<tr><td>" . $company['name'] . "</td><td>" . $company['country'] . "</td><td>" . ILARIA_ApplicationAsynchronous::getModalFillFormButton("<span class=\\\"glyphicon glyphicon-ok\\\" aria-hidden=\\\"true\\\"></span>", self::FORM_NAME, 'company', $company['id'], $company['name'] . ($company['country'] ? " (" . $company['country'] . ")" : "")) . "</td></tr>";
        }
        $view .= "</table>";

        // Create title
        $title = "Companies corresponding to \"" . $searchValue . "\"";

        return ILARIA_ApplicationAsynchronous::buildModalAjaxResponse(array(
            ILARIA_ApplicationAsynchronous::MODAL_TITLE => $title,
            ILARIA_ApplicationAsynchronous::MODAL_CONTENT => $view,
            ILARIA_ApplicationAsynchronous::MODAL_BUTTONS => array(
                array(
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_STYLE => "default",
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_TITLE => "Cancel",
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_ACTION => ILARIA_ApplicationAsynchronous::MODAL_ACTION_DISMISS,
                ),
            ),
        ));
    }
}