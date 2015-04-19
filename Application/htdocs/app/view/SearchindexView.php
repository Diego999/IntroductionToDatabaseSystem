<?php

class SearchindexView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        // Left margin
        $this->output("<div class=\"row row-pad-top-20\">");
        $this->output("<div class=\"col-md-3\"></div>");

        // Begin accordion component
        $this->output("<div class=\"col-md-6\">");
        $this->output("<div class=\"panel-group\" id=\"acc-search-options\" role=\"tablist\" aria-multiselectable=\"true\">");

        // First accordion option : simple search
        $this->output("<div class=\"panel panel-default\">");

        // Title
        $this->output("<div class=\"panel-heading\" role=\"tab\" id=\"acc-search-simple-title\">");
        $this->output("<h4 class=\"panel-title\">");
        $this->output("<a data-toggle=\"collapse\" data-parent=\"#acc-search-options\" href=\"#acc-search-simple-content\" aria-expanded=\"true\" aria-controls=\"acc-search-simple-content\">");
        $this->output("Simple search");
        $this->output("</a>");
        $this->output("</h4>");
        $this->output("</div>");

        // Start of content
        $this->output("<div id=\"acc-search-simple-content\" class=\"panel-collapse collapse in\" role=\"tabpanel\" aria-labelledby=\"acc-search-simple-title\">");
        $this->output("<div class=\"panel-body\">");

        // Form
        $this->output("<form class=\"form-horizontal\" action=\"" . ILARIA_ConfigurationGlobal::buildRequestChain('search', 'result', array()) . "\" method=\"post\">");
        $this->output("<div class=\"form-group\">");
        $this->output("<label for=\"input-value\" class=\"col-sm-3 control-label\">Search for</label>");
        $this->output("<div class=\"col-sm-6\">");
        $this->output("<input type=\"text\" class=\"form-control\" id=\"input-value\" name=\"input-value\" placeholder=\"Text to search\" />");
        $this->output("</div>");
        $this->output("<div class=\"col-sm-3\">");
        $this->output("<input type=\"hidden\" id=\"input-simple\" name=\"input-simple\" value=\"simple\" />");
        $this->output("<button type=\"submit\" class=\"btn btn-default\">Search</button>");
        $this->output("</div>");
        $this->output("</div>");
        $this->output("</form>");

        // End of content
        $this->output("</div>");
        $this->output("</div>");

        // End of first accordion option
        $this->output("</div>");

        // Second accordion option : simple search
        $this->output("<div class=\"panel panel-default\">");

        // Title
        $this->output("<div class=\"panel-heading\" role=\"tab\" id=\"acc-search-advanced-title\">");
        $this->output("<h4 class=\"panel-title\">");
        $this->output("<a class=\"collapsed\" data-toggle=\"collapse\" data-parent=\"#acc-search-options\" href=\"#acc-search-advanced-content\" aria-expanded=\"false\" aria-controls=\"acc-search-advanced-content\">");
        $this->output("Advanced search");
        $this->output("</a>");
        $this->output("</h4>");
        $this->output("</div>");

        // Start of content
        $this->output("<div id=\"acc-search-advanced-content\" class=\"panel-collapse collapse\" role=\"tabpanel\" aria-labelledby=\"acc-search-advanced-title\">");
        $this->output("<div class=\"panel-body\">");

        // Form
        $this->output("<form class=\"form-horizontal\" action=\"" . ILARIA_ConfigurationGlobal::buildRequestChain('search', 'result', array()) . "\" method=\"post\">");
        $this->output("<div class=\"form-group\">");
        $this->output("<label for=\"input-value\" class=\"col-sm-3 control-label\">Search for</label>");
        $this->output("<div class=\"col-sm-6\">");
        $this->output("<input type=\"text\" class=\"form-control\" id=\"input-value\" name=\"input-value\" placeholder=\"Text to search\" />");
        $this->output("</div>");
        $this->output("</div>");
        $this->output("<div class=\"form-group\">");
        $this->output("<label class=\"col-sm-3 control-label\">Search in</label>");
        $this->output("<div class=\"col-sm-6\">");
        $this->output("<table>");
        $this->output("<tr><td><input type=\"checkbox\" id=\"input-search-productions\" name=\"input-search-productions\" value=\"productions\" /></td><td style=\"padding-left:10px\">productions</td></tr>");
        $this->output("<tr><td><input type=\"checkbox\" id=\"input-search-persons\" name=\"input-search-persons\" value=\"persons\" /></td><td style=\"padding-left:10px\">persons</td></tr>");
        $this->output("<tr><td><input type=\"checkbox\" id=\"input-search-characters\" name=\"input-search-characters\" value=\"characters\" /></td><td style=\"padding-left:10px\">characters</td></tr>");
        $this->output("<tr><td><input type=\"checkbox\" id=\"input-search-companies\" name=\"input-search-companies\" value=\"companies\" /></td><td style=\"padding-left:10px\">companies</td></tr>");
        $this->output("<tr><td><input type=\"checkbox\" id=\"input-search-genders\" name=\"input-search-genders\" value=\"genders\" /></td><td style=\"padding-left:10px\">genres</td></tr>");
        $this->output("</table>");
        $this->output("</div>");
        $this->output("</div>");
        $this->output("<div class=\"form-group\">");
        $this->output("<div class=\"col-sm-3 col-sm-offset-3\">");
        $this->output("<button type=\"submit\" class=\"btn btn-default\">Search</button>");
        $this->output("</div>");
        $this->output("</div>");
        $this->output("</form>");

        // End of content
        $this->output("</div>");
        $this->output("</div>");

        // End of second accordion option
        $this->output("</div>");

        // End accordion component
        $this->output("</div>");
        $this->output("</div>");

        // Right margin
        $this->output("<div class=\"col-md-3\"></div>");
        $this->output("</div>");
    }
}