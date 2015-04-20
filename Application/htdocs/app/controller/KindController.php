<?php

class KindController extends ILARIA_ApplicationController
{
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

    }

    public function action_update($request)
    {

    }

    public function action_delete($request)
    {

    }
}