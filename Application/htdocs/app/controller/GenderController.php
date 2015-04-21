<?php

class GenderController extends ILARIA_ApplicationController
{
    const FORM_NAME = "gender";

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
        $model = $this->getModel("gender");

        // Instanciate view
        $view = $this->getView("genderform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MISCELLANEOUS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_NAME . "_dataid"))
        {
            // Gather values
            $name = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_name"));

            // Insert values
            if ($model->insert($name) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("directaccess");
                $view = $controller->action_miscellaneous(NULL);
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The genre \"" . $name . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The genre \"" . $name . "\" was not inserted");
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
        $model = $this->getModel("gender");

        // Instanciate view
        $view = $this->getView("genderform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_MISCELLANEOUS);
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

            // Update values
            if ($model->update($id, $name) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("directaccess");
                $view = $controller->action_miscellaneous(NULL);
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The genre \"" . $name . "\" was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The genre \"" . $name . "\" was not updated");
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
        $model = $this->getModel("gender");

        // Get ID
        $id = $request->getGetArg('id');

        // Gather infos
        $infos = $model->getGenderInfos($id);

        // If deletion confirmed
        if ($request->existGetArg("confirm"))
        {
            // Load other controller, launch action and gather back view
            $controller = ILARIA_CoreLoader::getInstance()->loadController("directaccess");
            $view = $controller->action_miscellaneous(NULL);

            // Proceed with deletion
            if ($model->delete($id) == 0)
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The genre \"" . $infos['name'] . "\" was successfully deleted");
            }
            else
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The genre \"" . $infos['name'] . "\" failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>The genre \\\"" . $infos['name'] . "\\\" will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("gender", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }
}