<?php

class ProductiondetailsView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Things that can be determined based on cardinality
        switch ($data['cardinality'])
        {
            case ProductionModel::CARD_SINGLE:
                $keyTitle = 'prod_title';
                $keyGender = 'prod_gender';
                $year = $data['infos']['prod_year'];
                break;
            case ProductionModel::CARD_SERIE:
                $keyTitle = 'serie_title';
                $keyGender = 'serie_gender';
                $data['infos']['prod_kind'] = 'serie';
                $year = ($data['infos']['serie_yearstart'] ? $data['infos']['serie_yearstart'] : "?") . "-" . ($data['infos']['serie_yearend'] ? $data['infos']['serie_yearend'] : "?");
                break;
            case ProductionModel::CARD_EPISODE:
                $keyTitle = 'episode_title';
                $keyGender = 'serie_gender';
                $data['infos']['prod_kind'] = 'episode';
                $year = $data['infos']['episode_year'];
                break;
            default:
                break;
        }

        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Production title
        $this->output("<div class=\"col-md-10 col-md-offset-1\">");
        $this->output("<h2>" . $data['infos'][$keyTitle] . "</h2>");
        $this->output("</div>");

        // Begin left panel
        $this->output("<div class=\"col-md-7 col-md-offset-1\">");

        // Casting
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asynccasting']->getPaginator());
        $this->output("</div>");
        $this->output($data['asynccasting']->getStructure(array(
            'prod_id' => $data['infos']['prod_id'],
        )));
        $this->output("</div>");

        // Companies
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asynccompanies']->getPaginator());
        $this->output("</div>");
        $this->output($data['asynccompanies']->getStructure(array(
            'prod_id' => $data['infos']['prod_id'],
        )));
        $this->output("</div>");

        // Begin right panel
        $this->output("</div>");
        $this->output("<div class=\"col-md-3\">");

        // Basic infos tab
        $this->output("<table class=\"table table-striped\">");
        $this->output("<tr><td>Kind</td><td>" . $data['infos']['prod_kind'] . "</td></tr>");
        $this->output("<tr><td>Year</td><td>" . $year . "</td></tr>");
        $this->output("<tr><td>Genre</td><td>" . $data['infos'][$keyGender] . "</td></tr>");
        switch ($data['cardinality'])
        {
            case ProductionModel::CARD_SINGLE:
                break;
            case ProductionModel::CARD_SERIE:
                $this->output("<tr><td>Seasons</td><td>" . $data['infos']['season_count'] . "</td></tr>");
                $this->output("<tr><td>Episodes</td><td>" . $data['infos']['episode_count'] . "</td></tr>");
                break;
            case ProductionModel::CARD_EPISODE:
                $this->output("<tr><td>Serie</td><td>" . $data['infos']['serie_title'] . " <a class=\"btn btn-primary btn-xs\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("production", "details", array('id' => $data['infos']['serie_id'])) . "\" role=\"button\"><span class=\"glyphicon glyphicon-arrow-right\" aria-hidden=\"true\"></span></a></td></tr>");
                $this->output("<tr><td>Season</td><td>" . $data['infos']['season_number'] . "</td></tr>");
                $this->output("<tr><td>Episode</td><td>" . $data['infos']['episode_number'] . "</td></tr>");
                break;
            default:
                break;
        }
        $this->output("</table>");

        // Alternative titles
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncalttitles']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncalttitles']->getStructure(array(
            'prod_id' => $data['infos']['prod_id'],
            'maintitle_id' => $data['infos']['maintitle_id'],
        )));
        $this->output("</div>");

        // Seasons list if any
        if ($data['cardinality'] == ProductionModel::CARD_SERIE)
        {
            $this->output("<div class=\"panel panel-default\">");
            $this->output("<div class=\"panel-heading\">");
            $this->output($data['asyncseasons']->getPaginator());
            $this->output("</div>");
            $this->output($data['asyncseasons']->getStructure(array(
                'prod_id' => $data['infos']['prod_id'],
            )));
            $this->output("</div>");
        }

        // End right panel
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}