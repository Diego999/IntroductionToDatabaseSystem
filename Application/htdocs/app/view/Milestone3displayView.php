<?php

class Milestone3displayView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Begin main panel
        $this->output("<div class=\"col-md-8 col-md-offset-2\">");

        // Title
        $this->output("<h2>Search results for query " . $data['query'] . "</h2>");

        // Result list
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output($data['async']->getPaginator());
        $this->output("</div>");
        $this->output($data['async']->getStructure($data['params']));
        $this->output("</div>");

        // End main panel
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}