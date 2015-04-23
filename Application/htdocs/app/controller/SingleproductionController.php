<?php

class SingleproductionController extends ILARIA_ApplicationController
{
    const FORM_NAME = 'singleproduction';

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
            default:
                return false;
        }
    }

    public function action_insert($request)
    {
        // Instanciate model
        $model = $this->getModel("singleproduction");

        // Instanciate view
        $view = $this->getView("singleproductionform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PRODUCTIONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_NAME . "_dataid"))
        {
            // Gather values
            $title = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_title"));
            $year = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_year"));
            $gender = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_gender"));
            $kind = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_kind"));

            // Insert values
            if (($id = $model->insert($title, $year, $kind, $gender)) >= 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The single production \"" . $title . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The single production \"" . $title . "\" was not inserted");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'insert',
            'genders' => $model->getListGenders(),
            'kinds' => $model->getListKinds(),
        ));

        // Return view
        return $view;
    }

    public function action_update($request)
    {
        // Instanciate model
        $model = $this->getModel("singleproduction");

        // Instanciate view
        $view = $this->getView("singleproductionform");
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
            $title = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_title"));
            $year = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_year"));
            $gender = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_gender"));
            $kind = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_kind"));

            // Update values
            if ($model->update($id, $title, $year, $kind, $gender) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The single production \"" . $title . "\" was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The person \"" . $title . "\" was not updated");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'update',
            'genders' => $model->getListGenders(),
            'kinds' => $model->getListKinds(),
            'id' => $id,
        ));

        // Return view
        return $view;
    }

    public function action_delete($request)
    {
        // Instanciate model
        $model = $this->getModel("singleproduction");

        // Get ID
        $id = $request->getGetArg('id');

        // Gather infos
        $title = $model->getFieldContent($id, 'title');

        // If deletion confirmed
        if ($request->existGetArg("confirm"))
        {
            // Proceed with deletion
            if ($model->delete($id) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("directaccess");
                $view = $controller->action_productions(NULL);
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The single production \"" . $title . "\" was successfully deleted");
            }
            else
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The single production \"" . $title . "\" failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>The single production \\\"" . $title . "\\\" will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("singleproduction", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }
}