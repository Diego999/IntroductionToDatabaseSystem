<?php

abstract class ILARIA_ApplicationAsynchronous
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    const PAGINATION_SIZE = 20;

    const MODAL_TITLE = '777ded5911632c4d54f98899f34b81bcaff1ce8c';
    const MODAL_CONTENT = '02cad44d0118827d6ecef22103bde1389543bf41';
    const MODAL_BUTTONS = '34fe3361c6e59470dec59554d09ad81747f44438';
    const MODAL_LINK = '84381582366b1334fb80159a5599b517b6ffc681';
    const MODAL_BUTTON_STYLE = 'b8a0a0df644c06a8cda0a242dc9033e517e1adde';
    const MODAL_BUTTON_ACTION = '74b2935c93c80d5f1eb901823870cb2b07582ce1';
    const MODAL_BUTTON_TITLE = '4f8c0399dda57b7b8d072a547ec051cd52d4238c';
    const MODAL_BUTTON_LINK = 'a1b26b8fdc78171da71ed738bd391d82ec298e1b';
    const MODAL_ACTION_SUBMIT = '52d3bb8d34a13b50ff858ee2489351d74fd03824';
    const MODAL_ACTION_RESET = 'a62f6695b78c8d5b1658ee6a7a04c6430845297b';
    const MODAL_ACTION_DISMISS = '19259c73fe89e01119a6592c5505e3f8afa132d5';
    const MODAL_ACTION_LINK = '32e301c16c04135b7673e164fe3140c419732773';

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

    public static function buildModalStructure()
    {
        // Build HTML visual structure
        $result = self::buildModalHtml();

        // Build javascript content
        $result .= self::buildModalScript();

        return $result;
    }

    public static function getModalOnClickShow($url, $doublequote=false)
    {
        $quote = ($doublequote ? "\\\"" : "\"");
        return "onclick=" . $quote . "modal_load_url('" . $url . "')" . $quote;
    }

    public static function buildModalAjaxResponse($content)
    {
        // object begin
        $ajaxString = '{';

        // modal title
        $ajaxString .= "\"title\":\"" . ILARIA_SecurityManager::out($content[self::MODAL_TITLE]) . "\",";

        // modal content
        $ajaxString .= "\"content\":\"" . $content[self::MODAL_CONTENT] . "\",";

        // modal link
        if (isset($content[self::MODAL_LINK]))
        {
            $ajaxString .= "\"formlink\":\"" . ILARIA_SecurityManager::out($content[self::MODAL_LINK]) . "\",";
        }

        // modal buttons
        $ajaxString .= "\"buttons\":[";
        $count = 0;
        foreach ($content[self::MODAL_BUTTONS] as $button)
        {
            if ($count > 0)
            {
                $ajaxString .= ",";
            }
            switch ($button[self::MODAL_BUTTON_ACTION])
            {
                case self::MODAL_ACTION_DISMISS:
                    $buttonString = "<button type=\\\"button\\\" class=\\\"btn btn-" . $button[self::MODAL_BUTTON_STYLE] . "\\\" data-dismiss=\\\"modal\\\">" . $button[self::MODAL_BUTTON_TITLE] . "</button>";
                    break;
                case self::MODAL_ACTION_RESET:
                    $buttonString = "<input type=\\\"reset\\\" class=\\\"btn btn-" . $button[self::MODAL_BUTTON_STYLE] . "\\\" name=\\\"reset\\\" value=\\\"" . $button[self::MODAL_BUTTON_TITLE] . "\\\" />";
                    break;
                case self::MODAL_ACTION_SUBMIT:
                    $buttonString = "<input type=\\\"submit\\\" class=\\\"btn btn-" . $button[self::MODAL_BUTTON_STYLE] . "\\\" name=\\\"submit\\\" value=\\\"" . $button[self::MODAL_BUTTON_TITLE] . "\\\" />";
                    break;
                case self::MODAL_ACTION_LINK:
                    $buttonString = "<a class=\\\"btn btn-" . $button[self::MODAL_BUTTON_STYLE] . "\\\" href=\\\"" . $button[self::MODAL_BUTTON_LINK] . "\\\" role=\\\"button\\\">" . $button[self::MODAL_BUTTON_TITLE] . "</a>";
                    break;
                default:
                    $buttonString = '';
                    break;
            }
            $ajaxString .= "\"" . $buttonString . "\"";
            $count++;
        }
        $ajaxString .= "]";

        // object end
        $ajaxString .= '}';

        // Return the JSON tab in its own view
        $view = new ILARIA_ApplicationAsynchronousView();
        $view->prepare($ajaxString);
        $view->setTemplateName('ajax');
        return $view;
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
        $output[] = "$(\"#" . $this->getPaginatorNextButton() . "\").before(btnCurrent.replace(/:num/g, i).replace(/:page/g, i));";
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
        $output[] = "elemcontent = elemcontent.replace(new RegExp(\":\" + idx, 'g'),val);";
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
        $output[] = "var errorcontent = errorstruct.replace(/:error/g, data.responseText);";

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

    private static function buildModalHtml()
    {
        $html = '';
        $html .= "<div class=\"modal fade\" id=\"modal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"modallabel\" aria-hidden=\"true\">\n";
        $html .= "<div class=\"modal-dialog\">\n";
        $html .= "<div class=\"modal-content\" id=\"modal-container\">\n";
        $html .= "</div>\n";
        $html .= "</div>\n";
        $html .= "</div>\n";
        return $html;
    }

    private static function buildModalScript()
    {
        // Begin script
        $script = "<script type=\"text/javascript\">\n";

        // Structure for the modal header
        $script .= "var modal_struct_header = \"";
        $script .= "<div class=\\\"modal-header\\\">";
        $script .= "<button type=\\\"button\\\" class=\\\"close\\\" data-dismiss=\\\"modal\\\" aria-label=\\\"Close\\\">";
        $script .= "<span aria-hidden=\\\"true\\\">&times;</span>";
        $script .= "</button>";
        $script .= "<h4 class=\\\"modal-title\\\">:title</h4>";
        $script .= "</div>";
        $script .= "\";\n";

        // Structure for modal body
        $script .= "var modal_struct_body = \"";
        $script .= "<div class=\\\"modal-body\\\">";
        $script .= ":content";
        $script .= "</div>";
        $script .= "\";\n";

        // Structure for modal footer
        $script .= "var modal_struct_footer =\"";
        $script .= "<div class=\\\"modal-footer\\\">";
        $script .= ":content";
        $script .= "</div>";
        $script .= "\";\n";

        // Structure for form opening
        $script .= "var modal_struct_form_open = \"";
        $script .= "<form method=\\\"post\\\" action=\\\":action\\\" enctype=\\\"multipart/form-data\\\">";
        $script .= "\";\n";

        // Structure for form closing
        $script .= "var modal_struct_form_close = \"";
        $script .= "</form>";
        $script .= "\";\n";

        // Function for building a header block
        $script .= "function modal_build_header(title) {\n";
        $script .= "return modal_struct_header.replace(/:title/g, title);\n";
        $script .= "}\n";

        // Function for building a body block
        $script .= "function modal_build_body(content) {\n";
        $script .= "return modal_struct_body.replace(/:content/g, content);\n";
        $script .= "}\n";

        // Function for building a footer block
        $script .= "function modal_build_footer(content) {\n";
        $script .= "return modal_struct_footer.replace(/:content/g, content);\n";
        $script .= "}\n";

        // Function for building a form opening
        $script .= "function modal_build_form_open(action) {\n";
        $script .= "return modal_struct_form_open.replace(/:action/g, action);\n";
        $script .= "}\n";

        // Function for loading url into the modal window
        $script .= "function modal_load_url(url) {\n";

        // Clear the modal window and put loading logo
        $script .= "$(\"#modal-container\").empty();\n";
        $script .= "var loadingContent = \"\";\n";
        $script .= "loadingContent += modal_build_header(\"loading...\");\n";
        $script .= "loadingContent += modal_build_body(\"<img src=\\\"" . ILARIA_CoreLoader::getInstance()->includeAsset("ajax/ajax_loading.gif") . "\\\" alt=\\\"loading...\\\" />\");\n";
        $script .= "loadingContent += modal_build_footer(\"\");\n";
        $script .= "$(\"#modal-container\").html(loadingContent);\n";

        // Show the modal window
        $script .= "$(\"#modal\").modal('show');\n";

        // Call the AJAX loading
        $script .= "$.getJSON(url, function(data) {\n";

        // Create the new content
        $script .= "var newContent = \"\";\n";

        // Add form if needed
        $script .= "if ('formlink' in data) {\n";
        $script .= "newContent += modal_build_form_open(data.formlink);\n";
        $script .= "}\n";

        // Prepare the header with title
        $script .= "newContent += modal_build_header(data.title);\n";

        // Prepare the content
        $script .= "newContent += modal_build_body(data.content);\n";

        // Prepare the footer
        $script .= "var footerContent = \"\";\n";
        $script .= "$.each(data.buttons, function(idx,val) {\n";
        $script .= "footerContent += val;\n";
        $script .= "});\n";
        $script .= "newContent += modal_build_footer(footerContent);\n";

        // Close form if needed
        $script .= "if ('formlink' in data) {\n";
        $script .= "newContent += modal_struct_form_close;\n";
        $script .= "}\n";

        // Clear the modal window
        $script .= "$(\"#modal-container\").empty();\n";

        // Fill the modal window
        $script .= "$(\"#modal-container\").html(newContent);\n";

        // Show the modal window
        $script .= "$(\"#modal\").modal('show');\n";

        // Ajax loading error
        $script .= "});\n";

        // End of function modal_load_url(url)
        $script .= "}\n";

        // End script
        $script .= "</script>\n";

        // Return script
        return $script;
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