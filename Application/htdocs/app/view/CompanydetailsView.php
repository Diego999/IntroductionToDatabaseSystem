<?php

class CompanydetailsView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Company name
        $this->output("<div class=\"col-md-10 col-md-offset-1\">");
        $this->output("<h2>" . $data['infos']['name'] . "</h2>");
        $this->output("</div>");

        // Begin left panel
        $this->output("<div class=\"col-md-3 col-md-offset-1\">");

        // Basic infos tab
        $this->output("<table class=\"table table-striped\">");
        $this->output("<tr><td>Country</td><td>" . ($data['infos']['country'] ? $data['infos']['country'] : "-") . "</td></tr>");
        $this->output("</table>");

        // Begin right panel
        $this->output("</div>");
        $this->output("<div class=\"col-md-7\">");

        // List of single productions
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncworksingle']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncworksingle']->getStructure(array(
            'company_id' => $data['infos']['id'],
        )));
        $this->output("</div>");

        // List of series productions
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncworkseries']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncworkseries']->getStructure(array(
            'company_id' => $data['infos']['id'],
        )));
        $this->output("</div>");

        // End of right panel
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}