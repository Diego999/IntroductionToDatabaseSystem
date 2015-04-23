<?php

class DirectaccessproductionsView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Begin box
        $this->output("<div class=\"col-md-4 col-md-offset-4\">");

        // Title
        $this->output("<h3>Productions</h3>");

        // Table with infos
        $this->output("<table class=\"table\">");
        $this->output("<tr><th>Kind</th><th>Count</th><th></th></tr>");
        $this->output("<tr><td>Movies</td><td>" . $data['stats']['count_single'] . "</td><td><a class=\"btn btn-danger btn-sm\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("singleproduction", "insert", array()) . "\" role=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> insert</a></td></tr>");
        $this->output("<tr><td>Series</td><td>" . $data['stats']['count_serie'] . "</td><td><a class=\"btn btn-danger btn-sm\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("serie", "insert", array()) . "\" role=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> insert</a></td></tr>");
        $this->output("<tr><td>Episodes</td><td>" . $data['stats']['count_episode'] . "</td><td>go to its serie's details page to add a new episode</td></tr>");
        $this->output("</table>");

        // End box
        $this->output("</div>");

        // Right margin
        $this->output("</div>");
    }
}