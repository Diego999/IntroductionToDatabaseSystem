<?php

class SerieController extends ILARIA_ApplicationController
{
    const FORM_NAME = 'serie';

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
        $model = $this->getModel("serie");

        // Instanciate view
        $view = $this->getView("serieform");
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
            $yearstart = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_yearstart"));
            $yearend = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_yearend"));
            $gender = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_gender"));

            // Insert values
            if (($id = $model->insert($title, $yearstart, $yearend, $gender)) >= 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The serie \"" . $title . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The serie \"" . $title . "\" was not inserted");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'insert',
            'genders' => $model->getListGenders(),
        ));

        // Return view
        return $view;
    }

    public function action_update($request)
    {
        // Instanciate model
        $model = $this->getModel("serie");

        // Instanciate view
        $view = $this->getView("serieform");
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
            $yearstart = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_yearstart"));
            $yearend = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_yearend"));
            $gender = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_gender"));

            // Update values
            if ($model->update($id, $title, $yearstart, $yearend, $gender) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The serie \"" . $title . "\" was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The serie \"" . $title . "\" was not updated");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'update',
            'genders' => $model->getListGenders(),
            'id' => $id,
        ));

        // Return view
        return $view;
    }

    public function action_delete($request)
    {
        // Instanciate model
        $model = $this->getModel("serie");

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
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The serie \"" . $title . "\" was successfully deleted");
            }
            else
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The serie \"" . $title . "\" failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>The serie \\\"" . $title . "\\\" and all its related seasons and episodes will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("serie", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }
}