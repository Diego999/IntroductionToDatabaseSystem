<?php

class FormbuilderController extends ILARIA_ApplicationController implements ILARIA_ModuleFormbuilderAjaxController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'ajaxunique':
                return true;
            default:
                return false;
        }
    }

    public function action_ajaxunique($request)
    {
        // Gather things
        $value = $request->getPostArg('value');
        $table = $request->getPostArg('table');
        $field = $request->getPostArg('field');
        $ignore = ($request->getPostArg('currentid') ? $request->getPostArg('currentid') : "0");

        // Instanciate model
        $model = $this->getModel("formbuilder");

        // Check uniqueness
        $isUnique = $model->checkUnique($table, $field, $value, $ignore);

        // Return value in view
        $view = $this->getView("formbuilderajaxunique");
        $view->setTemplateName('ajax');
        $view->prepare(array(
            'isunique' => ($isUnique ? 1 : 0),
        ));
        return $view;
    }
}