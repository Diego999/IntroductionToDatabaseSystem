<?php

class Milestone2indexView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Begin main panel
        $this->output("<div class=\"col-md-6 col-md-offset-3\">");

        // Title
        $this->output("<h2>Milestone 2 queries</h2>");

        // Query A
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query A</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the number of movies per year. Make sure to include tv and video movies.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'a')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query B
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query B</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the ten countries with most production companies.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'b')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query C
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query C</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the min, max and average career duration. (A career length is implied by the first and last production of a person)</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'c')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query D
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query D</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the min, max and average number of actors in a production.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'd')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query E
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query E</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the min, max and average height of female persons.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'e')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query F
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query F</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>List all pairs of persons and movies where the person has both directed the movie and acted in the movie. Do not include tv and video movies.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'f')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query G
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query G</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>List the three more popular character names.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone2", "display", array('q' => 'g')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // End main panel
        $this->output(("</div>"));

        // Right margin
        $this->output(("</div>"));
    }
}