<?php

class SearchController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'index':
                return true;
            case 'result':
                return true;
            case 'charactersbase':
                return true;
            case 'characteractors':
                return true;
            case 'charactermovies':
                return true;
            default:
                return false;
        }
    }

    public function action_index($request)
    {
        // Create view
        $view = $this->getView('searchindex');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_SEARCH);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Output to view
        $view->prepare(array());

        // Return view
        return $view;
    }

    public function action_result($request)
    {
        // Create view
        $view = $this->getView('searchresult');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_SEARCH);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Load asynchronous module
        $asyncCharacters = $this->getAsynchronous("searchcharactersbase");

        // Output to view
        $view->prepare(array(
            'search' => $request->getPostArg('input-value'),
            'charactersbase' => $asyncCharacters
        ));

        // Return view
        return $view;
    }

    public function action_charactersbase($request)
    {
        $params = array();
        if ($request->existGetArg('name'))
        {
            $params['name'] = $request->getGetArg('name');
        }
        return $this->getAsynchronous("searchcharactersbase")->getContent($params);
    }

    public function action_characteractors($request)
    {
        // Get model
        $model = $this->getModel("search");

        // Gather list of actors
        $actors = $model->getActorsPlayingCharacter($request->getGetArg('id'));

        // Deal with error
        if (!is_array($actors))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the actors",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        // Create view
        $view = "<table class=\\\"table table-striped table-condensed\\\">";
        $view .= "<tr><th>Last name</th><th>First name</th><th></th></tr>";
        foreach ($actors as $actor)
        {
            $view .= "<tr><td>" . $actor['lastname'] . "</td><td>" . $actor['firstname'] . "</td><td><a class=\\\"btn btn-default\\\" href=\\\"" . ILARIA_ConfigurationGlobal::buildRequestChain("person","details",array("id" => $actor['id'])) . "\\\" role=\\\"button\\\"><span class=\\\"glyphicon glyphicon-arrow-right\\\" aria-hidden=\\\"true\\\"></span> see details</a></td></tr>";
        }
        $view .= "</table>";

        // Create title
        $title = "Actors who played the role of " . $model->getCharacterName($request->getGetArg('id'));

        return ILARIA_ApplicationAsynchronous::buildModalAjaxResponse(array(
            ILARIA_ApplicationAsynchronous::MODAL_TITLE => $title,
            ILARIA_ApplicationAsynchronous::MODAL_CONTENT => $view,
            ILARIA_ApplicationAsynchronous::MODAL_BUTTONS => array(
                array(
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_STYLE => "default",
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_TITLE => "Close",
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_ACTION => ILARIA_ApplicationAsynchronous::MODAL_ACTION_DISMISS,
                ),
            ),
        ));
    }

    public function action_charactermovies($request)
    {
        // Get model
        $model = $this->getModel("search");

        // Gather list of movies
        $movies = $model->getMoviesContainingCharacter($request->getGetArg('id'));

        // Deal with error
        if (!is_array($movies))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the movies",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        // Create view
        $view = "<table class=\\\"table table-striped table-condensed\\\">";
        $view .= "<tr><th>Title</th><th>Year</th><th></th></tr>";
        foreach ($movies as $movie)
        {
            $view .= "<tr><td>" . $movie['title'] . "</td><td>" . ($movie['year'] ? $movie['year'] : '?') . "</td><td><a class=\\\"btn btn-default\\\" href=\\\"" . ILARIA_ConfigurationGlobal::buildRequestChain("production","details",array("id" => $movie['id'])) . "\\\" role=\\\"button\\\"><span class=\\\"glyphicon glyphicon-arrow-right\\\" aria-hidden=\\\"true\\\"></span> see details</a></td></tr>";
        }
        $view .= "</table>";

        // Create title
        $title = "Movies in which " . $model->getCharacterName($request->getGetArg('id')) . " appears";

        return ILARIA_ApplicationAsynchronous::buildModalAjaxResponse(array(
            ILARIA_ApplicationAsynchronous::MODAL_TITLE => $title,
            ILARIA_ApplicationAsynchronous::MODAL_CONTENT => $view,
            ILARIA_ApplicationAsynchronous::MODAL_BUTTONS => array(
                array(
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_STYLE => "default",
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_TITLE => "Close",
                    ILARIA_ApplicationAsynchronous::MODAL_BUTTON_ACTION => ILARIA_ApplicationAsynchronous::MODAL_ACTION_DISMISS,
                ),
            ),
        ));
    }

}
