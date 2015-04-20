<?php

class TypeController extends ILARIA_ApplicationController
{
    const FORM_NAME = "type";

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
        $model = $this->getModel("type");

        // Instanciate view
        $view = $this->getView("typeform");
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
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The type \"" . $name . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The type \"" . $name . "\" was not added");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
        ));

        // Return view
        return $view;
    }

    public function action_update($request)
    {

    }

    public function action_delete($request)
    {

    }
}