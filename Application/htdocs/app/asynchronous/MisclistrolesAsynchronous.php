<?php

class MisclistrolesAsynchronous extends ILARIA_ApplicationAsynchronous
{
    protected function getUniqueIdentifier()
    {
        return "misclistroles";
    }

    protected function getWebPath($params)
    {
        return ILARIA_ConfigurationGlobal::buildRequestChain("directaccess", "misclistroles", $params);
    }

    protected function getDisplayStructure()
    {
        return "<table class=\"table\" id=\"" . $this->getContainerId() . "\">"
        . "<tr class=\"insertor\"><td colspan=\"3\"><a class=\"btn btn-danger btn-sm\" href=\"" . ILARIA_ConfigurationGlobal::buildRequestChain("role", "insert", array()) . "\" role=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> insert</a></td></tr>"
            . "<tr id=\"" . $this->getLoadingId() . "\"><td colspan=\"3\" style=\"text-align:center\">" . $this->getLoadingGif() . "</td></tr>"
            . "</table>";
    }

    protected function getDisplayRow()
    {
        return "<tr class=\\\"" . $this->getElementClass() . "\\\">"
            . "<td>:name</td>"
            . "<td><a class=\\\"btn btn-danger btn-xs\\\" href=\\\"" . ILARIA_ConfigurationGlobal::buildRequestChain("role", "update", array('id' => ':id')) . "\\\" role=\\\"button\\\"><span class=\\\"glyphicon glyphicon-pencil\\\" aria-hidden=\\\"true\\\"></span> update</a></td>"
            . "<td><a class=\\\"btn btn-danger btn-xs\\\" href=\\\"#\\\" role=\\\"button\\\" " . ILARIA_ApplicationAsynchronous::getModalOnClickShow(ILARIA_ConfigurationGlobal::buildRequestChain('role', 'delete', array('id' => ':id')), true) . "><span class=\\\"glyphicon glyphicon-trash\\\" aria-hidden=\\\"true\\\"></span> delete</a></td>"
            . "</tr>";
    }

    protected function getDisplayError()
    {
        return "<tr>"
        . "<td colspan=\\\"3\\\" style=\\\"text-align:center; font-weight: bold; font-color: #0066ff\\\">:error</td>"
        . "</tr>";
    }

    protected function getDisplayPaginator()
    {
        return "<div>"
        . "<h3 class=\"panel-title\">Person's roles</h3></div>"
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
        $content = $model->getListRoles();
        if (!is_array($content))
        {
            throw new ILARIA_CoreError("An error occurred while searching through the roles",
                ILARIA_CoreError::GEN_ASYNC_QUERY_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
        return $content;
    }
}