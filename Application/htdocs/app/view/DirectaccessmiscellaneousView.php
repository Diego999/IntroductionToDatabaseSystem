<?php

class DirectaccessmiscellaneousView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Begin first column
        $this->output("<div class=\"col-md-3\">");

        // Roles
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncroles']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncroles']->getStructure(array()));
        $this->output("</div>");

        // Begin second column
        $this->output("</div>");
        $this->output("<div class=\"col-md-3\">");

        // Genders
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asyncgenders']->getPaginator());
        $this->output("</div>");
        $this->output($data['asyncgenders']->getStructure(array()));
        $this->output("</div>");

        // Begin third column
        $this->output("</div>");
        $this->output("<div class=\"col-md-3\">");

        // Types
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asynctypes']->getPaginator());
        $this->output("</div>");
        $this->output($data['asynctypes']->getStructure(array()));
        $this->output("</div>");

        // Begin fourth column
        $this->output("</div>");
        $this->output("<div class=\"col-md-3\">");

        // Kinds
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['asynckinds']->getPaginator());
        $this->output("</div>");
        $this->output($data['asynckinds']->getStructure(array()));
        $this->output("</div>");

        // End fourth column
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}