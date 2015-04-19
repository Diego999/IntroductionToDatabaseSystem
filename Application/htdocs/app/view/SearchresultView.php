<?php

class SearchresultView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");
        $this->output("<div class=\"col-md-1\"></div>");

        // Begin main panel
        $this->output("<div class=\"col-md-10\">");

        // Title
        $this->output("<h2>Search results for \"" . $data['search'] . "\"</h2>");

        // Characters list
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['charactersbase']->getPaginator());
        $this->output("</div>");
        $this->output($data['charactersbase']->getStructure(array('name' => $data['search'])));
        $this->output("</div>");

        // Companies list
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['companiesbase']->getPaginator());
        $this->output("</div>");
        $this->output($data['companiesbase']->getStructure(array('name' => $data['search'])));
        $this->output("</div>");

        // Genders list
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['gendersbase']->getPaginator());
        $this->output("</div>");
        $this->output($data['gendersbase']->getStructure(array('name' => $data['search'])));
        $this->output("</div>");

        // Persons list
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['personsbase']->getPaginator());
        $this->output("</div>");
        $this->output($data['personsbase']->getStructure(array('name' => $data['search'])));
        $this->output("</div>");

        // End main panel
        $this->output("</div>");

        // Right margin
        $this->output("<div class=\"col-md-1\"></div>");
        $this->output("</div>");
    }
}