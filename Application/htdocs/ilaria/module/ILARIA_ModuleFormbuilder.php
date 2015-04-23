<?php

class ILARIA_ModuleFormbuilder extends ILARIA_CoreModule
{
    public function __construct()
    {
        // Register services
        $this->registerService(self::SERVICE_HELPER);
    }

    public function call($key, $value)
    {
        // Nothing
    }
}

interface ILARIA_ModuleFormbuilderFieldGetterModel
{
    public function getFieldContent($id, $fieldName);
}

interface ILARIA_ModuleFormbuilderAjaxController
{
    public function action_ajaxunique($request);
}

class ILARIA_ModuleFormbuilderForm
{
    private $name = '';
    private $action = '';
    private $method = '';
    private $components = array();
    private $dataId = -1;
    private $model = NULL;
    private $ajaxController = '';

    public function __construct($name, $model, $ajaxController)
    {
        $this->name = $name;
        $this->model = $model;
        $this->ajaxController = $ajaxController;
    }

    public function getName()
    {
        return "f_" . $this->name;
    }

    public function getFieldValue($name)
    {
        if ($this->dataId < 0)
        {
            return "";
        }
        else
        {
            return $this->model->getFieldContent($this->dataId, $name);
        }
    }

    public function getOnkeypress($tableName, $fieldName, $isUnique, $canEmpty)
    {
        $jsFunction = "form_check_" . $this->name . "('" . $tableName . "', '" . $fieldName . "'," . ($isUnique ? "true" : "false") . ", " . ($canEmpty ? "true" : "false") . ")";
        return "oninput=\"" . $jsFunction . "\" onreset=\"" . $jsFunction . "\"";
    }

    public function getOnload($tableName, $fieldName, $isUnique, $canEmpty)
    {
        $jsFunction = "form_check_" . $this->name . "('" . $tableName . "', '" . $fieldName . "'," . ($isUnique ? "true" : "false") . ", " . ($canEmpty ? "true" : "false") . ")";
        return "<script type=\"text/javascript\">$(document).ready(function() { " . $jsFunction . "; });</script>";
    }

    public function addComponent($component)
    {
        $this->components[] = $component;
        $component->setForm($this);
    }

    public function setSettings($action, $method="POST")
    {
        $this->action = $action;
        $this->method = $method;
    }

    public function setDataId($id)
    {
        $this->dataId = $id;
    }

    public function display()
    {
        $result = array();
        $result[] = $this->getCheckScript();
        $result[] = "<form class=\"form-horizontal\" action=\"" . $this->action . "\" method=\"" . $this->method . "\" id=\"" . $this->getName() . "\">";
        $result[] = "<input type=\"hidden\" id=\"" . $this->getName() . "_dataid" . "\" name=\"" . $this->getName() . "_dataid" . "\" value=\"" . ($this->dataId >= 0 ? $this->dataId : "") . "\" />";
        foreach ($this->components as $component)
        {
            $result[] = $component->display();
        }
        $result[] = "</form>";
        return implode("\n", $result);
    }

    private function getCheckScript()
    {
        $result = array();

        // Script tag
        $result[] = "<script type=\"text/javascript\">";

        // Function to check the content of a field
        $result[] = "function form_check_" . $this->name . "(tablename, fieldname, isunique, canempty) {";

        // Gather field, div and value
        $result[] = "var field = $(\"#" . $this->getName() . "_\" + fieldname);";
        $result[] = "var div = $(\"#" . $this->getName() . "_\" + fieldname + \"_div\");";
        $result[] = "var current_value = field.val();";

        // Gather current ID
        $result[] = "var current_id = $(\"#" . $this->getName() . "_dataid\").val();";

        // Clear marks
        $result[] = "div.removeClass(\"has-success\");";
        $result[] = "div.removeClass(\"has-error\");";

        // Initialize validation variable
        $result[] = "var isSuccess = true;";

        // Check for emptiness
        $result[] = "if (!canempty) {";

        // If empty
        $result[] = "if (current_value == \"\") {";
        $result[] = "isSuccess = false;";
        $result[] = "}";

        // End of emptiness check
        $result[] = "}";

        // Check for uniqueness
        $result[] = "if (isunique) {";

        // Send AJAX request, value in POST
        $result[] = "$.post('/" . $this->ajaxController . "/ajaxunique', {\"value\":current_value, \"table\":tablename, \"field\":fieldname, \"currentid\":current_id}, function(data) {";

        // If uniqueness not OK
        $result[] = "if (data == 0) {";
        $result[] = "isSuccess = false;";
        $result[] = "}";

        // Apply new state
        $result[] = "if (isSuccess) {";
        $result[] = "div.addClass(\"has-success\");";
        $result[] = "} else {";
        $result[] = "div.addClass(\"has-error\");";
        $result[] = "}";

        // Update button status
        $result[] = "form_updatebutton_" . $this->name . "();";

        // End of AJAX request
        $result[] = "});";

        // End of uniqueness check
        $result[] = "} else {";

        // Apply new state
        $result[] = "if (isSuccess) {";
        $result[] = "div.addClass(\"has-success\");";
        $result[] = "} else {";
        $result[] = "div.addClass(\"has-error\");";
        $result[] = "}";

        // Update button status
        $result[] = "form_updatebutton_" . $this->name . "();";

        $result[] = "}";

        // End of form_check_XXX
        $result[] = "}";

        // Function for updating button status
        $result[] = "function form_updatebutton_" . $this->name . "() {";

        // Get number of "has-error" things in the form
        $result[] = "var count = $(\"#" . $this->getName() . " .has-error\").length;";

        // Get submit button
        $result[] = "var submitbtn = $(\"#" . $this->getName() . "_submit\");";

        // If number > 0, disable button
        $result[] = "if (count > 0) {";
        $result[] = "submitbtn.removeClass(\"btn-success\");";
        $result[] = "submitbtn.addClass(\"btn-danger\");";
        $result[] = "submitbtn.attr(\"disabled\",\"disabled\");";
        $result[] = "}";

        // If number == 0, enable button
        $result[] = "else {";
        $result[] = "submitbtn.removeClass(\"btn-danger\");";
        $result[] = "submitbtn.addClass(\"btn-success\");";
        $result[] = "submitbtn.removeAttr(\"disabled\");";
        $result[] = "}";

        // End of form_updatebutton_XXX
        $result[] = "}";

        // End of script
        $result[] = "</script>";

        return implode("\n", $result);
    }
}

abstract class ILARIA_ModuleFormbuilderComponent
{
    private $name = '';
    private $form = NULL;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public function display()
    {
        $result = array();
        $result[] = "<div class=\"form-group\" id=\"" . $this->getName() . "_div\">";
        $result[] = $this->displayComponent();
        $result[] = "</div>";
        return implode("\n", $result);
    }

    protected function getName()
    {
        return $this->form->getName() . "_" . $this->name;
    }

    protected function getFieldValue()
    {
        return $this->form->getFieldValue($this->name);
    }

    protected function getOnKeyPress($tableName, $isUnique, $canEmpty)
    {
        return $this->form->getOnkeypress($tableName, $this->name, $isUnique, $canEmpty);
    }

    protected function getOnLoad($tableName, $isUnique, $canEmpty)
    {
        return $this->form->getOnload($tableName, $this->name, $isUnique, $canEmpty);
    }

    abstract protected function displayComponent();
}

abstract class ILARIA_ModuleFormbuilderField extends ILARIA_ModuleFormbuilderComponent
{
    private $label = '';
    private $widthLabel = 0;
    private $widthField = 0;

    public function __construct($name, $label, $widthLabel, $widthField)
    {
        parent::__construct($name);
        $this->label = $label;
        $this->widthLabel = $widthLabel;
        $this->widthField = $widthField;
    }

    protected function displayComponent()
    {
        $result = array();
        $result[] = "<label class=\"control-label col-md-" . $this->widthLabel . "\" for=\"" . $this->getName() . "\">" . $this->label . "</label>";
        $result[] = "<div class=\"col-md-" . $this->widthField . "\">";
        $result[] = $this->displayField();
        $result[] = "</div>";
        return implode("\n", $result);
    }

    abstract protected function displayField();
}

class ILARIA_ModuleFormbuilderFieldInput extends ILARIA_ModuleFormbuilderField
{
    private $type = '';
    private $placeholder = '';
    private $isUnique = false;
    private $canEmpty = false;
    private $tableName = '';

    public function __construct($name, $label, $widthLabel, $widthField, $placeholder, $isUnique, $canEmpty, $tableName, $type="text")
    {
        parent::__construct($name, $label, $widthLabel, $widthField);
        $this->placeholder = $placeholder;
        $this->isUnique = $isUnique;
        $this->canEmpty = $canEmpty;
        $this->tableName = $tableName;
        $this->type = $type;
    }

    protected function displayField()
    {
        $result = array();
        $result[] = "<input type=\"" . $this->type . "\" class=\"form-control\" id=\"" . $this->getName() . "\" name=\"" . $this->getName() . "\" placeholder=\"" . $this->placeholder . "\" autocomplete=\"off\" value=\"" . $this->getFieldValue() . "\" " . $this->getOnKeyPress($this->tableName, $this->isUnique, $this->canEmpty) . " />";
        $result[] = $this->getOnLoad($this->tableName, $this->isUnique, $this->canEmpty);
        return implode("\n", $result);
    }
}

class ILARIA_ModuleFormbuilderFieldSelect extends ILARIA_ModuleFormbuilderField
{
    const ELEM_KEY = '5459914bf2cb65787aae0bef980ca1569f046547';
    const ELEM_VAL = '46ffd4422b53bbf7795dd952c424ae4f0a8547cf';

    private $elements = NULL;

    public function __construct($name, $label, $widthLabel, $widthField, $elements)
    {
        parent::__construct($name, $label, $widthLabel, $widthField);
        $this->elements = $elements;
    }

    protected function displayField()
    {
        $result = array();
        $result[] = "<select class=\"form-control\" id=\"" . $this->getName() . "\" name=\"" . $this->getName() . "\">";
        foreach ($this->elements as $elem)
        {
            $result[] = "<option value=\"" . $elem[self::ELEM_KEY] . "\"" . ($elem[self::ELEM_KEY] == $this->getFieldValue() ? " selected=\"selected\"" : "") . ">" . $elem[self::ELEM_VAL] . "</option>";
        }
        $result[] = "</select>";
        return implode("\n", $result);
    }
}

class ILARIA_ModuleFormbuilderFieldSearch extends ILARIA_ModuleFormbuilderField
{
    private $placeholder = '';
    private $searchController = '';
    private $searchAction = '';

    public function __construct($name, $label, $widthLabel, $widthField, $placeholder, $searchController, $searchAction)
    {
        parent::__construct($name, $label, $widthLabel, $widthField);
        $this->placeholder = $placeholder;
        $this->searchController = $searchController;
        $this->searchAction = $searchAction;
    }

    protected function displayField()
    {
        $result = array();
        $result[] = $this->buildScript();
        $result[] = "<table>";
        $result[] = "<tr><td colspan=\"2\">";
        $result[] = "<input type=\"hidden\" id=\"" . $this->getName() . "_id\" name=\"" . $this->getName() . "_id\" value=\"" . (is_array($this->getFieldValue()) ? $this->getFieldValue()['id'] : "") . "\" />";
        $result[] = "<input type=\"text\" class=\"form-control\" disabled=\"disabled\" id=\"" . $this->getName() . "_val\" name=\"" . $this->getName() . "_val\" value=\"" . (is_array($this->getFieldValue()) ? $this->getFieldValue()['val'] : "") . "\" />";
        $result[] = "</td></tr><tr><td>";
        $result[] = "<input type=\"text\" class=\"form-control\" id=\"" . $this->getName() . "_search\" placeholder=\"" . $this->placeholder . "\" autocomplete=\"off\" />";
        $result[] = "</td><td>";
        $result[] = "<a class=\"btn btn-default\" role=\"button\" onclick=\"form_search_" . $this->getName() . "()\">Search</a>";
        $result[] = "</td></tr>";
        $result[] = "</table>";
        return implode("\n", $result);
    }

    private function buildScript()
    {
        $result = array();
        $result[] = "<script type=\"text/javascript\">";

        // Function for opening search window
        $result[] = "function form_search_" . $this->getName() . "() {";

        // Get search field value
        $result[] = "var searchtext = $(\"#" . $this->getName() . "_search\").val();";

        // Send ajax request
        $result[] = "modal_load_url(\"/" . $this->searchController . "/" . $this->searchAction . "/val=\" + searchtext.replace(\" \",\"_\"));";

        // End of form_search_XXX
        $result[] = "}";

        // Function for setting values
        $result[] = "function form_search_" . $this->getName() . "_set(id,val) {";

        // Set id and val
        $result[] = "$(\"#" . $this->getName() . "_id\").val(id);";
        $result[] = "$(\"#" . $this->getName() . "_val\").val(val);";

        // End of form_search_XXX_set
        $result[] = "}";

        $result[] = "</script>";
        return implode("\n", $result);
    }
}

class ILARIA_ModuleFormbuilderFieldArea extends ILARIA_ModuleFormbuilderField
{
    private $placeholder = '';
    private $isUnique = false;
    private $canEmpty = false;
    private $tableName = '';
    private $rows = 0;

    public function __construct($name, $label, $widthLabel, $widthField, $placeholder, $isUnique, $canEmpty, $tableName, $rows)
    {
        parent::__construct($name, $label, $widthLabel, $widthField);
        $this->placeholder = $placeholder;
        $this->isUnique = $isUnique;
        $this->canEmpty = $canEmpty;
        $this->tableName = $tableName;
        $this->rows = $rows;
    }

    protected function displayField()
    {
        $result = array();
        $result[] = "<textarea class=\"form-control\" id=\"" . $this->getName() . "\" name=\"" . $this->getName() . "\" placeholder=\"" . $this->placeholder . "\" autocomplete=\"off\" " . $this->getOnKeyPress($this->tableName, $this->isUnique, $this->canEmpty) . ">" . $this->getFieldValue() . "</textarea>";
        return implode("\n", $result);
    }
}

class ILARIA_ModuleFormbuilderButtons extends ILARIA_ModuleFormbuilderComponent
{
    private $labelSubmit = '';
    private $labelCancel = '';
    private $targetCancel = '';
    private $marginLeft = 0;
    private $widthLeft = 0;
    private $widthRight = 0;

    public function __construct($labelSubmit, $labelCancel, $targetCancel, $marginLeft, $widthLeft, $widthRight)
    {
        parent::__construct("submit");
        $this->labelSubmit = $labelSubmit;
        $this->labelCancel = $labelCancel;
        $this->targetCancel = $targetCancel;
        $this->marginLeft = $marginLeft;
        $this->widthLeft = $widthLeft;
        $this->widthRight = $widthRight;
    }

    protected function displayComponent()
    {
        $result = array();
        $result[] = "<div class=\"col-md-offset-" . $this->marginLeft . " col-md-" . $this->widthLeft . "\">";
        $result[] = "<a class=\"btn btn-default\" role=\"button\" href=\"" . $this->targetCancel . "\">" . $this->labelCancel . "</a>";
        $result[] = "</div>";
        $result[] = "<div class=\"col-md-" . $this->widthRight . "\" style=\"text-align:right\">";
        $result[] = "<button type=\"submit\" class=\"btn btn-default\" id=\"" . $this->getName() . "\">" . $this->labelSubmit . "</button>";
        $result[] = "</div>";
        return implode("\n", $result);
    }
}