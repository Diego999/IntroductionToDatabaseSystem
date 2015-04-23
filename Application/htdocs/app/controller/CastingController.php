<?php

class CastingController extends ILARIA_ApplicationController
{
    const FORM_NAME = 'casting';

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
            case 'searchpersons':
                return true;
            default:
                return false;
        }
    }

    public function action_insert($request)
    {
        // Instanciate model
        $model = $this->getModel("casting");

        // Instanciate view
        $view = $this->getView("castingform");
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
            $person = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_person_id"));
            $role = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_role"));
            $character = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_character"));

            // Insert values
            if (($id = $model->insert($person, $productionId, $role, $character)) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $productionId))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "A casting record was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "No casting record was inserted");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'insert',
            'roles' => $model->getListRoles(),
            'prodid' => $productionId,
        ));

        // Return view
        return $view;
    }

    public function action_update($request)
    {
        // Instanciate model
        $model = $this->getModel("casting");

        // Instanciate view
        $view = $this->getView("castingform");
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
            $person = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_person_id"));
            $role = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_role"));
            $character = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_NAME . "_character"));

            // Update values
            if ($model->update($id, $person, $role, $character) == 0)
            {
                // Gather production ID
                $productionId = $model->getFieldContent($id, 'production');

                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("production");
                $view = $controller->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $productionId))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "A casting record was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "A casting record was not updated");
            }
        }

        // Gather production ID
        $productionId = $model->getFieldContent($id, 'production');

        // Prepare form view
        $view->prepare(array(
            'model' => $model,
            'action' => 'update',
            'roles' => $model->getListRoles(),
            'prodid' => $productionId,
            'id' => $id,
        ));

        // Return view
        return $view;
    }

    public function action_delete($request)
    {
        // Instanciate model
        $model = $this->getModel("casting");

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
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "A casting record was successfully deleted");
            }
            else
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "A casting record failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>A casting record will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("casting", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }

    public function action_searchpersons($request)
    {
        // Get model
        $model = $this->getModel("search");

        // Get search value
        $searchValue = str_replace("_", " ", $request->getGetArg('val'));

        // Gather list of persons
        $persons = $model->getPersonsLikeName($searchValue);

        // Deal with error
        if (!is_array($persons))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the persons",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        // Create view
        $view = "<table class=\\\"table table-striped\\\">";
        $view .= "<tr><th>Last name</th><th>First name</th><th>Life dates</th><th></th></tr>";
        foreach ($persons as $person)
        {
            $view .= "<tr><td>" . $person['lastname'] . "</td><td>" . $person['firstname'] . "</td><td>" . ($person['birthdate'] ? $person['birthdate'] : "?") . "-" . ($person['deathdate'] ? $person['deathdate'] : "?") . "</td><td>" . ILARIA_ApplicationAsynchronous::getModalFillFormButton("<span class=\\\"glyphicon glyphicon-ok\\\" aria-hidden=\\\"true\\\"></span>", self::FORM_NAME, 'person', $person['id'], $person['firstname'] . " " . $person['lastname']) . "</td></tr>";
        }
        $view .= "</table>";

        // Create title
        $title = "Persons corresponding to \"" . $searchValue . "\"";

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