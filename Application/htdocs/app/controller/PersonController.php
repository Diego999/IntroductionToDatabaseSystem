<?php

class PersonController extends ILARIA_ApplicationController
{
    const FORM_BASE_NAME = 'personbase';
    const FORM_ALTNAME_NAME = 'personaltname';

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
            case 'insert':
                return true;
            case 'update':
                return true;
            case 'delete':
                return true;
            case 'insertaltname':
                return true;
            case 'updatealtname':
                return true;
            case 'deletealtname':
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

    public function action_insert($request)
    {
        // Instanciate model
        $model = $this->getModel("person");

        // Instanciate view
        $view = $this->getView("personbaseform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PERSONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_BASE_NAME . "_dataid"))
        {
            // Gather values
            $firstname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_firstname"));
            $lastname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_lastname"));
            $gender = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_gender"));
            $trivia = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_trivia"));
            $quotes = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_quotes"));
            $birthdate = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_birthdate"));
            $deathdate = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_deathdate"));
            $birthname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_birthname"));
            $minibiography = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_minibiography"));
            $spouse = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_spouse"));
            $height = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_height"));

            // Correct gender
            if ($gender == "u")
            {
                $gender = "";
            }

            // Insert values
            if (($id = $model->insert($firstname, $lastname, $gender, $trivia, $quotes, $birthdate, $deathdate, $birthname, $minibiography, $spouse, $height)) >= 0)
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The person \"" . $firstname . " " . $lastname . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The person \"" . $firstname . " " . $lastname . "\" was not inserted");
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
        $model = $this->getModel("person");

        // Instanciate view
        $view = $this->getView("personbaseform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PERSONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Get ID if set
        if ($request->existGetArg('id'))
        {
            $id = $request->getGetArg('id');
        }

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_BASE_NAME . "_dataid"))
        {
            // Gather values
            $id = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_dataid"));
            $firstname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_firstname"));
            $lastname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_lastname"));
            $gender = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_gender"));
            $trivia = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_trivia"));
            $quotes = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_quotes"));
            $birthdate = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_birthdate"));
            $deathdate = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_deathdate"));
            $birthname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_birthname"));
            $minibiography = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_minibiography"));
            $spouse = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_spouse"));
            $height = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_BASE_NAME . "_height"));

            // Correct gender
            if ($gender == "u")
            {
                $gender = "";
            }

            // Update values
            if ($model->update($id, $firstname, $lastname, $gender, $trivia, $quotes, $birthdate, $deathdate, $birthname, $minibiography, $spouse, $height) == 0)
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The person \"" . $firstname . " " . $lastname . "\" was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The person \"" . $firstname . " " . $lastname . "\" was not updated");
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
        $model = $this->getModel("person");

        // Get ID
        $id = $request->getGetArg('id');

        // Gather infos
        $infos = $model->getPersonInfosGeneral($id);

        // If deletion confirmed
        if ($request->existGetArg("confirm"))
        {
            // Proceed with deletion
            if ($model->delete($id) == 0)
            {
                // Load other controller, launch action and gather back view
                $controller = ILARIA_CoreLoader::getInstance()->loadController("directaccess");
                $view = $controller->action_persons(NULL);
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The person \"" . $infos['firstname'] . " " . $infos['lastname'] . "\" was successfully deleted");
            }
            else
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $id))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The person \"" . $infos['firstname'] . " " . $infos['lastname'] . "\" failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>The person \\\"" . $infos['firstname'] . " " . $infos['lastname'] . "\\\" will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("person", "delete", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }

    public function action_insertaltname($request)
    {
        // Instanciate model
        $model = $this->getModel("alternativename");

        // Get person ID
        $personId = $request->getGetArg('person_id');

        // Instanciate view
        $view = $this->getView("personaltnameform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PERSONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_ALTNAME_NAME . "_dataid"))
        {
            // Gather values
            $firstname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_ALTNAME_NAME . "_firstname"));
            $lastname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_ALTNAME_NAME . "_lastname"));

            // Insert values
            if ($model->insert($firstname, $lastname, $personId) == 0)
            {
                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $personId))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The alternative name \"" . $firstname . " " . $lastname . "\" was successfully inserted");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The alternative name \"" . $firstname . " " . $lastname . "\" was not inserted");
            }
        }

        // Prepare form view
        $view->prepare(array(
            'person_id' => $personId,
            'model' => $model,
            'action' => 'insertaltname',
        ));

        // Return view
        return $view;
    }

    public function action_updatealtname($request)
    {
        // Instanciate model
        $model = $this->getModel("alternativename");

        // Instanciate view
        $view = $this->getView("personaltnameform");
        $view->setTemplateName('frontend');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PERSONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Get ID if set
        if ($request->existGetArg('id'))
        {
            $id = $request->getGetArg('id');
        }

        // If form has been posted
        if ($request->existPostArg("f_" . self::FORM_ALTNAME_NAME . "_dataid"))
        {
            // Gather values
            $id = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_ALTNAME_NAME . "_dataid"));
            $firstname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_ALTNAME_NAME . "_firstname"));
            $lastname = ILARIA_SecurityManager::in($request->getPostArg("f_" . self::FORM_ALTNAME_NAME . "_lastname"));

            // Update values
            if ($model->update($id, $firstname, $lastname) == 0)
            {
                // Get person ID
                $personId = $model->getAlternativeNameInfos($id)['person_id'];

                // Load other controller, launch action and gather back view
                $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $personId))));
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The alternative name \"" . $firstname . " " . $lastname . "\" was successfully updated");
                return $view;
            }
            else
            {
                // Register error
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The alternative name \"" . $firstname . " " . $lastname . "\" was not updated");
            }
        }

        // Get person ID
        $personId = $model->getAlternativeNameInfos($id)['person_id'];

        // Prepare form view
        $view->prepare(array(
            'person_id' => $personId,
            'model' => $model,
            'action' => 'updatealtname',
            'id' => $id,
        ));

        // Return view
        return $view;
    }

    public function action_deletealtname($request)
    {
        // Instanciate model
        $model = $this->getModel("alternativename");

        // Get ID
        $id = $request->getGetArg('id');

        // Gather infos
        $infos = $model->getAlternativeNameInfos($id);

        // If deletion confirmed
        if ($request->existGetArg("confirm"))
        {
            // Load other controller, launch action and gather back view
            $view = $this->action_details(new ILARIA_CoreRequest(ILARIA_ConfigurationGlobal::buildRequestChain("person", "details", array('id' => $infos['person_id']))));

            // Proceed with deletion
            if ($model->delete($id) == 0)
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_SUCCESS, "The alternative name \"" . $infos['firstname'] . " " . $infos['lastname'] . "\" was successfully deleted");
            }
            else
            {
                $view->addAlert(ILARIA_ApplicationView::ALERT_TYPE_DANGER, "The alternative name \"" . $infos['firstname'] . " " . $infos['lastname'] . "\" failed to be deleted");
            }

            // Return view
            return $view;
        }

        // If deletion not confirmed
        else
        {
            // Create view
            $view = "<p>The alternative name \\\"" . $infos['firstname'] . " " . $infos['lastname'] . "\\\" will be deleted. Are you sure ?</p>";

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
                        ILARIA_ApplicationAsynchronous::MODAL_BUTTON_LINK => ILARIA_ConfigurationGlobal::buildRequestChain("person", "deletealtname", array(
                            'id' => $id,
                            'confirm' => 'confirm',
                        )),
                    ),
                ),
            ));
        }
    }
}