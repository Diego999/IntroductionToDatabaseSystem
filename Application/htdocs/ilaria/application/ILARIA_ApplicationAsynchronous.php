<?php

abstract class ILARIA_ApplicationAsynchronous
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    const PAGINATION_SIZE = 20;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct() {}

    public function getStructure($params = array())
    {
        return $this->buildScript($params) . "\n" . $this->buildManager() . "\n" . $this->getDisplayStructure() . "\n";
    }

    public function getPaginator()
    {
        return $this->getDisplayPaginator();
    }

    public function getContent($params = array())
    {
        try
        {
            // Get raw content
            $rawContent = $this->getRawContent($params);

            // Map each subtab to a JSON object format
            $mappedObjects = array_map(function($o) {
                $obj = "{";
                $count = 0;
                foreach ($o as $key => $val)
                {
                    $obj .= ($count == 0 ? "" : ",") . "\"" . ILARIA_SecurityManager::out($key) . "\":\"" . ILARIA_SecurityManager::out($val) . "\"";
                    $count++;
                }
                $obj .= "}";
                return $obj;
            }, $rawContent);

            // Map the main tab to a JSON tab format
            $tab = "[" . implode(",", $mappedObjects) . "]";

            // Return the JSON tab in its own view
            $view = new ILARIA_ApplicationAsynchronousView();
            $view->prepare($tab);
            return $view;
        }

        catch (ILARIA_CoreError $e)
        {
            // Return the error in its own view
            $view = new ILARIA_ApplicationAsynchronousView();
            $view->prepare($e->getMsg());
            $view->setTemplateName('ajax');
            return $view;
        }
    }

    // #################################################################################################################
    // ##                                             PROTECTED FUNCTIONS                                             ##
    // #################################################################################################################

    protected function getLoadingGif()
    {
        return "<img src=\"" . ILARIA_CoreLoader::getInstance()->includeAsset("ajax/ajax_loading.gif") . "\" alt=\"loading...\" id=\"async_" . $this->getUniqueIdentifier() . "_gif\" />";
    }

    protected function getContainerId()
    {
        return "async_" . $this->getUniqueIdentifier() . "_container";
    }

    protected function getLoadingId()
    {
        return "async_" . $this->getUniqueIdentifier() . "_loading";
    }

    protected function getElementClass()
    {
        return "async_" . $this->getUniqueIdentifier() . "_elem";
    }

    protected function getPaginatorId()
    {
        return "async_" . $this->getUniqueIdentifier() . "_paginator";
    }

    protected function getPaginatorPreviousButton()
    {
        return "async_" . $this->getUniqueIdentifier() . "_paginator_previous";
    }

    protected function getPaginatorNextButton()
    {
        return "async_" . $this->getUniqueIdentifier() . "_paginator_next";
    }

    protected function getPaginatorButtonClass()
    {
        return "async_" . $this->getUniqueIdentifier() . "_paginator_button";
    }

    protected function getPaginatorText()
    {
        return "async_" . $this->getUniqueIdentifier() . "_paginator_text";
    }

    protected function getPaginatorOnClickNumeric()
    {
        return "onclick=\\\"async_" . $this->getUniqueIdentifier() . "_display_page(:page)\\\"";
    }

    protected function getPaginatorOnClickPrevious()
    {
        return "onclick=\"async_" . $this->getUniqueIdentifier() . "_display_previous()\"";
    }

    protected function getPaginatorOnClickNext()
    {
        return "onclick=\"async_" . $this->getUniqueIdentifier() . "_display_next()\"";
    }

    protected function getModel($modelName)
    {
        try
        {
            $model = ILARIA_CoreLoader::getInstance()->loadModel($modelName);
            return $model;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $e->changeType(ILARIA_CoreError::GEN_MODEL_UNLOADABLE);
            throw $e;
        }
    }

    abstract protected function getUniqueIdentifier();
    abstract protected function getWebPath($params);
    abstract protected function getDisplayStructure();
    abstract protected function getDisplayRow();
    abstract protected function getDisplayError();
    abstract protected function getDisplayPaginator();
    abstract protected function getDisplayPaginatorButton();
    abstract protected function getDisplayPaginatorButtonActive();
    abstract protected function getRawContent($params);

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################

    private function buildScript($params)
    {
        $output = array();
        $output[] = "<script type=\"text/javascript\">";

        // async_<id>_path contains the path to the server controller and action for loading data
        $output[] = "var async_" . $this->getUniqueIdentifier() . "_path = \"" . $this->getWebPath($params) . "\";";

        // async_<id>_data contains the data once loaded by ajax
        $output[] = "var async_" . $this->getUniqueIdentifier() . "_data = undefined;";

        // function for displaying the content in a paginated way
        $output[] = "function async_" . $this->getUniqueIdentifier() . "_display_page(number) {";

        // check that the data array is not undefined
        $output[] = "if (async_" . $this->getUniqueIdentifier() . "_data !== undefined) {";

        // Gather the parent element
        $output[] = "var container = $(\"#" . $this->getContainerId() . "\");";

        // Gather the non-active button empty element
        $output[] = "var btnBasic = \"" . $this->getDisplayPaginatorButton() . "\";";

        // Gather the active button empty element
        $output[] = "var btnActive =\"" . $this->getDisplayPaginatorButtonActive() . "\";";

        // clear the current content
        $output[] = "$(\"." . $this->getElementClass() . "\").remove();";

        // Empty the paginator
        $output[] = "$(\"." . $this->getPaginatorButtonClass() . "\").remove();";

        // Compute the number of pages
        $output[] = "var pagecount = Math.ceil(async_" . $this->getUniqueIdentifier() . "_data.length/" . self::PAGINATION_SIZE . ".0);";

        // Correct input page number
        $output[] = "if (number < 1) { number = 1; }";
        $output[] = "if (number > pagecount) { number = pagecount; }";

        // compute first idx
        $output[] = "var idx_start = (number-1) * " . self::PAGINATION_SIZE . ";";

        // compute last idx
        $output[] = "var idx_end = idx_start + " . self::PAGINATION_SIZE . " - 1;";
        $output[] = "if (idx_end >= async_" . $this->getUniqueIdentifier() . "_data.length) { idx_end = async_" . $this->getUniqueIdentifier() . "_data.length - 1; }";

        // Set text
        $output[] = "var text = \"results \" + (parseInt(idx_start)+1) + \"-\" + (parseInt(idx_end)+1) + \" (total \" + async_" . $this->getUniqueIdentifier() . "_data.length + \")\";";
        $output[] = "$(\"#" . $this->getPaginatorText() . "\").html(text);";

        // Activate/deactivate previous button according to page
        $output[] = "if (number == 1) {";
        $output[] = "$(\"#" . $this->getPaginatorPreviousButton() . "\").addClass(\"disabled\");";
        $output[] = "} else {";
        $output[] = "$(\"#" . $this->getPaginatorPreviousButton() . "\").removeClass(\"disabled\");";
        $output[] = "}";

        // Activate/deactivate next button according to page
        $output[] = "if (number == pagecount) {";
        $output[] = "$(\"#" . $this->getPaginatorNextButton() . "\").addClass(\"disabled\");";
        $output[] = "} else {";
        $output[] = "$(\"#" . $this->getPaginatorNextButton() . "\").removeClass(\"disabled\");";
        $output[] = "}";

        // Create paginator numeric buttons
        $output[] = "for (var i=1; i <= pagecount; i++) {";
        $output[] = "var btnCurrent = (i == number ? btnActive : btnBasic);";
        $output[] = "$(\"#" . $this->getPaginatorNextButton() . "\").before(btnCurrent.replace(\":num\", i).replace(\":page\", i));";
        $output[] = "}";

        // Write the new page into the corresponding div
        $output[] = "$(\"#async_" . $this->getUniqueIdentifier() . "_current_page\").html(number);";

        // Build the structure of one element
        $output[] = "var elemstruct = \"" . $this->getDisplayRow() . "\";";

        // loop over the indices to display
        $output[] = "for (var i=idx_start; i<=idx_end; i++) {";

        // Build the corresponding display row
        $output[] = "var elemcontent = elemstruct;";
        $output[] = "$.each(async_" . $this->getUniqueIdentifier() . "_data[i], function(idx,val) {";
        $output[] = "elemcontent = elemcontent.replace(\":\" + idx,val);";
        $output[] = "});";

        // Add the display row to the main container
        $output[] = "container.append(elemcontent);";

        // End of element loop
        $output[] = "}";

        // end of if part
        $output[] = "}";

        // End of async_XXX_display_page(number) function
        $output[] = "}";

        // function for going to next page
        $output[] = "function async_" . $this->getUniqueIdentifier() . "_display_previous() {";
        $output[] = "var newNum = parseInt($(\"#async_" . $this->getUniqueIdentifier() . "_current_page\").html()) - 1;";
        $output[] = "async_" . $this->getUniqueIdentifier() . "_display_page(newNum);";
        $output[] = "}";

        // function for going to next page
        $output[] = "function async_" . $this->getUniqueIdentifier() . "_display_next() {";
        $output[] = "var newNum = parseInt($(\"#async_" . $this->getUniqueIdentifier() . "_current_page\").html()) + 1;";
        $output[] = "async_" . $this->getUniqueIdentifier() . "_display_page(newNum);";
        $output[] = "}";

        // function for loading the content
        $output[] = "function async_" . $this->getUniqueIdentifier() . "_load() {";

        // Gather the parent element
        $output[] = "var container = $(\"#" . $this->getContainerId() . "\");";

        // $.getJSON call for asynchronous request
        $output[] = "$.getJSON( async_" . $this->getUniqueIdentifier() . "_path, function(data) {";

        // Delete the loading part
        $output[] = "$(\"#" . $this->getLoadingId() . "\").remove();";

        // Register data globally
        $output[] = "async_" . $this->getUniqueIdentifier() . "_data = data;";

        // Make paginator visible
        $output[] = "$(\"#" . $this->getPaginatorId() . "\").css(\"visibility\", \"visible\");";

        // Call the loading for the first elements
        $output[] = "async_" . $this->getUniqueIdentifier() . "_display_page(1);";

        // end of $.getJSON => add error handler
        $output[] = "}).fail(function(data) {";

        // Delete the loading part
        $output[] = "$(\"#" . $this->getLoadingId() . "\").remove();";

        // Build the structure for displaying error
        $output[] = "var errorstruct = \"" . $this->getDisplayError() . "\";";

        // Build the corresponding display row
        $output[] = "var errorcontent = errorstruct.replace(\":error\", data.responseText);";

        // Add the display row to the main container
        $output[] = "container.append(errorcontent);";

        // end of $.getJSON fail handler
        $output[] = "});";

        // end of async_XXX_load() function
        $output[] = "}";

        // Call of the load when document is ready
        $output[] = "$(document).ready(function() { async_" . $this->getUniqueIdentifier() . "_load(); });";

        $output[] = "</script>";

        // Return inlined tab
        return implode("\n", $output);
    }

    private function buildManager()
    {
        $output = array();
        $output[] = "<div style=\"display:none\" id=\"async_" . $this->getUniqueIdentifier() . "_current_page\">0</div>";
        return implode("\n", $output);
    }

}

class ILARIA_ApplicationAsynchronousView extends ILARIA_ApplicationView
{
    public function prepare($data)
    {
        $this->output($data);
    }
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_ApplicationAsynchronous.php] class loaded');