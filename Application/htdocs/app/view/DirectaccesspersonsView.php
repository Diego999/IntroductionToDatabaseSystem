<?php

class DirectaccesspersonsView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Begin box
        $this->output("<div class=\"col-md-4 col-md-offset-4\">");

        // Table with infos
        $this->output("<table class=\"table\">");
        $this->output("<tr><th>Count</th><th></th></tr>");
        $this->output("<tr><td>" . $data['stats']['count_person'] . "</td><td><a class=\"btn btn-danger btn-sm\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("person", "insert", array()) . "\" role=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> insert</a></td></tr>");
        $this->output("</table>");

        // End box
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}