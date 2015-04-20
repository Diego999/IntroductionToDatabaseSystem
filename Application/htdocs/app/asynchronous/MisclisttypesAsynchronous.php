<?php

class MisclisttypesAsynchronous extends ILARIA_ApplicationAsynchronous
{
    protected function getUniqueIdentifier()
    {
        return "misclisttypes";
    }

    protected function getWebPath($params)
    {
        return ILARIA_ConfigurationGlobal::buildRequestChain("directaccess", "misclisttypes", $params);
    }

    protected function getDisplayStructure()
    {
        return "<table class=\"table\" id=\"" . $this->getContainerId() . "\">"
        . "<tr class=\"insertor\"><td colspan=\"2\"><a class=\"btn btn-danger btn-sm\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("type", "insert", array()) . "\" role=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> insert</a></td></tr>"
        . "<tr id=\"" . $this->getLoadingId() . "\"><td colspan=\"2\" style=\"text-align:center\">" . $this->getLoadingGif() . "</td></tr>"
        . "</table>";
    }

    protected function getDisplayRow()
    {
        return "<tr class=\\\"" . $this->getElementClass() . "\\\">"
        . "<td>:name</td>"
        . "<td>:id</td>"
        . "</tr>";
    }

    protected function getDisplayError()
    {
        return "<tr>"
        . "<td colspan=\\\"2\\\" style=\\\"text-align:center; font-weight: bold; font-color: #0066ff\\\">:error</td>"
        . "</tr>";
    }

    protected function getDisplayPaginator()
    {
        return "<div>"
        . "<h3 class=\"panel-title\">Company's types</h3></div>"
        . "<div style=\"float: right; margin-top:-25px;\">"
        . "<table><tr>"
        . "<td><a id=\"" . $this->getPaginatorText() . "\" style=\"margin-right:20px\">Loading...</a></td>"
        . "<td><nav style=\"visibility:hidden\" id=\"" . $this->getPaginatorId() . "\">"
        . "<ul class=\"pagination\">"
        . "<li id=\"" . $this->getPaginatorPreviousButton() . "\" " . $this->getPaginatorOnClickPrevious() . ">"
        . "<a style=\"cursor:pointer\" aria-label=\"Previous\"><span aria-hidden=\"true\">&laquo;</span></a>"
        . "</li>"
        . "<li id=\"" . $this->getPaginatorNextButton() . "\" " . $this->getPaginatorOnClickNext() . ">"
        . "<a style=\"cursor:pointer\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a>"
        . "</li>"
        . "</ul>"
        . "</nav></td></tr></table></div>";
    }

    protected function getDisplayPaginatorButton()
    {
        return "<li class=\\\"" . $this->getPaginatorButtonClass() . "\\\"><a style=\\\"cursor:pointer\\\" " . $this->getPaginatorOnClickNumeric() . ">:num</a></li>";
    }

    protected function getDisplayPaginatorButtonActive()
    {
        return "<li class=\\\"" . $this->getPaginatorButtonClass() . " active\\\"><a " . $this->getPaginatorOnClickNumeric() . ">:num <span class=\\\"sr-only\\\">(current)</span></a></li>";
    }

    protected function getRawContent($params)
    {
        $model = $this->getModel("miscellaneous");
        $content = $model->getListTypes();
        if (!is_array($content))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the types",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
        return $content;
    }
}