<?php

class Milestone3indexView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");

        // Begin main panel
        $this->output("<div class=\"col-md-6 col-md-offset-3\">");

        // Title
        $this->output("<h2>Milestone 3 queries</h2>");

        // Query A
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query A</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Find the actors and actresses (and report the productions) who played in a production where they were 55 or more year older than the youngest actor/actress playing.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'a')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query B
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query B</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Given an actor, compute his most productive year.</p>");
        $this->output("<p>This query can be launched directly from an actor's page.</p>");
        $this->output("<form class=\"form-inline\" action=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'b')) . "\" method=\"post\">");
        $this->output("<div class=\"form-group\">");
        $this->output("<input type=\"text\" class=\"form-control\" id=\"actorid\" name=\"actorid\" placeholder=\"Actor ID\" />");
        $this->output("</div>");
        $this->output("<button class=\"btn btn-primary\" type=\"submit\">Execute</button>");
        $this->output("</form>");
        $this->output("</div>");
        $this->output("</div>");

        // Query C
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query C</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Given a year, list the company with the highest number of productions in each genre.</p>");
        $this->output("<form class=\"form-inline\" action=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'c')) . "\" method=\"post\">");
        $this->output("<div class=\"form-group\">");
        $this->output("<input type=\"text\" class=\"form-control\" id=\"year\" name=\"year\" placeholder=\"Year\" />");
        $this->output("</div>");
        $this->output("<button class=\"btn btn-primary\" type=\"submit\">Execute</button>");
        $this->output("</form>");
        $this->output("</div>");
        $this->output("</div>");

        // Query D
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query D</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute who worked with spouses/children/potential relatives on the same production.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'd')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query E
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query E</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the average number of actors per production per year.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'e')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query F
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query F</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the average number of episodes per season.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'f')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query G
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query G</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the average number of seasons per serie.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'g')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query H
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query H</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the top ten tv-series (by number of seasons).</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'h')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query I
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query I</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Compute the top ten tv-series (by number of episodes per season).</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'i')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query J
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query J</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>Find actors, actresses and directors who have movies (including tv movies and video movies) released after their death.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'j')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query K
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query K</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>For each year, show three companies that released the most movies.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'k')) . "\" role=\"button\">Execute</a>");
        $this->output("<a class=\"btn btn-danger\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'k', 'a' => 'r')) . "\" role=\"button\">Refresh (~3 minutes)</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query L
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query L</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>List all living people who are opera singers ordered from youngest to oldest.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'l')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query M
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query M</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>List 10 most ambiguous credits (pairs of people and productions) ordered by the degree of ambiguity. A credit is ambiguous if either a person has multiple alternative names or a production has multiple alternative titles. The degree of ambiguity is a product of the number of possible names (real name + all alternatives) and the number of possible titles (real + alternatives).</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'm')) . "\" role=\"button\">Execute</a>");
        $this->output("</div>");
        $this->output("</div>");

        // Query N
        $this->output("<div class=\"panel panel-default\">");
        $this->output("<div class=\"panel-heading\">");
        $this->output("<h3 class=\"panel-title\">Query N</h3>");
        $this->output("</div><div class=\"panel-body\">");
        $this->output("<p>For each country, list the most frequent character name that appears in the productions of a production company (not a distributor) from that country.</p>");
        $this->output("<a class=\"btn btn-primary\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'n')) . "\" role=\"button\">Execute</a>");
        $this->output("<a class=\"btn btn-danger\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("milestone3", "display", array('q' => 'n', 'a' => 'r')) . "\" role=\"button\">Refresh (~30 minutes)</a>");
        $this->output("</div>");
        $this->output("</div>");

        // End main panel
        $this->output(("</div>"));

        // Right margin
        $this->output(("</div>"));
    }
}