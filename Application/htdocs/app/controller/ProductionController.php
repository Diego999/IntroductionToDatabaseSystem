<?php

class ProductionController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'details':
                return true;
            case 'asyncalttitles':
                return true;
            case 'asynccasting':
                return true;
            case 'asynccompanies':
                return true;
            case 'asyncserieseasons':
                return true;
            case 'seasonepisodes':
                return true;
            default:
                return false;
        }
    }

    public function action_details($request)
    {
        // Create view
        $view = $this->getView('productiondetails');

        // Create menu
        $menu = $this->getMenu('main');
        $menu->setActiveEntry(MainMenu::ENTRY_PRODUCTIONS);
        ILARIA_ApplicationMenu::registerMenu(MainMenu::MAIN_MENU_KEY, $menu);

        // Define template
        $view->setTemplateName('frontend');

        // Create model
        $model = $this->getModel('production');

        // Gather production cardinality
        $cardinality = $model->getProductionCardinality($request->getGetArg('id'));

        // Load asynchronous general modules
        $asyncAlternativeTitles = $this->getAsynchronous("productionalternativetitles");
        $asyncCasting = $this->getAsynchronous("productioncasting");
        $asyncCompanies = $this->getAsynchronous("productioncompanies");

        // Prepare array for the view
        $viewParams = array(
            'cardinality' => $cardinality,
            'asyncalttitles' => $asyncAlternativeTitles,
            'asynccasting' => $asyncCasting,
            'asynccompanies' => $asyncCompanies,
        );

        // Act depending on the cardinality of production
        switch ($cardinality)
        {
            case ProductionModel::CARD_SINGLE:

                // Gather single production base infos
                $infos = $model->getSingleInfosGeneral($request->getGetArg('id'));

                // Add infos to view params
                $viewParams['infos'] = $infos;

                break;

            case ProductionModel::CARD_SERIE:

                // Gather serie production base infos
                $infos = $model->getSerieInfosGeneral($request->getGetArg('id'));

                // Load asynchronous module for seasons
                $asyncSeasons = $this->getAsynchronous("productionserieseasons");

                // Add infos to view params
                $viewParams['infos'] = $infos;
                $viewParams['asyncseasons'] = $asyncSeasons;

                break;

            case ProductionModel::CARD_EPISODE:

                // Gather episode production base infos
                $infos = $model->getEpisodeInfosGeneral($request->getGetArg('id'));

                // Add infos to view params
                $viewParams['infos'] = $infos;

                break;

            default:
                break;
        }

        // Output to view
        $view->prepare($viewParams);

        // Return view
        return $view;
    }

    public function action_asyncalttitles($request)
    {
        $params = array(
            'prod_id' => $request->getGetArg('prod_id'),
            'maintitle_id' => $request->getGetArg('maintitle_id'),
        );
        return $this->getAsynchronous("productionalternativetitles")->getContent($params);
    }

    public function action_asynccasting($request)
    {
        $params = array(
            'prod_id' => $request->getGetArg('prod_id'),
        );
        return $this->getAsynchronous("productioncasting")->getContent($params);
    }

    public function action_asynccompanies($request)
    {
        $params = array(
            'prod_id' => $request->getGetArg('prod_id'),
        );
        return $this->getAsynchronous("productioncompanies")->getContent($params);
    }

    public function action_asyncserieseasons($request)
    {
        $params = array(
            'prod_id' => $request->getGetArg('prod_id'),
        );
        return $this->getAsynchronous("productionserieseasons")->getContent($params);
    }

    public function action_seasonepisodes($request)
    {
        // Get model
        $model = $this->getModel("production");

        // Gather season infos
        $infos = $model->getSeasonInfos($request->getGetArg('season_id'));

        // Gather list of episodes
        $episodes = $model->getEpisodesList($request->getGetArg('season_id'));

        // Deal with error
        if (!is_array($episodes))
        {
            throw new ILARIA_CoreError("An error occurred while searching through episodes",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        // Create view
        $view = "<table class=\\\"table table-striped table-condensed\\\">";
        $view .= "<tr><th>Number</th><th>Title</th><th></th></tr>";
        foreach ($episodes as $episode)
        {
            $view .= "<tr><td>" . $episode['episode_number'] . "</td><td>" . $episode['episode_title'] . "</td><td><a class=\\\"btn btn-default\\\" href=\\\"" . ILARIA_ConfigurationGlobal::buildRequestChain("production","details",array("id" => $episode['episode_id'])) . "\\\" role=\\\"button\\\"><span class=\\\"glyphicon glyphicon-arrow-right\\\" aria-hidden=\\\"true\\\"></span> see details</a></td></tr>";
        }
        $view .= "</table>";

        // Create title
        $title = "Episodes in season " . $infos['season_number'] . " of serie " . $infos['serie_title'];

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